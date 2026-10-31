<?php

namespace App\Chat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Proyecto;
use App\Models\Documento;
use App\Models\Conversacion;

/**
 * ChatOrchestrator — Motor principal del chat v2 (optimizado).
 *
 * Cambios clave vs v1:
 *  - El historial guarda bloques tool_use + tool_result completos
 *    para que Claude recuerde que usó tools en turnos anteriores.
 *  - Loop de hasta 3 iteraciones de tool_use (por si Claude encadena).
 *  - System prompt más enfático sobre usar tools SIEMPRE para acciones.
 *  - sanitizeHistorial respeta content arrays (tool_use blocks).
 */
class ChatOrchestrator
{
    private \App\Services\TokenService $tokenService;
    private const MAX_TOOL_ROUNDS = 3;

    public function __construct()
    {
        $this->tokenService = new \App\Services\TokenService();
    }

    public function handle(string $message, $user, string $sessionId, bool $esNuevo = false): array
    {
        $cacheKey  = "chat_v2_{$user->id}_{$sessionId}";
        $historial = cache()->get($cacheKey, []);

        // Agregar mensaje del usuario
        $historial[] = ['role' => 'user', 'content' => $message];

        $systemPrompt    = $this->buildSystemPrompt($user, $esNuevo);
        $toolDefinitions = ToolRegistry::definitions();
        $frontendActions = [];

        try {
            // --- Loop de tool_use (máximo MAX_TOOL_ROUNDS) ---
            $messages = $this->sanitizeHistorial($historial);
            $finalText = '';

            for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
                $response = $this->callClaude($systemPrompt, $messages, $toolDefinitions, $user);

                $content    = $response->json('content', []);
                $stopReason = $response->json('stop_reason', 'end_turn');

                // Extraer texto de esta respuesta
                $roundText = '';
                foreach ($content as $block) {
                    if ($block['type'] === 'text') {
                        $roundText .= $block['text'];
                    }
                }

                // Si NO hay tool_use, terminamos
                if ($stopReason !== 'tool_use') {
                    $finalText = $roundText;
                    // Guardar respuesta en historial
                    $historial[] = ['role' => 'assistant', 'content' => $roundText];
                    break;
                }

                // --- Hay tool_use: ejecutar tools ---
                $toolResults = [];
                foreach ($content as $block) {
                    if ($block['type'] !== 'tool_use') continue;

                    $tool = ToolRegistry::get($block['name']);
                    if ($tool) {
                        $result = $tool->execute($block['input'] ?? [], $user);

                        $toolResults[] = [
                            'type'        => 'tool_result',
                            'tool_use_id' => $block['id'],
                            'content'     => $result['tool_result'] ?? json_encode($result),
                        ];

                        if (!empty($result['frontend_action'])) {
                            $frontendActions[] = [
                                'action'  => $result['frontend_action'],
                                'payload' => $result['frontend_payload'] ?? [],
                            ];
                        }
                    } else {
                        $toolResults[] = [
                            'type'        => 'tool_result',
                            'tool_use_id' => $block['id'],
                            'content'     => "Errore: tool '{$block['name']}' non trovato.",
                            'is_error'    => true,
                        ];
                    }
                }

                // Guardar en historial: respuesta con tool_use + resultados
                $historial[] = ['role' => 'assistant', 'content' => $content];
                $historial[] = ['role' => 'user',      'content' => $toolResults];

                // Reconstruir messages para la siguiente iteración
                $messages = $this->sanitizeHistorial($historial);
            }

            // Si terminó por max rounds sin texto final, generar resumen
            if (empty($finalText) && !empty($frontendActions)) {
                $finalText = $this->generateSummary($systemPrompt, $messages, $user);
                $historial[] = ['role' => 'assistant', 'content' => $finalText];
            }

            // --- Recortar historial (últimos 30 mensajes) ---
            if (count($historial) > 30) {
                $historial = array_slice($historial, -30);
            }
            cache()->put($cacheKey, $historial, now()->addHours(2));

            // Persistir conversación
            Conversacion::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'user_id'           => $user->id,
                    'historial'         => $historial,
                    'ultimo_mensaje_at' => now(),
                    'titulo'            => Conversacion::where('session_id', $sessionId)->exists()
                        ? Conversacion::where('session_id', $sessionId)->value('titulo')
                        : Conversacion::tituloDesde($message),
                ]
            );

            return [
                'success'    => true,
                'message'    => $finalText,
                'actions'    => $frontendActions,
                'session_id' => $sessionId,
            ];

        } catch (\Exception $e) {
            Log::error('ChatOrchestrator error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [
                'success'    => false,
                'message'    => 'Il servizio AI è temporaneamente sovraccarico. Riprova tra qualche secondo.',
                'actions'    => [],
                'session_id' => $sessionId,
            ];
        }
    }

    // ─── API call ────────────────────────────────────────

    private function callClaude(string $system, array $messages, array $tools, $user): \Illuminate\Http\Client\Response
    {
        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(30)
        ->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 1500,
            'system'     => $system,
            'messages'   => $messages,
            'tools'      => $tools,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->status() . ' — ' . $response->body());
        }

        $usage = $response->json('usage');
        if ($usage) {
            $this->tokenService->registrar(
                $user, 'chat_v2',
                $usage['input_tokens'] ?? 0,
                $usage['output_tokens'] ?? 0,
                'claude-haiku-4-5-20251001'
            );
        }

        return $response;
    }

    /**
     * Generar resumen cuando el loop termina sin texto final.
     */
    private function generateSummary(string $system, array $messages, $user): string
    {
        $response = $this->callClaude(
            $system . "\n\nRispondi brevemente confermando l'azione eseguita. Non usare tool.",
            $messages,
            [], // sin tools para forzar texto
            $user
        );

        $text = '';
        foreach ($response->json('content', []) as $block) {
            if ($block['type'] === 'text') $text .= $block['text'];
        }
        return $text ?: 'Fatto!';
    }

    // ─── System prompt ───────────────────────────────────

    private function buildSystemPrompt($user, bool $esNuevo = false): string
    {
        $contexto = $this->buildContexto($user);

        $prompt = "Sei Docum24 AI, l'assistente di {$user->name} per la gestione documentale.

REGOLE FONDAMENTALI:
1. Rispondi SEMPRE in italiano.
2. DEVI usare i tool per QUALSIASI azione: creare, cercare, aprire, eliminare, rinominare, cambiare tema, navigare.
3. NON dire mai \"Ho fatto X\" senza aver chiamato il tool corrispondente. Se l'utente chiede un'azione, CHIAMA IL TOOL.
4. Per attivare/disattivare il tema scuro → usa toggle_setting con setting='dark_mode'.
5. Per aprire sezioni (cronologia, impostazioni, profilo, ecc.) → usa navigate_ui.
6. Per cercare documenti → usa search_files.
7. Per creare progetto/categoria/tipologia → usa create_project / create_category / create_typology.
8. Quando l'utente chiede INFORMAZIONI o fa domande generali, rispondi in testo.
9. Non chiedere conferme: esegui subito.

CONTESTO:
{$contexto}";

        if ($esNuevo && !$user->business_type) {
            $prompt .= "\n\nL'utente è nuovo. Dai il benvenuto e chiedi il tipo di attività/business.";
        }

        return $prompt;
    }

    private function buildContexto($user): string
    {
        $proyectos = Proyecto::where('user_id', $user->id)
            ->with(['categorias.tipologias'])
            ->get();

        $totalDocs = Documento::where('user_id', $user->id)->count();

        $ctx = "Utente: {$user->name}\n";

        if (!empty($user->ai_prompt)) {
            $ctx .= "Business: {$user->business_type}\n";
            $ctx .= "Profilo: {$user->ai_prompt}\n";
        }

        $ctx .= "Documenti totali: {$totalDocs}\n\n";

        if ($proyectos->isEmpty()) {
            $ctx .= "Nessun progetto creato.\n";
        } else {
            $ctx .= "PROGETTI:\n";
            foreach ($proyectos as $p) {
                $docCount = Documento::where('proyecto_id', $p->id)->count();
                $ctx .= "- {$p->nombre} (ID:{$p->id}) — {$docCount} doc\n";
                foreach ($p->categorias as $cat) {
                    $ctx .= "  - Cat: {$cat->nombre} (ID:{$cat->id})\n";
                    foreach ($cat->tipologias as $tip) {
                        $ctx .= "    - Tip: {$tip->nombre} (ID:{$tip->id})\n";
                    }
                }
            }
        }

        $ultimos = Documento::where('user_id', $user->id)
            ->with(['proyecto'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        if ($ultimos->isNotEmpty()) {
            $ctx .= "\nULTIMI DOCUMENTI:\n";
            foreach ($ultimos as $doc) {
                $ctx .= "- {$doc->nombre} → {$doc->proyecto?->nombre} ({$doc->created_at->format('d/m/Y')})\n";
            }
        }

        return $ctx;
    }

    // ─── Historial ───────────────────────────────────────

    /**
     * Sanitizar historial preservando bloques tool_use/tool_result.
     *
     * content puede ser:
     *   - string (texto normal)
     *   - array  (bloques tool_use o tool_result)
     *
     * Alternancia: solo fusiona strings con strings del mismo rol.
     * Arrays (tool blocks) se dejan intactos.
     */
    private function sanitizeHistorial(array $historial): array
    {
        $clean = [];
        $lastRole = null;

        foreach ($historial as $msg) {
            $role    = $msg['role'] ?? 'user';
            $content = $msg['content'] ?? '';

            if (!in_array($role, ['user', 'assistant'])) continue;

            // Si mismo rol consecutivo y AMBOS son strings → fusionar
            if ($role === $lastRole && !empty($clean)) {
                $lastContent = $clean[count($clean) - 1]['content'];
                if (is_string($lastContent) && is_string($content)) {
                    $clean[count($clean) - 1]['content'] .= "\n" . $content;
                    continue;
                }
                // Si uno es array (tool blocks), NO fusionar — insertar separador
                // Para mantener alternancia, insertar un msg del otro rol
                $separator = ($role === 'user')
                    ? ['role' => 'assistant', 'content' => 'Ok.']
                    : ['role' => 'user',      'content' => 'Continua.'];
                $clean[] = $separator;
            }

            $clean[] = ['role' => $role, 'content' => $content];
            $lastRole = $role;
        }

        // El primer mensaje debe ser de user
        while (!empty($clean) && $clean[0]['role'] !== 'user') {
            array_shift($clean);
        }

        return $clean;
    }
}

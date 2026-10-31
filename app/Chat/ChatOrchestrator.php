<?php

namespace App\Chat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Proyecto;
use App\Models\Documento;
use App\Models\Conversacion;

/**
 * ChatOrchestrator — Motor principal del chat v2.
 *
 * Flujo:
 *  1. Enviar historial + mensaje a Claude con tools
 *  2. Si Claude usa tools → ejecutar → segunda llamada para resumen
 *  3. Guardar en historial solo texto plano (nunca raw tool blocks)
 */
class ChatOrchestrator
{
    private \App\Services\TokenService $tokenService;

    public function __construct()
    {
        $this->tokenService = new \App\Services\TokenService();
    }

    public function handle(string $message, $user, string $sessionId, bool $esNuevo = false, array $activeContext = []): array
    {
        $cacheKey  = "chat_v2_{$user->id}_{$sessionId}";
        $historial = cache()->get($cacheKey, []);

        // Agregar mensaje del usuario
        $historial[] = ['role' => 'user', 'content' => $message];

        $systemPrompt    = $this->buildSystemPrompt($user, $esNuevo, $activeContext);
        $toolDefinitions = ToolRegistry::definitions();
        $frontendActions = [];

        try {
            $messages = $this->sanitizeHistorial($historial);

            // === Primera llamada a Claude (con tools) ===
            $response = $this->callClaude($systemPrompt, $messages, $toolDefinitions, $user);

            $content    = $response->json('content', []);
            $stopReason = $response->json('stop_reason', 'end_turn');

            // Si Claude NO usó tools → respuesta directa
            if ($stopReason !== 'tool_use') {
                $finalText = $this->extractText($content);
                $historial[] = ['role' => 'assistant', 'content' => $finalText];

                $this->saveHistorial($cacheKey, $historial, $sessionId, $user, $message);

                return [
                    'success'    => true,
                    'message'    => $finalText,
                    'actions'    => [],
                    'session_id' => $sessionId,
                ];
            }

            // === Claude usó tools → ejecutar ===
            $toolResults = $this->executeTools($content, $user, $frontendActions);

            // === Segunda llamada: enviar tool_results para que Claude resuma ===
            // Aquí SÍ enviamos los raw blocks (tool_use + tool_result) pero
            // solo para esta llamada, nunca se guardan en historial.
            $secondMessages = $messages;
            $secondMessages[] = ['role' => 'assistant', 'content' => $content];
            $secondMessages[] = ['role' => 'user',      'content' => $toolResults];

            $secondResponse = $this->callClaude($systemPrompt, $secondMessages, $toolDefinitions, $user);
            $secondContent  = $secondResponse->json('content', []);
            $secondStop     = $secondResponse->json('stop_reason', 'end_turn');

            // Si la segunda respuesta TAMBIÉN usa tools → ejecutar
            if ($secondStop === 'tool_use') {
                $moreToolResults = $this->executeTools($secondContent, $user, $frontendActions);

                $thirdMessages = $secondMessages;
                $thirdMessages[] = ['role' => 'assistant', 'content' => $secondContent];
                $thirdMessages[] = ['role' => 'user',      'content' => $moreToolResults];

                $thirdResponse = $this->callClaude(
                    $systemPrompt . "\n\nRispondi brevemente confermando le azioni eseguite.",
                    $thirdMessages,
                    [], // sin tools para forzar texto
                    $user
                );
                $finalText = $this->extractText($thirdResponse->json('content', []));
            } else {
                $finalText = $this->extractText($secondContent);
            }

            if (empty($finalText)) {
                $finalText = 'Fatto!';
            }

            // === Guardar en historial solo TEXTO ===
            $historial[] = ['role' => 'assistant', 'content' => $finalText];

            $this->saveHistorial($cacheKey, $historial, $sessionId, $user, $message);

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

    // ─── Helpers ─────────────────────────────────────────

    private function extractText(array $content): string
    {
        $parts = [];
        foreach ($content as $block) {
            if (($block['type'] ?? '') === 'text' && !empty($block['text'])) {
                $parts[] = $block['text'];
            }
        }
        return implode("\n", $parts);
    }

    private function executeTools(array $content, $user, array &$frontendActions): array
    {
        $toolResults = [];
        foreach ($content as $block) {
            if (($block['type'] ?? '') !== 'tool_use') continue;

            $tool = ToolRegistry::get($block['name']);
            \Log::info('ChatOrchestrator: executing tool', [
                'tool' => $block['name'],
                'input' => $block['input'] ?? [],
            ]);
            if ($tool) {
                $result = $tool->execute($block['input'] ?? [], $user);
                \Log::info('ChatOrchestrator: tool result', [
                    'tool' => $block['name'],
                    'result' => $result,
                ]);

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
        return $toolResults;
    }

    private function callClaude(string $system, array $messages, array $tools, $user): \Illuminate\Http\Client\Response
    {
        $payload = [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 1500,
            'system'     => $system,
            'messages'   => $messages,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(30)
        ->post('https://api.anthropic.com/v1/messages', $payload);

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

    private function saveHistorial(string $cacheKey, array $historial, string $sessionId, $user, string $message): void
    {
        // Recortar (últimos 20 mensajes)
        if (count($historial) > 20) {
            $historial = array_slice($historial, -20);
        }
        cache()->put($cacheKey, $historial, now()->addHours(2));

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
    }

    // ─── System prompt ───────────────────────────────────

    private function buildSystemPrompt($user, bool $esNuevo = false, array $activeContext = []): string
    {
        $contexto = $this->buildContexto($user);

        $prompt = "Sei Docum24 AI, l'assistente di {$user->name} per la gestione documentale.

REGOLE FONDAMENTALI:
1. Rispondi SEMPRE in italiano.
2. DEVI usare i tool per QUALSIASI azione: creare, cercare, aprire, eliminare, rinominare, cambiare tema, navigare.
3. NON dire mai \"Ho fatto X\" senza aver chiamato il tool corrispondente. Se l'utente chiede un'azione, CHIAMA IL TOOL.
4. Per attivare/disattivare il tema scuro → usa toggle_setting con setting='dark_mode'.
5. Per aprire sezioni (cronologia, impostazioni, profilo, ecc.) → usa navigate_ui. Per chiudere il progetto attivo → section='close_project'. Per aprire/chiudere una categoria nel nav → section='open_category'/'close_category'. Per chiudere l'anteprima file → section='close_file'.
6. Per cercare documenti → usa search_files.
7. Per creare progetto/categoria/tipologia → usa create_project / create_category / create_typology.
8. Quando l'utente chiede INFORMAZIONI o fa domande generali, rispondi in testo.
9. Non chiedere conferme: esegui subito.
10. Sii conciso: dopo un'azione, conferma in una frase breve.

CONTESTO:
{$contexto}";

        // Aggiungere contesto attivo (progetto aperto nel frontend)
        if (!empty($activeContext['project_id'])) {
            $prompt .= "\n\nCONTESTO ATTIVO: L'utente sta visualizzando il progetto '{$activeContext['project_name']}' (ID:{$activeContext['project_id']}). Quando chiede di creare categorie o tipologie senza specificare il progetto, usa QUESTO progetto.";
        }

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
     * Sanitizar historial: solo texto plano, alternancia user/assistant.
     */
    private function sanitizeHistorial(array $historial): array
    {
        $clean = [];
        $lastRole = null;

        foreach ($historial as $msg) {
            $role    = $msg['role'] ?? 'user';
            $content = $msg['content'] ?? '';

            if (!in_array($role, ['user', 'assistant'])) continue;

            // Forzar content a string
            if (!is_string($content)) {
                if (is_array($content)) {
                    $texts = [];
                    foreach ($content as $block) {
                        if (is_array($block) && ($block['type'] ?? '') === 'text') {
                            $texts[] = $block['text'] ?? '';
                        }
                    }
                    $content = implode(' ', array_filter($texts)) ?: 'Ok.';
                } else {
                    $content = (string)$content;
                }
            }

            if (empty(trim($content))) continue;

            // Fusionar consecutivos del mismo rol
            if ($role === $lastRole && !empty($clean)) {
                $clean[count($clean) - 1]['content'] .= "\n" . $content;
                continue;
            }

            $clean[] = ['role' => $role, 'content' => $content];
            $lastRole = $role;
        }

        // Primer mensaje debe ser user
        while (!empty($clean) && $clean[0]['role'] !== 'user') {
            array_shift($clean);
        }

        return $clean;
    }
}

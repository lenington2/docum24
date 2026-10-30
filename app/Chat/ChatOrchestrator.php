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
 * Usa tool_use nativo de Claude en vez de JSON forzado.
 * Flujo:
 *   1. Construir contexto del usuario
 *   2. Enviar mensaje + tools a Claude
 *   3. Si Claude llama tools → ejecutarlos → enviar resultados → Claude responde
 *   4. Retornar mensaje + acciones frontend
 */
class ChatOrchestrator
{
    private \App\Services\TokenService $tokenService;

    public function __construct()
    {
        $this->tokenService = new \App\Services\TokenService();
    }

    /**
     * Procesar un mensaje del usuario.
     */
    public function handle(string $message, $user, string $sessionId, bool $esNuevo = false): array
    {
        // 1. Recuperar historial
        $cacheKey  = "chat_v2_{$user->id}_{$sessionId}";
        $historial = cache()->get($cacheKey, []);
        $historial[] = ['role' => 'user', 'content' => $message];

        // 2. Construir contexto y system prompt
        $systemPrompt = $this->buildSystemPrompt($user, $esNuevo);

        // 3. Obtener definiciones de tools
        $toolDefinitions = ToolRegistry::definitions();

        // 4. Llamar a Claude con tool_use
        try {
            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1500,
                'system'     => $systemPrompt,
                'messages'   => $this->sanitizeHistorial($historial),
                'tools'      => $toolDefinitions,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Claude API error: ' . $response->status());
            }

            // Registrar tokens
            $usage = $response->json('usage');
            if ($usage) {
                $this->tokenService->registrar(
                    $user,
                    'chat_v2',
                    $usage['input_tokens'] ?? 0,
                    $usage['output_tokens'] ?? 0,
                    'claude-haiku-4-5-20251001'
                );
            }

            // 5. Procesar respuesta de Claude
            $content  = $response->json('content', []);
            $stopReason = $response->json('stop_reason', 'end_turn');

            $textParts = [];
            $frontendActions = [];
            $toolResults = [];

            foreach ($content as $block) {
                if ($block['type'] === 'text') {
                    $textParts[] = $block['text'];
                }

                if ($block['type'] === 'tool_use') {
                    $toolName = $block['name'];
                    $toolInput = $block['input'] ?? [];
                    $toolUseId = $block['id'];

                    // Ejecutar el tool
                    $tool = ToolRegistry::get($toolName);
                    if ($tool) {
                        $result = $tool->execute($toolInput, $user);

                        // Guardar resultado para la segunda llamada a Claude
                        $toolResults[] = [
                            'type'       => 'tool_result',
                            'tool_use_id' => $toolUseId,
                            'content'    => $result['tool_result'] ?? json_encode($result),
                        ];

                        // Acumular acciones frontend
                        if (!empty($result['frontend_action'])) {
                            $frontendActions[] = [
                                'action'  => $result['frontend_action'],
                                'payload' => $result['frontend_payload'] ?? [],
                            ];
                        }
                    } else {
                        $toolResults[] = [
                            'type'       => 'tool_result',
                            'tool_use_id' => $toolUseId,
                            'content'    => "Errore: tool '{$toolName}' non trovato.",
                            'is_error'   => true,
                        ];
                    }
                }
            }

            // 6. Si hubo tool_use, hacer segunda llamada para que Claude resuma
            if ($stopReason === 'tool_use' && !empty($toolResults)) {
                $secondMessages = $this->sanitizeHistorial($historial);
                // Agregar la respuesta del asistente con tool_use
                $secondMessages[] = ['role' => 'assistant', 'content' => $content];
                // Agregar los resultados de los tools
                $secondMessages[] = ['role' => 'user', 'content' => $toolResults];

                $secondResponse = Http::withHeaders([
                    'x-api-key'         => config('services.anthropic.key'),
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ])
                ->timeout(30)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model'      => 'claude-haiku-4-5-20251001',
                    'max_tokens' => 1000,
                    'system'     => $systemPrompt,
                    'messages'   => $secondMessages,
                ]);

                if ($secondResponse->successful()) {
                    // Registrar tokens de la segunda llamada
                    $usage2 = $secondResponse->json('usage');
                    if ($usage2) {
                        $this->tokenService->registrar(
                            $user,
                            'chat_v2',
                            $usage2['input_tokens'] ?? 0,
                            $usage2['output_tokens'] ?? 0,
                            'claude-haiku-4-5-20251001'
                        );
                    }

                    $secondContent = $secondResponse->json('content', []);
                    $textParts = []; // Resetear - el resumen final es lo que importa
                    foreach ($secondContent as $block) {
                        if ($block['type'] === 'text') {
                            $textParts[] = $block['text'];
                        }
                    }
                }
            }

            $finalMessage = implode("\n", $textParts);

            // 7. Guardar en historial
            $historial[] = ['role' => 'assistant', 'content' => $finalMessage];
            if (count($historial) > 20) {
                $historial = array_slice($historial, -20);
            }
            cache()->put($cacheKey, $historial, now()->addHours(2));

            // 8. Persistir conversación
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
                'message'    => $finalMessage,
                'actions'    => $frontendActions,
                'session_id' => $sessionId,
            ];

        } catch (\Exception $e) {
            Log::error('ChatOrchestrator error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Il servizio AI è temporaneamente sovraccarico. Riprova tra qualche secondo.',
                'actions' => [],
                'session_id' => $sessionId,
            ];
        }
    }

    /**
     * System prompt limpio — sin reglas de JSON, sin acciones listadas.
     * Claude usa los tools para decidir qué hacer.
     */
    private function buildSystemPrompt($user, bool $esNuevo = false): string
    {
        $contexto = $this->buildContexto($user);

        $prompt = "Sei Docum24 AI, l'assistente intelligente per la gestione documentale di {$user->name}.

REGOLE:
- Rispondi SEMPRE in italiano
- Sii diretto, conciso e utile
- Usa i tool disponibili per eseguire azioni (creare, cercare, aprire, modificare, eliminare)
- Quando l'utente chiede di FARE qualcosa (creare, aprire, cercare, eliminare), usa il tool appropriato IMMEDIATAMENTE senza chiedere conferme
- Quando l'utente chiede INFORMAZIONI o ha domande, rispondi in testo naturale
- Puoi chiamare più tool nella stessa risposta se necessario

CONTESTO:
{$contexto}";

        if ($esNuevo && !$user->business_type) {
            $prompt .= "\n\nONBOARDING:
L'utente è nuovo. Dai il benvenuto e chiedi che tipo di attività/business gestisce, per personalizzare l'esperienza.";
        }

        return $prompt;
    }

    /**
     * Construir contexto del usuario (proyectos, documentos, etc.)
     */
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

        // Últimos 3 documentos
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

    /**
     * Sanitizar historial para la API de Claude.
     * Asegura formato válido y alterna user/assistant.
     */
    private function sanitizeHistorial(array $historial): array
    {
        $clean = [];
        $lastRole = null;

        foreach ($historial as $msg) {
            $role = $msg['role'] ?? 'user';
            $content = $msg['content'] ?? '';

            // Solo user y assistant (no system)
            if (!in_array($role, ['user', 'assistant'])) continue;

            // Asegurar alternancia
            if ($role === $lastRole) {
                // Fusionar con el anterior
                if (!empty($clean)) {
                    $last = &$clean[count($clean) - 1];
                    if (is_string($last['content']) && is_string($content)) {
                        $last['content'] .= "\n" . $content;
                    }
                    continue;
                }
            }

            $clean[] = ['role' => $role, 'content' => $content];
            $lastRole = $role;
        }

        // El primer mensaje debe ser de user
        if (!empty($clean) && $clean[0]['role'] !== 'user') {
            array_shift($clean);
        }

        return $clean;
    }
}

<?php

namespace App\Services;

use App\Models\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatDocumentService
{
    public function chat(array $ids, string $pregunta, array $historial = []): string
    {
        $user = Auth::user();

        // Cargar documentos con texto
        $documentos = Documento::whereIn('id', $ids)
            ->where('user_id', $user->id)
            ->whereNotNull('contenido_texto')
            ->where('contenido_texto', '!=', '')
            ->get();

        if ($documentos->isEmpty()) {
            return 'Nessun documento leggibile trovato.';
        }

        $totalDocs = $documentos->count();

        // Índice explícito de documentos
        $indice = "=== INDICE DOCUMENTI ANALIZZATI ({$totalDocs} totali) ===\n";
        foreach ($documentos as $i => $doc) {
            $indice .= ($i + 1) . ". {$doc->nombre}\n";
        }
        $indice .= "\nQUESTI SONO I SOLI {$totalDocs} DOCUMENTI DISPONIBILI. Non fare riferimento a file o liste menzionati DENTRO il contenuto.\n\n";

        // Contenido de los documentos
        $contenido = "=== CONTENUTO DEI DOCUMENTI ===\n\n";
        foreach ($documentos as $doc) {
            $contenido .= "--- INIZIO: {$doc->nombre} ---\n";
            $contenido .= $doc->contenido_texto . "\n";
            $contenido .= "--- FINE: {$doc->nombre} ---\n\n";
        }

        // Perfil profesional del usuario
        $profiloUtente = '';
        if (!empty($user->ai_prompt)) {
            $profiloUtente = "=== PROFILO PROFESSIONALE ===\n{$user->ai_prompt}\n\n";
        }

        $systemPrompt = "Sei Docum24 AI, l'assistente per la gestione documentale di {$user->name}.

{$profiloUtente}Stai operando in modalità CHAT DOCUMENTI.

REGOLE CRITICHE:
- Stai analizzando ESATTAMENTE {$totalDocs} documenti — non di più, non di meno
- Se ti viene chiesto quanti documenti hai, rispondi sempre {$totalDocs}
- NON contare file elencati DENTRO il contenuto dei documenti (es. indici, elenchi allegati)
- Rispondi SOLO basandoti sul contenuto dei documenti forniti
- Se l'informazione non è presente, dillo chiaramente
- Cita sempre il nome del documento: 'Nel documento X...'
- Rispondi in italiano con tono professionale
- Usa numerazione progressiva: 1. 2. 3. (mai ripetere 1.)
- Usa trattini per liste puntate: - elemento

{$indice}
{$contenido}";

        // Construir mensajes con historial
        $messages = [];
        foreach ($historial as $msg) {
            $messages[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }
        $messages[] = [
            'role'    => 'user',
            'content' => $pregunta,
        ];

        $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 4000,
                'system'     => $systemPrompt,
                'messages'   => $messages,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Errore API Claude: ' . $response->body());
        }

        // Registrar tokens
        $usage = $response->json('usage');
        if ($usage) {
            $tokenService = new \App\Services\TokenService();
            $tokenService->registrar(
                $user,
                'chat_documento',
                $usage['input_tokens']  ?? 0,
                $usage['output_tokens'] ?? 0,
                'claude-haiku-4-5-20251001'
            );
        }

        return $response->json('content.0.text');
    }
}

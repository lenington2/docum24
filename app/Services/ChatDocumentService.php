<?php

namespace App\Services;

use App\Models\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatDocumentService
{
    // Límites de la API de Claude
    const MAX_REQUEST_SIZE_MB = 30;  // 32MB limit, dejamos margen
    const MAX_PDF_PAGES = 100;       // Haiku 4.5 = 200K context = max 100 pages
    const MAX_TOKENS = 4096;

    /**
     * Chat con documentos usando PDF nativo (base64) + fallback texto.
     *
     * @return array ['response' => string, 'meta' => array]
     */
    public function chat(array $ids, string $pregunta, array $historial = []): array
    {
        $user = Auth::user();

        // Cargar TODOS los documentos solicitados (sin filtrar por contenido_texto)
        $documentos = Documento::whereIn('id', $ids)
            ->where('user_id', $user->id)
            ->get();

        if ($documentos->isEmpty()) {
            return [
                'response' => 'Nessun documento trovato.',
                'meta' => ['processed' => [], 'failed' => [], 'warnings' => []]
            ];
        }

        // Clasificar y preparar documentos
        $contentBlocks = [];
        $processed = [];
        $failed = [];
        $warnings = [];
        $totalSizeBytes = 0;
        $totalPages = 0;

        foreach ($documentos as $doc) {
            $result = $this->prepareDocument($doc, $totalSizeBytes, $totalPages);

            if ($result['status'] === 'ok') {
                $contentBlocks = array_merge($contentBlocks, $result['blocks']);
                $processed[] = $doc->nombre;
                $totalSizeBytes += $result['size'];
                $totalPages += $result['pages'] ?? 0;

                if (!empty($result['warning'])) {
                    $warnings[] = $result['warning'];
                }
            } else {
                $failed[] = ['name' => $doc->nombre, 'reason' => $result['reason']];
            }
        }

        if (empty($contentBlocks)) {
            $failedNames = array_map(fn($f) => "- {$f['name']}: {$f['reason']}", $failed);
            return [
                'response' => "Non è stato possibile leggere nessuno dei documenti selezionati.\n\n" . implode("\n", $failedNames),
                'meta' => ['processed' => [], 'failed' => $failed, 'warnings' => []]
            ];
        }

        $totalProcessed = count($processed);

        // Perfil profesional del usuario
        $profiloUtente = '';
        if (!empty($user->ai_prompt)) {
            $profiloUtente = "=== PROFILO PROFESSIONALE ===\n{$user->ai_prompt}\n\n";
        }

        // Índice de documentos procesados
        $indice = "=== DOCUMENTI ANALIZZATI ({$totalProcessed} totali) ===\n";
        foreach ($processed as $i => $name) {
            $indice .= ($i + 1) . ". {$name}\n";
        }

        // Avisar sobre documentos que no se pudieron leer
        $avisoFailed = '';
        if (!empty($failed)) {
            $avisoFailed = "\n\n⚠️ DOCUMENTI NON LEGGIBILI (" . count($failed) . "):\n";
            foreach ($failed as $f) {
                $avisoFailed .= "- {$f['name']}: {$f['reason']}\n";
            }
            $avisoFailed .= "\nSe l'utente chiede di questi documenti, informa che non è stato possibile leggerli.\n";
        }

        $systemPrompt = "Sei Docum24 AI, l'assistente per la gestione documentale di {$user->name}.

{$profiloUtente}Stai operando in modalità CHAT DOCUMENTI.

REGOLE CRITICHE:
- Stai analizzando ESATTAMENTE {$totalProcessed} documenti — non di più, non di meno
- Se ti viene chiesto quanti documenti hai, rispondi sempre {$totalProcessed}
- NON contare file elencati DENTRO il contenuto dei documenti (es. indici, elenchi allegati)
- Rispondi SOLO basandoti sul contenuto dei documenti forniti
- Se l'informazione non è presente, dillo chiaramente
- Cita sempre il nome del documento: 'Nel documento X...'
- Rispondi in italiano con tono professionale

{$indice}{$avisoFailed}";

        // Construir mensajes
        $messages = [];
        foreach ($historial as $msg) {
            $messages[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // El primer mensaje del usuario incluye los documentos como content blocks
        $userContent = $contentBlocks;
        $userContent[] = [
            'type' => 'text',
            'text' => $pregunta,
        ];

        $messages[] = [
            'role'    => 'user',
            'content' => $userContent,
        ];

        Log::info('ChatDocumentService: sending request', [
            'processed' => count($processed),
            'failed' => count($failed),
            'total_size_mb' => round($totalSizeBytes / 1024 / 1024, 2),
            'total_pages' => $totalPages,
        ]);

        $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(120) // más tiempo para PDFs grandes
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => self::MAX_TOKENS,
                'system'     => $systemPrompt,
                'messages'   => $messages,
            ]);

        if (!$response->successful()) {
            $errorBody = $response->body();
            Log::error('ChatDocumentService: API error', ['body' => $errorBody]);

            // Intentar dar un mensaje útil según el error
            if (str_contains($errorBody, 'too many PDF pages')) {
                throw new \Exception('Troppi pagine PDF. Riduci il numero di documenti selezionati.');
            }
            if (str_contains($errorBody, 'request too large') || str_contains($errorBody, 'payload')) {
                throw new \Exception('I documenti selezionati superano il limite di dimensione. Seleziona meno documenti.');
            }
            throw new \Exception('Errore API Claude: ' . $errorBody);
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

        return [
            'response' => $response->json('content.0.text'),
            'meta' => [
                'processed' => $processed,
                'failed' => $failed,
                'warnings' => $warnings,
                'tokens_used' => $usage['input_tokens'] ?? 0,
            ]
        ];
    }

    /**
     * Prepara un documento para enviar a Claude.
     * PDFs → base64 document block (lectura nativa)
     * Otros → texto extraído como text block
     */
    private function prepareDocument(Documento $doc, int $currentTotalSize, int $currentTotalPages): array
    {
        $isPdf = str_contains($doc->mime_type ?? '', 'pdf');

        if ($isPdf) {
            return $this->preparePdf($doc, $currentTotalSize, $currentTotalPages);
        }

        return $this->prepareTextDocument($doc);
    }

    /**
     * Prepara un PDF como document block base64.
     */
    private function preparePdf(Documento $doc, int $currentTotalSize, int $currentTotalPages): array
    {
        $path = $doc->archivo;

        if (!Storage::disk('local')->exists($path)) {
            Log::warning('ChatDocumentService: file not found', ['doc' => $doc->nombre, 'path' => $path]);
            return ['status' => 'error', 'reason' => 'File non trovato nel server'];
        }

        $fileSize = Storage::disk('local')->size($path);
        $base64Size = (int)($fileSize * 1.37); // base64 overhead

        // Verificar que no excedamos el límite total
        $newTotal = $currentTotalSize + $base64Size;
        if ($newTotal > self::MAX_REQUEST_SIZE_MB * 1024 * 1024) {
            return [
                'status' => 'error',
                'reason' => 'Limite dimensione raggiunto (' . round($fileSize / 1024 / 1024, 1) . ' MB)'
            ];
        }

        // Estimar páginas (aprox 50KB por página en PDF promedio)
        $estimatedPages = max(1, (int)ceil($fileSize / 50000));
        if ($currentTotalPages + $estimatedPages > self::MAX_PDF_PAGES) {
            return [
                'status' => 'error',
                'reason' => "Limite pagine raggiunto (~{$estimatedPages} pagine, max " . self::MAX_PDF_PAGES . " totali)"
            ];
        }

        try {
            $content = Storage::disk('local')->get($path);
            $base64 = base64_encode($content);

            return [
                'status' => 'ok',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => "📄 Documento: {$doc->nombre}",
                    ],
                    [
                        'type' => 'document',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => 'application/pdf',
                            'data' => $base64,
                        ],
                    ],
                ],
                'size' => $base64Size,
                'pages' => $estimatedPages,
            ];
        } catch (\Exception $e) {
            Log::error('ChatDocumentService: error reading PDF', [
                'doc' => $doc->nombre,
                'error' => $e->getMessage()
            ]);
            return ['status' => 'error', 'reason' => 'Errore lettura file: ' . $e->getMessage()];
        }
    }

    /**
     * Prepara un documento no-PDF usando texto extraído.
     */
    private function prepareTextDocument(Documento $doc): array
    {
        // Intentar con texto ya extraído
        if (!empty($doc->contenido_texto)) {
            $text = $doc->contenido_texto;
            $size = strlen($text);

            return [
                'status' => 'ok',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => "📄 Documento: {$doc->nombre}\n--- INIZIO CONTENUTO ---\n{$text}\n--- FINE CONTENUTO ---",
                    ],
                ],
                'size' => $size,
                'pages' => 0,
            ];
        }

        // Si no hay texto, intentar re-extraer
        $path = $doc->archivo;
        if (!Storage::disk('local')->exists($path)) {
            return ['status' => 'error', 'reason' => 'File non trovato nel server'];
        }

        try {
            $extractor = new TextExtractorService();
            $text = $extractor->extract($path, $doc->mime_type);

            if (empty($text)) {
                // Para imágenes, intentar enviar como imagen
                if ($this->isImage($doc->mime_type)) {
                    return $this->prepareImage($doc);
                }
                return ['status' => 'error', 'reason' => 'Impossibile estrarre testo (formato non supportato)'];
            }

            // Guardar el texto extraído para futuro uso
            $doc->update(['contenido_texto' => $text]);

            return [
                'status' => 'ok',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => "📄 Documento: {$doc->nombre}\n--- INIZIO CONTENUTO ---\n{$text}\n--- FINE CONTENUTO ---",
                    ],
                ],
                'size' => strlen($text),
                'pages' => 0,
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'reason' => 'Errore estrazione: ' . $e->getMessage()];
        }
    }

    /**
     * Prepara una imagen como image block base64.
     */
    private function prepareImage(Documento $doc): array
    {
        $path = $doc->archivo;

        if (!Storage::disk('local')->exists($path)) {
            return ['status' => 'error', 'reason' => 'File immagine non trovato'];
        }

        $content = Storage::disk('local')->get($path);
        $base64 = base64_encode($content);
        $mediaType = $this->imageMediaType($doc->mime_type);

        if (!$mediaType) {
            return ['status' => 'error', 'reason' => 'Formato immagine non supportato'];
        }

        return [
            'status' => 'ok',
            'blocks' => [
                [
                    'type' => 'text',
                    'text' => "🖼️ Immagine: {$doc->nombre}",
                ],
                [
                    'type' => 'image',
                    'source' => [
                        'type' => 'base64',
                        'media_type' => $mediaType,
                        'data' => $base64,
                    ],
                ],
            ],
            'size' => strlen($base64),
            'pages' => 0,
            'warning' => "L'immagine '{$doc->nombre}' verrà analizzata visivamente.",
        ];
    }

    private function isImage(?string $mimeType): bool
    {
        if (!$mimeType) return false;
        return str_starts_with($mimeType, 'image/');
    }

    private function imageMediaType(?string $mimeType): ?string
    {
        return match (true) {
            str_contains($mimeType, 'jpeg'), str_contains($mimeType, 'jpg') => 'image/jpeg',
            str_contains($mimeType, 'png') => 'image/png',
            str_contains($mimeType, 'gif') => 'image/gif',
            str_contains($mimeType, 'webp') => 'image/webp',
            default => null,
        };
    }
}

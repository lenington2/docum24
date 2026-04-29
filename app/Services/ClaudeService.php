<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClaudeService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model  = 'claude-haiku-4-5-20251001';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
    }

    public function analizarDocumento(string $filePath, string $mimeType): array
    {
        try {
            $fileContent = base64_encode(Storage::disk('local')->get($filePath));

            // Claude soporta PDF e imágenes nativamente
            $mediaType = $this->normalizeMediaType($mimeType);

            $prompt = "Analizza questo documento e rispondi SOLO con un JSON valido, senza markdown, senza backtick, senza spiegazioni extra. Il JSON deve avere esattamente questi campi:
{
  \"categoria\": \"categoria breve (es: Immobiliare, Fiscale, Legale, Medico, Assicurativo, Bancario, Contratti, Altro)\",
  \"tipologia\": \"tipo specifico (es: Contratto di Affitto, Fattura, Ricevuta, Certificato, Atto Notarile, Dichiarazione dei Redditi, Polizza, Altro)\",
  \"data_documento\": \"data in formato YYYY-MM-DD se trovata, altrimenti null\",
  \"data_scadenza\": \"data di scadenza in formato YYYY-MM-DD se presente (es: scadenza polizza, scadenza contratto, data limite pagamento fattura), altrimenti null\",
  \"descrizione\": \"massimo 100 parole in italiano\"
}";

            $content = [];

            // Si es PDF o imagen lo mandamos como archivo
            if (in_array($mediaType, ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                $content[] = [
                    'type'   => $mediaType === 'application/pdf' ? 'document' : 'image',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => $mediaType,
                        'data'       => $fileContent,
                    ],
                ];
            }

            // Siempre añadimos el prompt de texto
            $content[] = ['type' => 'text', 'text' => $prompt];

            $response = Http::withHeaders([
                    'x-api-key'         => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                    'anthropic-beta'    => 'pdfs-2024-09-25',
                ])
                ->timeout(60)
                ->post($this->apiUrl, [
                    'model'      => $this->model,
                    'max_tokens' => 500,
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => $content,
                        ]
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Claude API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->resultadoFallback();
            }

            $text = $response->json('content.0.text');
            $text = trim(preg_replace('/```json|```/i', '', $text));
            $data = json_decode($text, true);

            if (!$data || !isset($data['categoria'])) {
                Log::warning('Claude JSON inválido', ['text' => $text]);
                return $this->resultadoFallback();
            }

            // Registrar tokens
            $usage = $response->json('usage');
            if ($usage && \Illuminate\Support\Facades\Auth::check()) {
                $tokenService = new \App\Services\TokenService();
                $tokenService->registrar(
                    \Illuminate\Support\Facades\Auth::user(),
                    'analisi_upload',
                    $usage['input_tokens']  ?? 0,
                    $usage['output_tokens'] ?? 0,
                    $this->model
                );
            }

            return [
                'success'        => true,
                'categoria'      => $data['categoria']      ?? 'Senza Categoria',
                'tipologia'      => $data['tipologia']      ?? 'Altro',
                'data_documento' => $data['data_documento'] ?? null,
                'data_scadenza'  => $data['data_scadenza']  ?? null,
                'descrizione'    => $data['descrizione']    ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('ClaudeService error', ['message' => $e->getMessage()]);
            return $this->resultadoFallback();
        }
    }

    private function normalizeMediaType(string $mimeType): string
    {
        $map = [
            'application/pdf'    => 'application/pdf',
            'image/jpeg'         => 'image/jpeg',
            'image/jpg'          => 'image/jpeg',
            'image/png'          => 'image/png',
            'image/gif'          => 'image/gif',
            'image/webp'         => 'image/webp',
        ];
        return $map[$mimeType] ?? 'application/pdf';
    }

    private function resultadoFallback(): array
    {
        return [
            'success'        => false,
            'categoria'      => 'Senza Categoria',
            'tipologia'      => 'Altro',
            'data_documento' => null,
            'data_scadenza' => null,
            'descrizione'    => null,
        ];
    }
}

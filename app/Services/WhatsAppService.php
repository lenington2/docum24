<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $phoneId;
    private string $token;
    private string $apiVersion;
    private string $baseUrl;

    public function __construct()
    {
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->token = config('services.whatsapp.token');
        $this->apiVersion = config('services.whatsapp.api_version', 'v21.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneId}";
    }

    /**
     * Enviar mensaje de texto simple
     */
    public function sendText(string $to, string $message): array
    {
        $to = $this->formatPhone($to);

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        Log::info('WhatsApp text sent', ['to' => $to, 'status' => $response->status()]);

        return $response->json();
    }

    /**
     * Enviar documento por URL
     */
    public function sendDocument(string $to, string $url, string $filename, string $caption = ''): array
    {
        $to = $this->formatPhone($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'document',
            'document' => [
                'link' => $url,
                'filename' => $filename,
            ],
        ];

        if ($caption) {
            $payload['document']['caption'] = $caption;
        }

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", $payload);

        Log::info('WhatsApp document sent', [
            'to' => $to,
            'filename' => $filename,
            'status' => $response->status(),
        ]);

        return $response->json();
    }

    /**
     * Enviar imagen por URL
     */
    public function sendImage(string $to, string $url, string $caption = ''): array
    {
        $to = $this->formatPhone($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'image',
            'image' => ['link' => $url],
        ];

        if ($caption) {
            $payload['image']['caption'] = $caption;
        }

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", $payload);

        return $response->json();
    }

    /**
     * Enviar plantilla (para mensajes iniciales)
     */
    public function sendTemplate(string $to, string $templateName, string $languageCode = 'it', array $components = []): array
    {
        $to = $this->formatPhone($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", $payload);

        return $response->json();
    }

    /**
     * Subir media a Meta (para archivos locales)
     */
    public function uploadMedia(string $filePath, string $mimeType): ?string
    {
        $response = Http::withToken($this->token)
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post("{$this->baseUrl}/media", [
                'messaging_product' => 'whatsapp',
                'type' => $mimeType,
            ]);

        return $response->json('id');
    }

    /**
     * Enviar documento por media_id (subido previamente)
     */
    public function sendDocumentByMediaId(string $to, string $mediaId, string $filename, string $caption = ''): array
    {
        $to = $this->formatPhone($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'document',
            'document' => [
                'id' => $mediaId,
                'filename' => $filename,
            ],
        ];

        if ($caption) {
            $payload['document']['caption'] = $caption;
        }

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/messages", $payload);

        return $response->json();
    }

    /**
     * Formatear número de teléfono (quitar +, espacios, guiones)
     */
    private function formatPhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}

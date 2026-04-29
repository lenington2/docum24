<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSearchService
{
    public function fetchUrl(string $url): ?string
    {
        try {
           $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Docum24/1.0)',
                ])
                ->timeout(10)
                ->get($url);

            if (!$response->successful()) return null;

            $html = $response->body();
            return $this->extractText($html);

        } catch (\Exception $e) {
            Log::warning("DeepSearch fetch error: {$url}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function extractText(string $html): string
    {
        // Eliminar scripts, styles, comentarios
        $html = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $html);
        $html = preg_replace('/<!--.*?-->/si', '', $html);

        // Convertir a texto
        $text = strip_tags($html);

        // Limpiar espacios
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Limitar a 3000 chars por URL para no explotar el context
        return mb_substr($text, 0, 3000);
    }
}

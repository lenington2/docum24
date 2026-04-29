<?php

namespace App\Services;

use App\Models\Proyecto;
use Illuminate\Support\Facades\Http;

class ReporteService
{
    public function generarContenido(int $userId, ?int $proyectoId, string $instrucciones): string
    {
        $query = Proyecto::with([
            'categorias.tipologias',
            'documentos' => fn($q) => $q->whereNotNull('descripcion')
        ])->where('user_id', $userId);

        if ($proyectoId) {
            $query->where('id', $proyectoId);
        }

        $proyectos   = $query->get();
        $user        = \App\Models\User::find($userId);
        $businessType = $user->business_type ?? 'generale';

        $empresa = \App\Models\Empresa::where('user_id', $userId)->first();
        $datosEmpresa = '';
        if ($empresa) {
            $datosEmpresa = "\nDATA AZIENDA:\n";
            if ($empresa->nombre)    $datosEmpresa .= "Nome: {$empresa->nombre}\n";
            if ($empresa->piva)      $datosEmpresa .= "P.IVA: {$empresa->piva}\n";
            if ($empresa->direccion) $datosEmpresa .= "Indirizzo: {$empresa->direccion}\n";
            if ($empresa->telefono)  $datosEmpresa .= "Tel: {$empresa->telefono}\n";
            if ($empresa->email)     $datosEmpresa .= "Email: {$empresa->email}\n";
            if ($empresa->website)   $datosEmpresa .= "Web: {$empresa->website}\n";
            if ($empresa->descripcion) $datosEmpresa .= "Descrizione: {$empresa->descripcion}\n";
            $datosEmpresa .= "\n";
        }

        $contexto = "SETTORE: {$businessType}\n{$datosEmpresa}\n";

        foreach ($proyectos as $proyecto) {
            $contexto .= "## PROGETTO: {$proyecto->nombre}\n";
            $contexto .= "Totale documenti: " . $proyecto->documentos->count() . "\n\n";

            foreach ($proyecto->categorias as $cat) {
                $contexto .= "### Categoria: {$cat->nombre}\n";
                $docs = $proyecto->documentos->where('categoria_id', $cat->id);
                foreach ($docs as $doc) {
                    $desc = $doc->descripcion ? ": {$doc->descripcion}" : '';
                    $tip  = $doc->tipologia->nombre ?? 'N/D';
                    $contexto .= "- [{$tip}] {$doc->nombre}{$desc}\n";
                }
                $contexto .= "\n";
            }
        }

        $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(90)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 4000,
                'system'     => "Sei un esperto analista documentale. Genera report professionali in italiano basandoti SOLO sui dati forniti. Usa Markdown: titoli ##, tabelle |col|col|, liste -, grassetto **testo**. Sii preciso e professionale.",
                'messages'   => [[
                    'role'    => 'user',
                    'content' => "ISTRUZIONI PER IL REPORT:\n{$instrucciones}\n\nDATI DISPONIBILI:\n{$contexto}",
                ]],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Errore Claude API: ' . $response->status());
        }

        return $response->json('content.0.text');
    }
}

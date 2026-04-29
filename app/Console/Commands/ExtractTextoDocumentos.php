<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Documento;
use App\Services\TextExtractorService;

class ExtractTextoDocumentos extends Command
{
    protected $signature   = 'Docum24:extract-texto';
    protected $description = 'Extrae texto de documentos existentes sin contenido_texto';

    public function handle()
    {
        $extractor = new TextExtractorService();
        $docs      = Documento::whereNull('contenido_texto')->get();

        $this->info("Documentos a procesar: {$docs->count()}");

        foreach ($docs as $doc) {
            $texto = $extractor->extract($doc->archivo, $doc->mime_type);
            if ($texto) {
                $doc->update(['contenido_texto' => $texto]);
                $this->info("✓ {$doc->nombre}");
            } else {
                $this->warn("✗ {$doc->nombre} (tipo no soportado o error)");
            }
        }

        $this->info('Completato!');
    }
}

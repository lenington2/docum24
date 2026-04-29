<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;

class ReporteGeneratorService
{
    public function generarPDF(string $markdown, string $titulo): string
    {
        $html    = $this->toHtml($markdown, $titulo);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Cambiado a true para permitir estilos complejos

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $path = storage_path('app/temp/rep_' . uniqid() . '.pdf');
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, $dompdf->output());
        return $path;
    }

    private function limpiarEmojis(string $texto): string
    {
        $reemplazos = [
            '✅' => '[OK]',
            '❌' => '[NO]',
            '⚠️' => '[!]',
            '🔴' => '[ALTO]',
            '🟠' => '[MEDIO]',
            '🟡' => '[ATENCIÓN]',
            '🟢' => '[OK]',
            '📄' => '',
            '📁' => '',
            '📋' => '',
            '🔍' => '',
            '💼' => '',
            '⚡' => '',
            '🎯' => '',
        ];
        return str_replace(array_keys($reemplazos), array_values($reemplazos), $texto);
    }

    private function toHtml(string $md, string $titulo): string
    {
        $userId  = Auth::id();
        $empresa = \App\Models\Empresa::where('user_id', $userId)->first();

        // 1. Limpieza de metadatos del Markdown para evitar duplicados en el cuerpo
        $lineasAExcluir = [
            'REPORT DOCUMENTALE',
            'Azienda:',
            'Indirizzo:',
            'Telefono:',
            'Settore:',
            'Totale Documenti:',
            'Generato il'
        ];

        $mdFiltrado = collect(explode("\n", $md))
            ->reject(fn($linea) => str_contains($linea, '---')) // Quitar separadores manuales
            ->reject(fn($linea) => \Illuminate\Support\Str::contains($linea, $lineasAExcluir))
            ->implode("\n");

        // 2. Preparación de Logo y Header
        $nombreEmpresa = $empresa?->nombre ?? 'Docum24 AI';
        $logoHtml = '';
        if ($empresa?->logo) {
            $logoPath = storage_path('app/public/' . $empresa->logo);
            if (file_exists($logoPath)) {
                $mime = mime_content_type($logoPath);
                $b64 = base64_encode(file_get_contents($logoPath));
                $logoHtml = "<img src='data:{$mime};base64,{$b64}' class='logo'>";
            }
        }

        $date = now()->format('d/m/Y H:i');

        $mdFiltrado = $this->limpiarEmojis($mdFiltrado);
        $mdFiltrado = mb_convert_encoding($mdFiltrado, 'UTF-8', 'UTF-8');

        // 3. Conversión de Markdown a HTML (Simplificada y robusta)
        $mdFiltrado = mb_convert_encoding($mdFiltrado, 'UTF-8', 'UTF-8');
        $htmlBody = htmlspecialchars($mdFiltrado, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Tablas con estilo profesional
        $htmlBody = preg_replace_callback('/(\|.+\|\n)+/m', function ($m) {
            $rows = array_filter(explode("\n", trim($m[0])));
            $out = '<table class="content-table"><thead>';
            $first = true;
            foreach ($rows as $row) {
                if (preg_match('/^\|[-| ]+\|$/', trim($row))) {
                    $out .= '</thead><tbody>';
                    continue;
                }
                $cols = array_map('trim', explode('|', trim($row, '|')));
                $tag = $first ? 'th' : 'td';
                $out .= '<tr>' . implode('', array_map(fn($c) => "<{$tag}>{$c}</{$tag}>", $cols)) . '</tr>';
                $first = false;
            }
            return $out . '</tbody></table>';
        }, $htmlBody);

        $htmlBody = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $htmlBody);
        $htmlBody = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $htmlBody);
        $htmlBody = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $htmlBody);
        $htmlBody = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $htmlBody);
        $htmlBody = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $htmlBody);
        $htmlBody = preg_replace('/^- (.+)$/m', '<li>$1</li>', $htmlBody);
        $htmlBody = preg_replace('/(<br>\s*){3,}/', '<br><br>', $htmlBody);

        return "
<html>
<head>
<style>
    @page { margin: 120px 50px 80px 50px; }
    header {
        position: fixed; top: -90px; left: 0px; right: 0px; height: 80px;
        border-bottom: 2px solid #333; padding-bottom: 10px;
    }
    footer {
        position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px;
        text-align: center; font-size: 9pt; color: #777; border-top: 1px solid #eee;
    }
    body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; line-height: 1.5; }

    .logo { max-height: 50px; float: left; }
    .header-info { float: right; text-align: right; font-size: 9pt; }

    h1 { font-size: 16pt; color: #1a1a1a; margin-top: 20px; text-transform: uppercase; }
    h2 { font-size: 13pt; color: #444; border-left: 4px solid #F53003; padding-left: 10px; margin: 15px 0 6px; }
    h3 { font-size: 11pt; color: #333; font-weight: bold; margin: 10px 0 4px; }
    h4 { font-size: 10pt; color: #555; font-weight: bold; margin: 8px 0 3px; }
    li { margin-bottom: 2px; }

    .content-table { width: 100%; border-collapse: collapse; margin: 20px 0; table-layout: auto; }
    .content-table th { background-color: #f2f2f2; color: #333; text-align: left; padding: 8px 10px; border: 1px solid #ddd; font-weight: bold; white-space: nowrap; }
    .content-table td { padding: 6px 10px; border: 1px solid #ddd; vertical-align: top; word-break: break-word; font-size: 9pt; }
    .content-table tr:nth-child(even) { background-color: #fafafa; }

    .page-number:after { content: counter(page); }
</style>
</head>
<body>
    <header>
        " . ($logoHtml ?: "<strong>{$nombreEmpresa}</strong>") . "
        <div class='header-info'>
            <strong>{$titulo}</strong><br>
            Data: {$date}<br>
        </div>
    </header>

    <footer>
        {$nombreEmpresa} - " . ($empresa?->direccion ?? '') . (($empresa?->direccion && $empresa?->telefono) ? ' - ' : '') . ($empresa?->telefono ?? '') . " - Pagina <span class='page-number'></span>
    </footer>

    <main>
        {$htmlBody}
    </main>
</body>
</html>";
    }


   public function generarDocx(string $markdown, string $titulo): string
{
    // Primero generar PDF
    $pdfPath = $this->generarPDF($markdown, $titulo);

    try {
        $cloudConvert = new \CloudConvert\CloudConvert([
            'api_key' => config('services.cloudconvert.key'),
            'sandbox' => false,
        ]);

        $job = (new \CloudConvert\Models\Job())
            ->addTask(
                (new \CloudConvert\Models\Task('import/upload', 'upload-pdf'))
            )
            ->addTask(
                (new \CloudConvert\Models\Task('convert', 'convert-pdf-docx'))
                    ->set('input', 'upload-pdf')
                    ->set('output_format', 'docx')
            )
            ->addTask(
                (new \CloudConvert\Models\Task('export/url', 'export-docx'))
                    ->set('input', 'convert-pdf-docx')
            );

        $cloudConvert->jobs()->create($job);

        // Upload PDF
        $uploadTask = $job->getTasks()->whereName('upload-pdf')[0];
        $cloudConvert->tasks()->upload($uploadTask, fopen($pdfPath, 'r'), basename($pdfPath));

        // Esperar resultado
        $cloudConvert->jobs()->wait($job);

        // Descargar DOCX
        $exportTask = $job->getTasks()->whereName('export-docx')[0];
        $files = $exportTask->getResult()->files;

        if (empty($files)) {
            throw new \RuntimeException('Conversione DOCX fallita.');
        }

        $docxPath = storage_path('app/temp/rep_' . uniqid() . '.docx');
        @mkdir(dirname($docxPath), 0755, true);

        $file = $files[0];
        $docxContent = file_get_contents($file->url);

        if (!$docxContent || strlen($docxContent) < 100) {
            throw new \RuntimeException('Download DOCX fallito.');
        }

        file_put_contents($docxPath, $docxContent);

        @unlink($pdfPath);
        return $docxPath;

    } catch (\Exception $e) {
        @unlink($pdfPath);
        \Illuminate\Support\Facades\Log::error('CloudConvert error', ['error' => $e->getMessage()]);
        throw new \RuntimeException('Conversione DOCX non disponibile: ' . $e->getMessage());
    }
}

}

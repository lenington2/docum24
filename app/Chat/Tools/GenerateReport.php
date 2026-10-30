<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Proyecto;
use App\Models\Documento;

class GenerateReport implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'generate_report',
            'description' => "Genera un report/resoconto di un progetto o di tutti i documenti. Usa questo tool quando l'utente chiede un report, riepilogo o statistiche.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'project_id' => [
                        'type' => 'integer',
                        'description' => 'ID del progetto per il report',
                    ],
                    'project_name' => [
                        'type' => 'string',
                        'description' => 'Nome del progetto (alternativa a project_id)',
                    ],
                    'report_type' => [
                        'type' => 'string',
                        'enum' => ['summary', 'detailed', 'stats'],
                        'description' => 'Tipo di report: summary (breve), detailed (completo), stats (solo numeri)',
                    ],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $proyecto = null;

        if (!empty($input['project_id'])) {
            $proyecto = Proyecto::where('id', $input['project_id'])
                ->where('user_id', $user->id)->first();
        } elseif (!empty($input['project_name'])) {
            $proyecto = Proyecto::where('user_id', $user->id)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($input['project_name']) . '%'])
                ->first();
        }

        if ($proyecto) {
            return $this->projectReport($proyecto, $user);
        }

        // Report general
        return $this->generalReport($user);
    }

    private function projectReport($proyecto, $user): array
    {
        $docs = Documento::where('proyecto_id', $proyecto->id)->get();
        $proyecto->load('categorias.tipologias');

        $totalSize = $docs->sum('file_size');
        $sizeFormatted = $this->formatSize($totalSize);

        $byType = $docs->groupBy(function ($d) {
            return $this->friendlyType($d->mime_type);
        })->map->count();

        $report = "REPORT — Progetto: {$proyecto->nombre}\n";
        $report .= "Documenti: {$docs->count()}\n";
        $report .= "Dimensione totale: {$sizeFormatted}\n";
        $report .= "Categorie: {$proyecto->categorias->count()}\n";

        $tipCount = $proyecto->categorias->sum(fn($c) => $c->tipologias->count());
        $report .= "Tipologie: {$tipCount}\n";

        if ($byType->isNotEmpty()) {
            $report .= "\nPer tipo:\n";
            foreach ($byType as $type => $count) {
                $report .= "- {$type}: {$count}\n";
            }
        }

        if ($docs->isNotEmpty()) {
            $latest = $docs->sortByDesc('created_at')->first();
            $oldest = $docs->sortBy('created_at')->first();
            $report .= "\nUltimo caricamento: {$latest->nombre} ({$latest->created_at->format('d/m/Y')})\n";
            $report .= "Primo documento: {$oldest->nombre} ({$oldest->created_at->format('d/m/Y')})\n";
        }

        return [
            'tool_result'      => $report,
            'frontend_action'  => 'show_report',
            'frontend_payload' => [
                'project'  => $proyecto->nombre,
                'stats'    => [
                    'documents'  => $docs->count(),
                    'size'       => $sizeFormatted,
                    'categories' => $proyecto->categorias->count(),
                    'typologies' => $tipCount,
                    'by_type'    => $byType->toArray(),
                ],
            ],
        ];
    }

    private function generalReport($user): array
    {
        $proyectos = Proyecto::where('user_id', $user->id)->get();
        $totalDocs = Documento::where('user_id', $user->id)->count();
        $totalSize = Documento::where('user_id', $user->id)->sum('file_size');

        $report = "REPORT GENERALE\n";
        $report .= "Progetti: {$proyectos->count()}\n";
        $report .= "Documenti totali: {$totalDocs}\n";
        $report .= "Spazio occupato: {$this->formatSize($totalSize)}\n";

        foreach ($proyectos as $p) {
            $dc = Documento::where('proyecto_id', $p->id)->count();
            $report .= "- {$p->nombre}: {$dc} documenti\n";
        }

        return [
            'tool_result' => $report,
        ];
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    private function friendlyType(?string $mime): string
    {
        if (!$mime) return 'Altro';
        if (str_contains($mime, 'pdf')) return 'PDF';
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return 'Word';
        if (str_contains($mime, 'sheet') || str_contains($mime, 'excel')) return 'Excel';
        if (str_contains($mime, 'image')) return 'Immagine';
        if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint')) return 'PowerPoint';
        return 'Altro';
    }
}

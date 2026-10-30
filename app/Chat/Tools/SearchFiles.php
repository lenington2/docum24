<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Documento;
use App\Models\Proyecto;

class SearchFiles implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'search_files',
            'description' => "Cerca documenti/file dell'utente. Usa questo tool quando l'utente chiede di cercare, trovare, mostrare o elencare documenti.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Termine di ricerca (nome o descrizione del documento)',
                    ],
                    'project_name' => [
                        'type' => 'string',
                        'description' => 'Filtrare per nome progetto',
                    ],
                    'project_id' => [
                        'type' => 'integer',
                        'description' => 'Filtrare per ID progetto',
                    ],
                    'file_type' => [
                        'type' => 'string',
                        'enum' => ['pdf', 'excel', 'word', 'image', 'all'],
                        'description' => 'Filtrare per tipo di file',
                    ],
                    'date_filter' => [
                        'type' => 'string',
                        'enum' => ['today', 'yesterday', 'this_week', 'this_month'],
                        'description' => 'Filtrare per data',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Numero massimo di risultati (default 10)',
                    ],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $query = Documento::where('user_id', $user->id);
        $limit = $input['limit'] ?? 10;

        // Filtro por texto
        if (!empty($input['query'])) {
            $term = '%' . $input['query'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'LIKE', $term)
                  ->orWhere('descripcion', 'LIKE', $term);
            });
        }

        // Filtro por proyecto
        if (!empty($input['project_id'])) {
            $query->where('proyecto_id', $input['project_id']);
        } elseif (!empty($input['project_name'])) {
            $proyecto = Proyecto::where('user_id', $user->id)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($input['project_name']) . '%'])
                ->first();
            if ($proyecto) {
                $query->where('proyecto_id', $proyecto->id);
            }
        }

        // Filtro por tipo
        if (!empty($input['file_type']) && $input['file_type'] !== 'all') {
            $mimeMap = [
                'pdf'   => ['application/pdf'],
                'excel' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'word'  => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            ];

            if (isset($mimeMap[$input['file_type']])) {
                $query->whereIn('mime_type', $mimeMap[$input['file_type']]);
            }
        }

        // Filtro por fecha
        if (!empty($input['date_filter'])) {
            switch ($input['date_filter']) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'this_month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
            }
        }

        $docs = $query->with(['proyecto', 'categoria', 'tipologia'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($docs->isEmpty()) {
            return [
                'tool_result' => 'Nessun documento trovato con i criteri specificati.',
            ];
        }

        // Construir resultado para Claude
        $resultText = "Trovati {$docs->count()} documenti:\n";
        $docsPayload = [];

        foreach ($docs as $doc) {
            $resultText .= "- {$doc->nombre} (ID:{$doc->id}) — Progetto: {$doc->proyecto?->nombre} — {$doc->created_at->format('d/m/Y')}\n";
            $docsPayload[] = [
                'id'          => $doc->id,
                'nombre'      => $doc->nombre,
                'descripcion' => $doc->descripcion,
                'mime_type'   => $doc->mime_type,
                'proyecto'    => $doc->proyecto?->nombre,
                'categoria'   => $doc->categoria?->nombre,
                'tipologia'   => $doc->tipologia?->nombre,
                'created_at'  => $doc->created_at->format('d/m/Y H:i'),
                'file_size'   => $doc->file_size,
            ];
        }

        return [
            'tool_result'      => $resultText,
            'frontend_action'  => 'show_documents',
            'frontend_payload' => [
                'documents' => $docsPayload,
                'total'     => $docs->count(),
            ],
        ];
    }
}

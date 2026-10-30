<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Proyecto;
use App\Models\Documento;

class ListProjects implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'list_projects',
            'description' => "Elenca tutti i progetti dell'utente con le loro statistiche. Usa questo tool quando l'utente chiede di vedere, elencare o mostrare i suoi progetti.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'include_details' => [
                        'type' => 'boolean',
                        'description' => 'Se true, include anche categorie e tipologie',
                    ],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $proyectos = Proyecto::where('user_id', $user->id)
            ->with(['categorias.tipologias'])
            ->get();

        if ($proyectos->isEmpty()) {
            return [
                'tool_result' => "Non hai ancora nessun progetto. Vuoi crearne uno?",
            ];
        }

        $resultText = "Hai {$proyectos->count()} progetti:\n";
        $projectsPayload = [];

        foreach ($proyectos as $p) {
            $docCount = Documento::where('proyecto_id', $p->id)->count();
            $resultText .= "- {$p->nombre} (ID:{$p->id}) — {$docCount} documenti, {$p->categorias->count()} categorie\n";

            $pData = [
                'id'          => $p->id,
                'nombre'      => $p->nombre,
                'descripcion' => $p->descripcion,
                'doc_count'   => $docCount,
                'categorias'  => [],
            ];

            if (!empty($input['include_details'])) {
                foreach ($p->categorias as $cat) {
                    $catData = [
                        'id'          => $cat->id,
                        'nombre'      => $cat->nombre,
                        'tipologias'  => [],
                    ];
                    foreach ($cat->tipologias as $tip) {
                        $catData['tipologias'][] = [
                            'id'     => $tip->id,
                            'nombre' => $tip->nombre,
                        ];
                    }
                    $pData['categorias'][] = $catData;
                }
            }

            $projectsPayload[] = $pData;
        }

        return [
            'tool_result'      => $resultText,
            'frontend_action'  => 'show_projects',
            'frontend_payload' => [
                'projects' => $projectsPayload,
                'total'    => $proyectos->count(),
            ],
        ];
    }
}

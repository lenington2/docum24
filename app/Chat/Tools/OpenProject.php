<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Proyecto;
use App\Models\Documento;

class OpenProject implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'open_project',
            'description' => "Apre un progetto specifico, mostrando i suoi documenti. Usa questo tool quando l'utente chiede di aprire, mostrare o andare a un progetto.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'project_id' => [
                        'type' => 'integer',
                        'description' => 'ID del progetto da aprire',
                    ],
                    'project_name' => [
                        'type' => 'string',
                        'description' => 'Nome del progetto (cerca per nome se ID non disponibile)',
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
                ->where('user_id', $user->id)
                ->first();
        } elseif (!empty($input['project_name'])) {
            $proyecto = Proyecto::where('user_id', $user->id)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($input['project_name'])) . '%'])
                ->first();
        }

        if (!$proyecto) {
            $all = Proyecto::where('user_id', $user->id)->pluck('nombre')->implode(', ');
            return [
                'tool_result' => "Progetto non trovato. Progetti disponibili: {$all}",
            ];
        }

        $docCount = Documento::where('proyecto_id', $proyecto->id)->count();

        return [
            'tool_result'      => "Progetto '{$proyecto->nombre}' aperto. Contiene {$docCount} documenti.",
            'frontend_action'  => 'open_project',
            'frontend_payload' => [
                'id'     => $proyecto->id,
                'nombre' => $proyecto->nombre,
            ],
        ];
    }
}

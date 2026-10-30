<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Proyecto;

class CreateProject implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'create_project',
            'description' => "Crea un nuovo progetto per l'utente. Usa questo tool quando l'utente chiede di creare un progetto/cartella.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'Nome del progetto da creare',
                    ],
                    'description' => [
                        'type' => 'string',
                        'description' => 'Descrizione opzionale del progetto',
                    ],
                ],
                'required' => ['name'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $name = trim($input['name']);

        // Verificar duplicado
        $exists = Proyecto::where('user_id', $user->id)
            ->whereRaw('LOWER(nombre) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            return [
                'tool_result' => "Il progetto '{$name}' esiste già.",
            ];
        }

        $proyecto = Proyecto::create([
            'user_id'     => $user->id,
            'nombre'      => $name,
            'descripcion' => $input['description'] ?? null,
        ]);

        return [
            'tool_result'      => "Progetto '{$name}' creato con successo (ID: {$proyecto->id}).",
            'frontend_action'  => 'project_created',
            'frontend_payload' => [
                'id'          => $proyecto->id,
                'nombre'      => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
            ],
        ];
    }
}

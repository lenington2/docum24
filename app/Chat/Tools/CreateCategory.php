<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Categoria;
use App\Models\Proyecto;

class CreateCategory implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'create_category',
            'description' => "Crea una nuova categoria dentro un progetto. Usa questo tool quando l'utente chiede di creare una categoria.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'Nome della categoria da creare',
                    ],
                    'project_id' => [
                        'type' => 'integer',
                        'description' => "ID del progetto. Se non specificato dall'utente, usa il contesto per trovare il progetto giusto.",
                    ],
                    'project_name' => [
                        'type' => 'string',
                        'description' => 'Nome del progetto (alternativa a project_id, cerca per nome)',
                    ],
                ],
                'required' => ['name'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $name = trim($input['name']);

        // Resolver proyecto
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
            // Si solo tiene un proyecto, usarlo
            $proyectos = Proyecto::where('user_id', $user->id)->get();
            if ($proyectos->count() === 1) {
                $proyecto = $proyectos->first();
            } else {
                $nombres = $proyectos->pluck('nombre')->implode(', ');
                return [
                    'tool_result' => "Non ho trovato il progetto. Progetti disponibili: {$nombres}. Specifica in quale progetto creare la categoria.",
                ];
            }
        }

        // Verificar duplicado
        $exists = Categoria::where('proyecto_id', $proyecto->id)
            ->whereRaw('LOWER(nombre) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            return [
                'tool_result' => "La categoria '{$name}' esiste già nel progetto '{$proyecto->nombre}'.",
            ];
        }

        $categoria = Categoria::create([
            'proyecto_id' => $proyecto->id,
            'nombre'      => $name,
        ]);

        return [
            'tool_result'      => "Categoria '{$name}' creata nel progetto '{$proyecto->nombre}' (ID: {$categoria->id}).",
            'frontend_action'  => 'category_created',
            'frontend_payload' => [
                'id'          => $categoria->id,
                'nombre'      => $categoria->nombre,
                'proyecto_id' => $proyecto->id,
            ],
        ];
    }
}

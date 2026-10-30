<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Tipologia;
use App\Models\Categoria;
use App\Models\Proyecto;

class CreateTypology implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'create_typology',
            'description' => "Crea una nuova tipologia dentro una categoria. Usa questo tool quando l'utente chiede di creare una tipologia/sottocategoria.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'Nome della tipologia da creare',
                    ],
                    'category_id' => [
                        'type' => 'integer',
                        'description' => 'ID della categoria',
                    ],
                    'category_name' => [
                        'type' => 'string',
                        'description' => 'Nome della categoria (alternativa a category_id)',
                    ],
                    'project_name' => [
                        'type' => 'string',
                        'description' => 'Nome del progetto per disambiguare la categoria',
                    ],
                ],
                'required' => ['name'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $name = trim($input['name']);

        // Resolver categoría
        $categoria = null;
        if (!empty($input['category_id'])) {
            $categoria = Categoria::where('id', $input['category_id'])
                ->whereHas('proyecto', fn($q) => $q->where('user_id', $user->id))
                ->first();
        } elseif (!empty($input['category_name'])) {
            $query = Categoria::whereHas('proyecto', fn($q) => $q->where('user_id', $user->id))
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($input['category_name'])) . '%']);

            if (!empty($input['project_name'])) {
                $query->whereHas('proyecto', fn($q) =>
                    $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($input['project_name'])) . '%'])
                );
            }

            $categoria = $query->first();
        }

        if (!$categoria) {
            return [
                'tool_result' => "Non ho trovato la categoria. Specifica in quale categoria creare la tipologia.",
            ];
        }

        $exists = Tipologia::where('categoria_id', $categoria->id)
            ->whereRaw('LOWER(nombre) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            return [
                'tool_result' => "La tipologia '{$name}' esiste già nella categoria '{$categoria->nombre}'.",
            ];
        }

        $tipologia = Tipologia::create([
            'categoria_id' => $categoria->id,
            'nombre'       => $name,
        ]);

        return [
            'tool_result'      => "Tipologia '{$name}' creata nella categoria '{$categoria->nombre}' (ID: {$tipologia->id}).",
            'frontend_action'  => 'typology_created',
            'frontend_payload' => [
                'id'           => $tipologia->id,
                'nombre'       => $tipologia->nombre,
                'categoria_id' => $categoria->id,
            ],
        ];
    }
}

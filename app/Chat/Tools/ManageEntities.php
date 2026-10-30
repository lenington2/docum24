<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Proyecto;
use App\Models\Categoria;
use App\Models\Tipologia;

class ManageEntities implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'manage_entities',
            'description' => "Modifica o elimina progetti, categorie o tipologie. Usa questo tool quando l'utente chiede di rinominare, modificare, eliminare o gestire entità esistenti.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'entity_type' => [
                        'type' => 'string',
                        'enum' => ['project', 'category', 'typology'],
                        'description' => "Tipo di entità da gestire",
                    ],
                    'action' => [
                        'type' => 'string',
                        'enum' => ['rename', 'delete', 'update_description'],
                        'description' => "Azione da eseguire",
                    ],
                    'entity_id' => [
                        'type' => 'integer',
                        'description' => "ID dell'entità",
                    ],
                    'entity_name' => [
                        'type' => 'string',
                        'description' => "Nome dell'entità (per cercarla se non si ha l'ID)",
                    ],
                    'new_name' => [
                        'type' => 'string',
                        'description' => 'Nuovo nome (per rename)',
                    ],
                    'new_description' => [
                        'type' => 'string',
                        'description' => 'Nuova descrizione (per update_description)',
                    ],
                ],
                'required' => ['entity_type', 'action'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $type = $input['entity_type'];
        $action = $input['action'];

        // Resolver entidad
        $entity = $this->findEntity($type, $input, $user);

        if (!$entity) {
            return [
                'tool_result' => "Non ho trovato {$this->typeLabel($type)} specificato/a. Verifica il nome e riprova.",
            ];
        }

        switch ($action) {
            case 'rename':
                if (empty($input['new_name'])) {
                    return ['tool_result' => 'Specifica il nuovo nome.'];
                }
                $oldName = $entity->nombre;
                $entity->nombre = trim($input['new_name']);
                $entity->save();
                return [
                    'tool_result'      => "{$this->typeLabel($type)} rinominato/a da '{$oldName}' a '{$entity->nombre}'.",
                    'frontend_action'  => 'entity_updated',
                    'frontend_payload' => [
                        'type' => $type,
                        'id'   => $entity->id,
                        'nombre' => $entity->nombre,
                    ],
                ];

            case 'delete':
                $name = $entity->nombre;
                $entity->delete();
                return [
                    'tool_result'      => "{$this->typeLabel($type)} '{$name}' eliminato/a con successo.",
                    'frontend_action'  => 'entity_deleted',
                    'frontend_payload' => [
                        'type' => $type,
                        'id'   => $entity->id,
                    ],
                ];

            case 'update_description':
                if ($type !== 'project') {
                    return ['tool_result' => 'Solo i progetti hanno una descrizione.'];
                }
                $entity->descripcion = $input['new_description'] ?? '';
                $entity->save();
                return [
                    'tool_result'      => "Descrizione del progetto '{$entity->nombre}' aggiornata.",
                    'frontend_action'  => 'entity_updated',
                    'frontend_payload' => [
                        'type' => $type,
                        'id'   => $entity->id,
                        'nombre' => $entity->nombre,
                        'descripcion' => $entity->descripcion,
                    ],
                ];

            default:
                return ['tool_result' => "Azione '{$action}' non supportata."];
        }
    }

    private function findEntity(string $type, array $input, $user)
    {
        $id   = $input['entity_id'] ?? null;
        $name = $input['entity_name'] ?? null;

        switch ($type) {
            case 'project':
                if ($id) return Proyecto::where('id', $id)->where('user_id', $user->id)->first();
                if ($name) return Proyecto::where('user_id', $user->id)
                    ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($name) . '%'])->first();
                break;

            case 'category':
                if ($id) return Categoria::where('id', $id)
                    ->whereHas('proyecto', fn($q) => $q->where('user_id', $user->id))->first();
                if ($name) return Categoria::whereHas('proyecto', fn($q) => $q->where('user_id', $user->id))
                    ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($name) . '%'])->first();
                break;

            case 'typology':
                if ($id) return Tipologia::where('id', $id)
                    ->whereHas('categoria.proyecto', fn($q) => $q->where('user_id', $user->id))->first();
                if ($name) return Tipologia::whereHas('categoria.proyecto', fn($q) => $q->where('user_id', $user->id))
                    ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($name) . '%'])->first();
                break;
        }

        return null;
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'project'  => 'Il progetto',
            'category' => 'La categoria',
            'typology' => 'La tipologia',
            default    => "L'entità",
        };
    }
}

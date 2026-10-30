<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Documento;

class OpenFile implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'open_file',
            'description' => "Apre un documento/file specifico nel viewer. Usa questo tool quando l'utente chiede di aprire, visualizzare o mostrare un file specifico.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'file_id' => [
                        'type' => 'integer',
                        'description' => 'ID del documento da aprire',
                    ],
                    'file_name' => [
                        'type' => 'string',
                        'description' => 'Nome del file (cerca per nome se ID non disponibile)',
                    ],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $doc = null;

        if (!empty($input['file_id'])) {
            $doc = Documento::where('id', $input['file_id'])
                ->where('user_id', $user->id)
                ->first();
        } elseif (!empty($input['file_name'])) {
            $doc = Documento::where('user_id', $user->id)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($input['file_name'])) . '%'])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$doc) {
            return [
                'tool_result' => 'Documento non trovato. Prova con un nome diverso o cerca tra i tuoi documenti.',
            ];
        }

        return [
            'tool_result'      => "Apro il documento '{$doc->nombre}'.",
            'frontend_action'  => 'open_file',
            'frontend_payload' => [
                'id'        => $doc->id,
                'nombre'    => $doc->nombre,
                'mime_type' => $doc->mime_type,
            ],
        ];
    }
}

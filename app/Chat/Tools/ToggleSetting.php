<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;

class ToggleSetting implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'toggle_setting',
            'description' => "Cambia un'impostazione dell'interfaccia. Usa questo tool quando l'utente chiede di attivare/disattivare dark mode, cambiare tema o impostazioni visive.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'setting' => [
                        'type' => 'string',
                        'enum' => ['dark_mode', 'sidebar_collapsed'],
                        'description' => "Impostazione da cambiare",
                    ],
                    'value' => [
                        'type' => 'boolean',
                        'description' => 'Valore: true per attivare, false per disattivare. Se non specificato, inverte lo stato attuale.',
                    ],
                ],
                'required' => ['setting'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $setting = $input['setting'];
        $value = $input['value'] ?? null; // null = toggle

        $labels = [
            'dark_mode'         => 'Dark mode',
            'sidebar_collapsed' => 'Sidebar',
        ];

        $label = $labels[$setting] ?? $setting;

        return [
            'tool_result'      => $value === null
                ? "{$label} commutato."
                : "{$label} " . ($value ? 'attivato' : 'disattivato') . ".",
            'frontend_action'  => 'toggle_setting',
            'frontend_payload' => [
                'setting' => $setting,
                'value'   => $value,
            ],
        ];
    }
}

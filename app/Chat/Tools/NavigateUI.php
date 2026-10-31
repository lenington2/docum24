<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;

class NavigateUI implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'navigate_ui',
            'description' => "Apre una sezione o pannello dell'interfaccia. Usa questo tool quando l'utente chiede di aprire, mostrare o andare a una sezione specifica dell'app come cronologia, conversazioni, impostazioni, profilo, supporto, team, scadenze.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'section' => [
                        'type' => 'string',
                        'enum' => [
                            'conversations',
                            'history',
                            'settings',
                            'profile',
                            'company',
                            'support',
                            'team',
                            'deadlines',
                            'report',
                            'new_chat',
                            'close_project',
                        ],
                        'description' => "Sezione da aprire: conversations (conversazioni), history (cronologia/attività), settings (impostazioni), profile (profilo), company (azienda), support (supporto), team (gestione team), deadlines (scadenze), report (report), new_chat (nuova chat), close_project (chiudi progetto attivo e torna alla lista progetti)",
                    ],
                    'filter' => [
                        'type' => 'string',
                        'enum' => ['today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month'],
                        'description' => 'Filtro temporale opzionale (per cronologia, scadenze)',
                    ],
                    'query' => [
                        'type' => 'string',
                        'description' => "Termine di ricerca per cercare conversazioni specifiche (es. 'contratti', 'fatture'). Usa con section=conversations.",
                    ],
                ],
                'required' => ['section'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $section = $input['section'];
        $filter = $input['filter'] ?? null;

        $sectionMap = [
            'conversations' => ['action' => 'open_conversations', 'label' => 'Conversazioni'],
            'history'       => ['action' => 'open_history',       'label' => 'Cronologia attività'],
            'settings'      => ['action' => 'open_settings',      'label' => 'Impostazioni'],
            'profile'       => ['action' => 'open_profile',       'label' => 'Profilo'],
            'company'       => ['action' => 'open_empresa',       'label' => 'Azienda'],
            'support'       => ['action' => 'toggle_support',     'label' => 'Supporto'],
            'team'          => ['action' => 'open_team',          'label' => 'Team'],
            'deadlines'     => ['action' => 'open_scadenze',      'label' => 'Scadenze'],
            'report'        => ['action' => 'open_report',        'label' => 'Report'],
            'new_chat'      => ['action' => 'new_chat',           'label' => 'Nuova chat'],
            'close_project' => ['action' => 'close_project',      'label' => 'Lista progetti'],
        ];

        $info = $sectionMap[$section] ?? null;

        if (!$info) {
            return [
                'tool_result' => "Sezione '{$section}' non riconosciuta.",
            ];
        }

        $result = "Apro {$info['label']}.";
        if (!empty($input['query']) && $section === 'conversations') {
            $result = "Cerco nelle conversazioni: '{$input['query']}'.";
        }
        if ($filter) {
            $filterLabels = [
                'today'      => 'di oggi',
                'yesterday'  => 'di ieri',
                'this_week'  => 'di questa settimana',
                'last_week'  => 'della settimana scorsa',
                'this_month' => 'di questo mese',
                'last_month' => 'del mese scorso',
            ];
            $result = "Apro {$info['label']} {$filterLabels[$filter]}.";
        }

        $payload = [];
        if ($filter) $payload['filter'] = $filter;
        if (!empty($input['query'])) $payload['query'] = $input['query'];

        return [
            'tool_result'      => $result,
            'frontend_action'  => $info['action'],
            'frontend_payload' => $payload,
        ];
    }
}

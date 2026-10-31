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
                            'open_category',
                            'close_category',
                            'close_file',
                        ],
                        'description' => "Sezione da aprire: conversations, history, settings, profile, company, support, team, deadlines, report, new_chat, close_project, open_category, close_category, close_file (chiude l'anteprima del file)",
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
                    'category_id' => [
                        'type' => 'integer',
                        'description' => 'ID della categoria da aprire/chiudere nel nav. Usa con section=open_category o close_category.',
                    ],
                    'category_name' => [
                        'type' => 'string',
                        'description' => 'Nome della categoria (alternativa a category_id). Usa con section=open_category o close_category.',
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
            'close_file'    => ['action' => 'close_file',         'label' => 'Anteprima file'],
        ];

        // Handle open/close category specially
        if (in_array($section, ['open_category', 'close_category'])) {
            $catId = $input['category_id'] ?? null;
            $catName = $input['category_name'] ?? null;

            if (!$catId && $catName) {
                $cat = \App\Models\Categoria::whereHas('proyecto', fn($q) => $q->where('user_id', $user->id))
                    ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($catName)) . '%'])
                    ->first();
                $catId = $cat?->id;
                $catName = $cat?->nombre ?? $catName;
            } elseif ($catId) {
                $cat = \App\Models\Categoria::find($catId);
                $catName = $cat?->nombre ?? "ID:{$catId}";
            }

            if (!$catId) {
                return ['tool_result' => "Categoria non trovata. Specifica nome o ID."];
            }

            $isOpen = $section === 'open_category';
            $label = $isOpen ? 'Aperta' : 'Chiusa';

            return [
                'tool_result' => "{$label} la categoria '{$catName}'.",
                'frontend_action' => 'toggle_category',
                'frontend_payload' => [
                    'category_id' => $catId,
                    'open' => $isOpen,
                ],
            ];
        }

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

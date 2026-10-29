<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Proyecto;
use App\Models\Categoria;
use App\Models\Tipologia;
use Illuminate\Support\Facades\Auth;

/**
 * IntentResolver — Capa determinística antes del AI.
 *
 * Analiza el mensaje del usuario con regex + consultas DB.
 * Si reconoce la intención → devuelve respuesta estructurada.
 * Si no → devuelve null y ChatController llama al AI.
 *
 * CONTRATO DE RESPUESTA (siempre igual):
 * [
 *   'message'  => 'texto italiano para el chat',
 *   'action'   => 'intent_name',
 *   'params'   => [
 *     'docs'      => [...],   // cuando hay documentos
 *     'proyectos' => [...],   // cuando hay proyectos
 *     'target'    => [...],   // entidad objetivo (para editar, mover, etc.)
 *   ],
 * ]
 */
class IntentResolver
{
    private $user;
    private string $message;
    private string $messageLower;

    /**
     * PRE-procesador: intenta resolver ANTES del AI.
     * Retorna array estructurado o null (fall-through al AI).
     */
    public function resolve(string $message, $user): ?array
    {
        $this->user = $user;
        $this->message = $message;
        $this->messageLower = mb_strtolower(trim($message));

        return $this->resolveOpenFile()
            ?? $this->resolveShowFiles()
            ?? $this->resolveOpenProject()
            ?? $this->resolveSearchProject()
            ?? null; // → cae al AI
    }

    /**
     * POST-procesador: enriquece la respuesta del AI con datos reales del DB.
     * El AI interpreta la intención (en cualquier idioma), este método
     * toma el action+params y busca los datos reales.
     *
     * Retorna el array enriquecido, o el mismo array sin cambios si
     * la acción no necesita datos del DB.
     */
    public function resolveFromAI(array $aiResult, $user): array
    {
        $this->user = $user;
        $action = $aiResult['action'] ?? 'nessuna';
        $params = $aiResult['params'] ?? [];

        // ── ABRIR ARCHIVO: AI dice "apri_file" pero sin docs reales ──
        if ($action === 'apri_file' || $action === 'mostrar_documentos') {
            // Si el AI ya devolvió docs con IDs, no hacer nada
            if (!empty($params['docs']) && isset($params['docs'][0]['id'])) {
                return $aiResult;
            }

            // Buscar por nombre que el AI extrajo
            $nombre = $params['nombre'] ?? $params['nome'] ?? $params['name']
                    ?? $params['file'] ?? $params['filename'] ?? null;

            if ($nombre) {
                $docs = $this->searchDocs($nombre, 5);

                if ($docs->isNotEmpty()) {
                    if ($action === 'apri_file' && $docs->count() === 1) {
                        return [
                            'message' => $aiResult['message'],
                            'action'  => 'apri_file',
                            'params'  => ['docs' => $docs->toArray()],
                        ];
                    }
                    return [
                        'message' => $aiResult['message'],
                        'action'  => 'mostrar_documentos',
                        'params'  => ['docs' => $docs->toArray()],
                    ];
                }

                // AI dijo que abriera algo pero no existe
                return [
                    'message' => $aiResult['message'] . "\n\n⚠️ Non ho trovato file con \"{$nombre}\".",
                    'action'  => 'nessuna',
                    'params'  => [],
                ];
            }

            // AI dijo mostrar_documentos pero sin nombre → buscar recientes
            if ($action === 'mostrar_documentos') {
                $filtro = $params['filtro'] ?? $params['filter'] ?? null;
                $query = Documento::with(['proyecto', 'categoria', 'tipologia'])
                    ->where('user_id', $this->user->id);

                // Interpretar filtros del AI
                if ($filtro) {
                    $filtroLower = mb_strtolower($filtro);
                    if (str_contains($filtroLower, 'oggi') || str_contains($filtroLower, 'today')) {
                        $query->whereDate('created_at', today());
                    } elseif (str_contains($filtroLower, 'ieri') || str_contains($filtroLower, 'yesterday')) {
                        $query->whereDate('created_at', today()->subDay());
                    } elseif (str_contains($filtroLower, 'settimana') || str_contains($filtroLower, 'week')) {
                        $query->where('created_at', '>=', now()->subWeek());
                    } elseif (str_contains($filtroLower, 'mese') || str_contains($filtroLower, 'month')) {
                        $query->where('created_at', '>=', now()->subMonth());
                    }
                }

                // Filtro por tipo si el AI lo indicó
                $tipo = $params['tipo'] ?? $params['type'] ?? $params['mime'] ?? null;
                if ($tipo) {
                    $tipoLower = mb_strtolower($tipo);
                    if (str_contains($tipoLower, 'pdf')) {
                        $query->where('mime_type', 'like', '%pdf%');
                    } elseif (str_contains($tipoLower, 'excel') || str_contains($tipoLower, 'xls')) {
                        $query->where(function ($q) {
                            $q->where('mime_type', 'like', '%excel%')
                              ->orWhere('mime_type', 'like', '%spreadsheet%');
                        });
                    } elseif (str_contains($tipoLower, 'image') || str_contains($tipoLower, 'immagin') || str_contains($tipoLower, 'foto')) {
                        $query->where('mime_type', 'like', 'image/%');
                    } elseif (str_contains($tipoLower, 'word') || str_contains($tipoLower, 'doc')) {
                        $query->where(function ($q) {
                            $q->where('mime_type', 'like', '%word%')
                              ->orWhere('mime_type', 'like', '%document%');
                        });
                    }
                }

                $docs = $query->orderBy('created_at', 'desc')
                    ->limit(10)->get()
                    ->map(fn($d) => [
                        'id'          => $d->id,
                        'nombre'      => $d->nombre,
                        'mime_type'   => $d->mime_type,
                        'created_at'  => $d->created_at->format('Y-m-d'),
                        'categoria'   => $d->categoria?->nombre,
                        'tipologia'   => $d->tipologia?->nombre,
                        'proyecto_id' => $d->proyecto_id,
                        'proyecto'    => $d->proyecto?->nombre,
                        'descripcion' => $d->descripcion,
                    ]);

                if ($docs->isNotEmpty()) {
                    return [
                        'message' => $aiResult['message'],
                        'action'  => 'mostrar_documentos',
                        'params'  => ['docs' => $docs->toArray()],
                    ];
                }

                return [
                    'message' => 'Non ho trovato documenti con questi criteri.',
                    'action'  => 'nessuna',
                    'params'  => [],
                ];
            }
        }

        // ── ABRIR PROYECTO: AI dice "apri_progetto" pero sin ID real ──
        if ($action === 'apri_progetto') {
            if (!empty($params['id'])) {
                // Ya tiene ID → verificar que existe
                $p = Proyecto::where('user_id', $this->user->id)
                    ->where('id', $params['id'])->first();
                if ($p) return $aiResult;
            }

            $nombre = $params['nombre'] ?? $params['nome'] ?? $params['name'] ?? null;
            if ($nombre) {
                $proyectos = Proyecto::where('user_id', $this->user->id)
                    ->where('nombre', 'like', "%{$nombre}%")
                    ->limit(5)->get();

                if ($proyectos->count() === 1) {
                    $p = $proyectos->first();
                    return [
                        'message' => $aiResult['message'],
                        'action'  => 'apri_progetto',
                        'params'  => ['id' => $p->id, 'nombre' => $p->nombre],
                    ];
                }
                if ($proyectos->count() > 1) {
                    return [
                        'message' => $aiResult['message'],
                        'action'  => 'mostrar_proyectos',
                        'params'  => ['proyectos' => $proyectos->map(fn($p) => [
                            'id' => $p->id, 'nombre' => $p->nombre,
                        ])->toArray()],
                    ];
                }

                // No encontró proyecto → intentar como archivo (el AI pudo confundir)
                $docs = $this->searchDocs($nombre, 3);
                if ($docs->isNotEmpty()) {
                    if ($docs->count() === 1) {
                        return [
                            'message' => "Apro il file **{$docs->first()['nombre']}**...",
                            'action'  => 'apri_file',
                            'params'  => ['docs' => $docs->toArray()],
                        ];
                    }
                    return [
                        'message' => "Ho trovato {$docs->count()} file:",
                        'action'  => 'mostrar_documentos',
                        'params'  => ['docs' => $docs->toArray()],
                    ];
                }

                return [
                    'message' => $aiResult['message'] . "\n\n⚠️ Non ho trovato né progetto né file con \"{$nombre}\".",
                    'action'  => 'nessuna',
                    'params'  => [],
                ];
            }
        }

        // ── BUSCAR PROYECTOS ──
        if ($action === 'mostrar_proyectos') {
            if (!empty($params['proyectos']) && isset($params['proyectos'][0]['id'])) {
                return $aiResult;
            }

            $nombre = $params['nombre'] ?? $params['nome'] ?? $params['query'] ?? null;
            if ($nombre) {
                $proyectos = Proyecto::where('user_id', $this->user->id)
                    ->where('nombre', 'like', "%{$nombre}%")
                    ->limit(5)->get();

                if ($proyectos->isNotEmpty()) {
                    return [
                        'message' => $aiResult['message'],
                        'action'  => 'mostrar_proyectos',
                        'params'  => ['proyectos' => $proyectos->map(fn($p) => [
                            'id' => $p->id, 'nombre' => $p->nombre,
                        ])->toArray()],
                    ];
                }
            }
        }

        // ── Cualquier otra acción → pasar sin cambios ──
        return $aiResult;
    }

    // ═══════════════════════════════════════════════════════════
    // ABRIR ARCHIVO ESPECÍFICO
    // "apri il file X", "aprimi X", "mostrami il file X"
    // ═══════════════════════════════════════════════════════════
    private function resolveOpenFile(): ?array
    {
        // Detectar intención de ABRIR (no listar)
        if (!preg_match('/\b(apri|apre|aprimi|aprilo|apriamo|aprire|mostrami|fammi vedere|voglio vedere|abrelo|abrilo|abre|abrir|abreme|ábrelo|ábrelo)\b/i', $this->message)) {
            return null;
        }

        // Excluir si es proyecto
        if (preg_match('/\bprogett[oi]\b/i', $this->message)) {
            return null;
        }

        // Excluir si pide listar múltiples ("mostrami i file", "fammi vedere tutti")
        if (preg_match('/\b(i file|i document|tutti|gli ultimi|quelli)\b/i', $this->message)) {
            return null;
        }

        // Extraer nombre del archivo
        $nombre = $this->extractFileName();
        if (!$nombre || strlen($nombre) < 2) return null;

        // Buscar en DB
        $docs = $this->searchDocs($nombre, 3);

        if ($docs->isEmpty()) {
            return [
                'message' => "Non ho trovato nessun file con \"{$nombre}\". Prova con un altro termine.",
                'action'  => 'nessuna',
                'params'  => [],
            ];
        }

        if ($docs->count() === 1) {
            return [
                'message' => "Apro il file **{$docs->first()['nombre']}**...",
                'action'  => 'apri_file',
                'params'  => ['docs' => $docs->toArray()],
            ];
        }

        return [
            'message' => "Ho trovato {$docs->count()} file. Quale vuoi aprire?",
            'action'  => 'mostrar_documentos',
            'params'  => ['docs' => $docs->toArray()],
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // LISTAR/MOSTRAR ARCHIVOS
    // "mostrami i file", "file caricati oggi", "ultimi documenti"
    // ═══════════════════════════════════════════════════════════
    private function resolveShowFiles(): ?array
    {
        $isShowIntent = preg_match(
            '/\b(mostra|mostram[ie]|elenca|lista|elenco|file|document[oi]|caricati?|ultim[oia]|ultimi|recenti|tutti i file|quanti file|cercando|cerco|cerca|show|find|search|muestrame|muestra|busco|busca|dame|ver los)\b/i',
            $this->message
        );

        // Si tiene "cerco/cerca" pero NO tiene "file/document/caricati" → no es listar archivos
        if (preg_match('/\b(cerco|cerca|cercando|sto cercando)\b/i', $this->message)
            && !preg_match('/\b(file|document[oi]|caricati?|pdf|excel|word|immagin|foto|ultim[oia]|ultimi|recenti)\b/i', $this->message)) {
            return null;
        }

        if (!$isShowIntent) return null;

        // Excluir si es claramente proyecto
        if (preg_match('/\bprogett[oi]\b/i', $this->message) && !preg_match('/\b(file|document)/i', $this->message)) {
            return null;
        }

        $query = Documento::with(['proyecto', 'categoria', 'tipologia'])
            ->where('user_id', $this->user->id);

        // ── Filtro por FECHA ──
        $hasDateFilter = false;
        if (preg_match('/\b(oggi|today|hoy)\b/i', $this->message)) {
            $query->whereDate('created_at', today());
            $hasDateFilter = true;
        } elseif (preg_match('/\b(ieri|yesterday|ayer)\b/i', $this->message)) {
            $query->whereDate('created_at', today()->subDay());
            $hasDateFilter = true;
        } elseif (preg_match('/\b(questa settimana|ultima settimana|last week|this week|esta semana)\b/i', $this->message)) {
            $query->where('created_at', '>=', now()->subWeek());
            $hasDateFilter = true;
        } elseif (preg_match('/\b(questo mese|ultimo mese|last month|this month|este mes)\b/i', $this->message)) {
            $query->where('created_at', '>=', now()->subMonth());
            $hasDateFilter = true;
        } elseif (preg_match('/\b(ultim[oia]|ultimo|last|reciente|più recente)\b/i', $this->message)) {
            // "ultimo file", "last file" → solo el más reciente, sin filtro de fecha
            $query->limit(3);
            $hasDateFilter = true;
        }

        // ── Filtro por TIPO de archivo ──
        $hasTypeFilter = false;
        if (preg_match('/\b(pdf)\b/i', $this->message)) {
            $query->where('mime_type', 'like', '%pdf%');
            $hasTypeFilter = true;
        } elseif (preg_match('/\b(excel|xlsx|xls|fogli[oi])\b/i', $this->message)) {
            $query->where(function ($q) {
                $q->where('mime_type', 'like', '%excel%')
                  ->orWhere('mime_type', 'like', '%spreadsheet%');
            });
            $hasTypeFilter = true;
        } elseif (preg_match('/\b(immagin[ie]|foto|png|jpg|jpeg|image)\b/i', $this->message)) {
            $query->where('mime_type', 'like', 'image/%');
            $hasTypeFilter = true;
        } elseif (preg_match('/\b(word|docx|doc)\b/i', $this->message)) {
            $query->where(function ($q) {
                $q->where('mime_type', 'like', '%word%')
                  ->orWhere('mime_type', 'like', '%document%');
            });
            $hasTypeFilter = true;
        }

        // ── Filtro por PROYECTO (nombre) ──
        $hasProjectFilter = false;
        if (preg_match('/\b(?:del progetto|progetto|nel progetto|del proyecto|proyecto)\s+["\']?(.+?)["\']?\s*$/i', $this->message, $m)) {
            $projName = trim($m[1]);
            $proyecto = Proyecto::where('user_id', $this->user->id)
                ->where('nombre', 'like', "%{$projName}%")
                ->first();
            if ($proyecto) {
                $query->where('proyecto_id', $proyecto->id);
                $hasProjectFilter = true;
            }
        }

        // ── Filtro por texto libre (SOLO si no hay otro filtro activo) ──
        if (!$hasDateFilter && !$hasTypeFilter && !$hasProjectFilter) {
            $searchTerm = $this->extractSearchTerm();
            if ($searchTerm && strlen($searchTerm) >= 2) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('descripcion', 'like', "%{$searchTerm}%");
                });
            }
        }

        $docs = $query->orderBy('created_at', 'desc')
            ->limit($hasDateFilter ? 10 : 10)
            ->get()
            ->map(fn($d) => [
                'id'          => $d->id,
                'nombre'      => $d->nombre,
                'mime_type'   => $d->mime_type,
                'created_at'  => $d->created_at->format('Y-m-d'),
                'categoria'   => $d->categoria?->nombre,
                'tipologia'   => $d->tipologia?->nombre,
                'proyecto_id' => $d->proyecto_id,
                'proyecto'    => $d->proyecto?->nombre,
                'descripcion' => $d->descripcion,
            ]);

        if ($docs->isEmpty()) {
            return [
                'message' => 'Non ho trovato documenti con questi criteri.',
                'action'  => 'nessuna',
                'params'  => [],
            ];
        }

        return [
            'message' => $docs->count() === 1
                ? "Ho trovato 1 documento:"
                : "Ho trovato {$docs->count()} documenti:",
            'action'  => 'mostrar_documentos',
            'params'  => ['docs' => $docs->toArray()],
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // ABRIR PROYECTO
    // "apri progetto X", "vai al progetto X"
    // ═══════════════════════════════════════════════════════════
    private function resolveOpenProject(): ?array
    {
        if (!preg_match('/\b(apri|apre|aprimi|aprilo|aprire|vai\s+a[l]?|apriamo)\b/i', $this->message)) {
            return null;
        }

        if (!preg_match('/\bprogett[oi]\b/i', $this->message)) {
            return null;
        }

        // Extraer nombre del proyecto
        $nombre = preg_replace(
            '/^.*?\b(?:apri|apre|aprimi|aprilo|aprire|vai\s+a[l]?|apriamo)\s+(?:il\s+|mi\s+)?progett[oi]\s*/i',
            '',
            $this->message
        );
        $nombre = trim($nombre, ' "\'.');

        if (strlen($nombre) < 2) return null;

        $proyectos = Proyecto::where('user_id', $this->user->id)
            ->where('nombre', 'like', "%{$nombre}%")
            ->limit(5)->get();

        if ($proyectos->isEmpty()) {
            return [
                'message' => "Non ho trovato nessun progetto con \"{$nombre}\".",
                'action'  => 'nessuna',
                'params'  => [],
            ];
        }

        if ($proyectos->count() === 1) {
            $p = $proyectos->first();
            return [
                'message' => "Apro il progetto **{$p->nombre}**...",
                'action'  => 'apri_progetto',
                'params'  => [
                    'id'     => $p->id,
                    'nombre' => $p->nombre,
                ],
            ];
        }

        return [
            'message' => "Ho trovato {$proyectos->count()} progetti:",
            'action'  => 'mostrar_proyectos',
            'params'  => [
                'proyectos' => $proyectos->map(fn($p) => [
                    'id'     => $p->id,
                    'nombre' => $p->nombre,
                ])->toArray(),
            ],
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // BUSCAR PROYECTO
    // "cerco progetto X", "trova progetto X"
    // ═══════════════════════════════════════════════════════════
    private function resolveSearchProject(): ?array
    {
        if (!preg_match('/\b(cerco|cerca|trova|sto cercando)\b/i', $this->message)) {
            return null;
        }

        if (!preg_match('/\bprogett[oi]\b/i', $this->message)) {
            return null;
        }

        $nombre = preg_replace(
            '/^.*?\b(?:cerco|cerca|trova|sto cercando)\s+(?:il\s+|un\s+)?progett[oi]\s*/i',
            '',
            $this->message
        );
        $nombre = trim($nombre, ' "\'.');

        if (strlen($nombre) < 2) return null;

        $proyectos = Proyecto::where('user_id', $this->user->id)
            ->where('nombre', 'like', "%{$nombre}%")
            ->limit(5)->get();

        if ($proyectos->isEmpty()) {
            return [
                'message' => "Non ho trovato nessun progetto con \"{$nombre}\".",
                'action'  => 'nessuna',
                'params'  => [],
            ];
        }

        return [
            'message' => "Ho trovato {$proyectos->count()} progetto/i:",
            'action'  => 'mostrar_proyectos',
            'params'  => [
                'proyectos' => $proyectos->map(fn($p) => [
                    'id'     => $p->id,
                    'nombre' => $p->nombre,
                ])->toArray(),
            ],
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Extraer nombre de archivo del mensaje.
     */
    private function extractFileName(): ?string
    {
        // Quitar verbos y artículos iniciales
        $clean = preg_replace(
            '/^.*?\b(?:apri|apre|aprimi|aprilo|aprire|mostrami|fammi vedere|voglio vedere|abrelo|abrilo|abre|abrir|abreme|ábrelo)\s+(?:il\s+|la\s+|lo\s+|el\s+)?(?:file|documento|doc)?\s*/i',
            '',
            $this->message
        );
        $clean = trim($clean, ' "\'.');

        // Si queda algo útil, devolverlo
        return strlen($clean) >= 2 ? $clean : null;
    }

    /**
     * Extraer término de búsqueda libre del mensaje (para listar).
     * Quita las palabras comunes/filtros ya procesados.
     */
    private function extractSearchTerm(): ?string
    {
        $clean = $this->message;

        // Quitar frases de intención (IT/ES/EN)
        $clean = preg_replace(
            '/\b(mostra|mostram[ie]|elenca|lista|fammi vedere|voglio vedere|i miei|gli ultimi|i file|i document[oi]|caricati?|recenti|ultimi|ultimo|ultima|cerco|cerca|cercando|sto cercando|sto|tipo|di tipo)\b/i',
            '', $clean
        );

        // Quitar verbos y palabras comunes ES/EN/IT
        $clean = preg_replace(
            '/\b(show|me|my|the|a|an|from|last|recent|latest|find|search|get|open|muestrame|muestram[ie]|muestra|busco|busca|buscando|dame|ver|mostrar|abrir|abre|abreme|los|las|mis|el|la|lo|le|un|una|del|de|que|he|ha|con|por|nel|dei|degli|delle|il|gli|i|di|che|ho|sono|hai|è|più|mio|mia|miei|mie|este|esta|esto|estos|estas|agregado|subido|cargado|uploaded|added|loaded)\b/i',
            '', $clean
        );

        // Quitar filtros de fecha
        $clean = preg_replace(
            '/\b(oggi|ieri|hoy|ayer|today|yesterday|questa settimana|ultima settimana|this week|last week|esta semana|questo mese|ultimo mese|this month|last month|este mes)\b/i',
            '', $clean
        );

        // Quitar filtros de tipo
        $clean = preg_replace(
            '/\b(pdf|excel|xlsx|xls|word|docx|immagini|foto|png|jpg)\b/i',
            '', $clean
        );

        // Quitar filtros de proyecto
        $clean = preg_replace('/\b(?:del|nel|de[l]?)\s+pro[yg]ect[oi]?\s+.+$/i', '', $clean);

        $clean = trim($clean, ' ,.;:');
        return strlen($clean) >= 2 ? $clean : null;
    }

    /**
     * Buscar documentos por nombre/descripción.
     */
    private function searchDocs(string $term, int $limit = 8)
    {
        return Documento::with(['proyecto', 'categoria', 'tipologia'])
            ->where('user_id', $this->user->id)
            ->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('descripcion', 'like', "%{$term}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($d) => [
                'id'          => $d->id,
                'nombre'      => $d->nombre,
                'mime_type'   => $d->mime_type,
                'created_at'  => $d->created_at->format('Y-m-d'),
                'categoria'   => $d->categoria?->nombre,
                'tipologia'   => $d->tipologia?->nombre,
                'proyecto_id' => $d->proyecto_id,
                'proyecto'    => $d->proyecto?->nombre,
                'descripcion' => $d->descripcion,
            ]);
    }
}
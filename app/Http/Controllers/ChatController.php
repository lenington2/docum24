<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Documento;
use App\Services\ClaudeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    protected ClaudeService $claude;

    protected \App\Services\TokenService $tokenService;

    public function __construct(ClaudeService $claude)
    {
        $this->claude       = $claude;
        $this->tokenService = new \App\Services\TokenService();
    }

    public function send(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'nullable|string',
            'deep_search' => 'nullable|boolean',
        ]);

        $user      = Auth::user();
        $message   = $request->message;
        $sessionId = $request->session_id ?? uniqid('chat_');
        $esNuevo   = $request->boolean('es_nuevo', false);

        // ═══════════════════════════════════════════════════════════
        // BLOQUE ONBOARDING — solo para usuarios sin business_type
        // ═══════════════════════════════════════════════════════════

        // Onboarding de business_type eliminado — se hace en el registro

        // ═══════════════════════════════════════════════════════════
        // BÚSQUEDA DE PROYECTO
        // ═══════════════════════════════════════════════════════════

        if (
            preg_match('/cerco|cerca|trova|sto cercando|apri|aprir[ei]|vai a|apriamo/i', $message) &&
            preg_match('/progett[oi]/i', $message)
        ) {

            $termino = preg_replace(
                '/^.*?(cerco|cerca|trova|sto cercando|apri|apriamo|vai a)\s+(il\s+|un\s+|la\s+)?progett[oi]\s*/i',
                '',
                $message
            );
            $termino = trim($termino);

            if (strlen($termino) >= 2) {
                $proyectos = \App\Models\Proyecto::where('user_id', $user->id)
                    ->where('nombre', 'like', "%{$termino}%")
                    ->limit(5)->get();

                if ($proyectos->count() > 0) {
                    return response()->json([
                        'success'    => true,
                        'response'   => "Ho trovato {$proyectos->count()} progetto/i:",
                        'action'     => 'mostrar_proyectos',
                        'params'     => ['proyectos' => $proyectos->map(fn($p) => [
                            'id'     => $p->id,
                            'nombre' => $p->nombre,
                        ])],
                        'session_id' => $sessionId,
                    ]);
                }
            }
        }

        // ═══════════════════════════════════════════════════════════
        // BÚSQUEDA DE DOCUMENTO
        // ═══════════════════════════════════════════════════════════

        if (preg_match('/cerco|cerca|trova|sto cercando|searching|busco|find/i', $message)) {

            $termino = preg_replace(
                '/^.*?(cerco|cerca|trova|sto cercando|busco)\s+(un\s+)?(documento|file|doc)?\s*(con\s+)?(questo:?|questi:?)?\s*/i',
                '',
                $message
            );
            $termino = trim($termino);
            if (strlen($termino) < 2) $termino = $message;

            $palabras = array_filter(explode(' ', $termino), fn($p) => strlen($p) >= 2);

            if (!empty($palabras)) {
                $docs = \App\Models\Documento::with(['proyecto', 'categoria', 'tipologia'])
                    ->where('user_id', $user->id)
                    ->where(function ($q) use ($palabras, $termino) {
                        $q->where('descripcion', 'like', "%{$termino}%")
                            ->orWhere('nombre', 'like', "%{$termino}%");

                        foreach (array_filter($palabras, fn($p) => strlen($p) >= 4) as $palabra) {
                            if (in_array(strtolower($palabra), [
                                'questo',
                                'questa',
                                'numero',
                                'ordine',
                                'valore',
                                'documento',
                                'file',
                                'cerca',
                                'cerco',
                                'nella',
                            ])) continue;
                            $q->orWhere('descripcion', 'like', "%{$palabra}%")
                                ->orWhere('nombre', 'like', "%{$palabra}%");
                        }
                    })
                    ->limit(8)->get();

                if ($docs->count() > 0) {
                    return response()->json([
                        'success'    => true,
                        'response'   => "Ho trovato {$docs->count()} documento/i che potrebbero corrispondere:",
                        'action'     => 'mostrar_documentos',
                        'params'     => ['docs' => $docs->map(fn($d) => [
                            'id'          => $d->id,
                            'nombre'      => $d->nombre,
                            'mime_type'   => $d->mime_type,
                            'categoria'   => $d->categoria?->nombre,
                            'tipologia'   => $d->tipologia?->nombre,
                            'proyecto_id' => $d->proyecto_id,
                            'proyecto'    => $d->proyecto?->nombre,
                            'descripcion' => $d->descripcion,
                        ])],
                        'session_id' => $sessionId,
                    ]);
                }
            }
        }

        // ═══════════════════════════════════════════════════════════
        // LLAMADA A CLAUDE
        // ═══════════════════════════════════════════════════════════

        $contexto  = $this->buildContexto($user);
        $cacheKey  = "chat_session_{$user->id}_{$sessionId}";
        $historial = cache()->get($cacheKey, []);
        $historial[] = ['role' => 'user', 'content' => $message];

        try {

            $deepSearch = $request->boolean('deep_search', false);

            if ($deepSearch) {
                $result = $this->callClaudeDeepSearch($historial, $contexto, $user, $message);
            } else {
                $result = $this->callClaude($historial, $contexto, $user, $esNuevo);
            }


            $historial[] = ['role' => 'assistant', 'content' => $result['message']];
            if (count($historial) > 20) $historial = array_slice($historial, -20);
            cache()->put($cacheKey, $historial, now()->addHours(2));

            // Persistir en BD (upsert por session_id)
            \App\Models\Conversacion::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'user_id'           => $user->id,
                    'historial'         => $historial,
                    'ultimo_mensaje_at' => now(),
                    'titulo'            => \App\Models\Conversacion::where('session_id', $sessionId)->exists()
                        ? \App\Models\Conversacion::where('session_id', $sessionId)->value('titulo')
                        : \App\Models\Conversacion::tituloDesde($message),
                ]
            );

            return response()->json([
                'success'    => true,
                'response'   => $result['message'],
                'action'     => $result['action'],
                'params'     => $result['params'],
                'session_id' => $sessionId,
                'referencias' => $result['referencias'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('ChatController error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error'   => 'Errore nella comunicazione con l\'AI.',
            ], 500);
        }
    }

    private function buildContexto($user): string
    {
        $proyectos = Proyecto::where('user_id', $user->id)
            ->with(['categorias.tipologias'])
            ->get();

        $totalDocs = Documento::where('user_id', $user->id)->count();

        $ctx  = "=== CONTESTO UTENTE ===\n";
        $ctx .= "Utente: {$user->name}\n";

        // ── Prompt de negocio personalizado ──
        if (!empty($user->ai_prompt)) {
            $ctx .= "Tipo di business: {$user->business_type}\n\n";
            $ctx .= "=== PROFILO PROFESSIONALE ===\n";
            $ctx .= $user->ai_prompt . "\n\n";
            $ctx .= "=== LIMITI DI COMPETENZA ===\n";
            $ctx .= "Rispondi ESCLUSIVAMENTE a domande inerenti all'attività del business dell'utente, alla gestione documentale e alle questioni professionali del settore indicato nel profilo.\n";
            $ctx .= "Se l'utente pone domande su argomenti non correlati (tecnologia generica, programmazione, cucina, sport, o qualsiasi tema non professionale), rispondi che puoi assistere solo in materia di {$user->business_type} e gestione documentale. ECCEZIONE: rispondi sempre alle domande su come usare Docum24 e le sue funzionalità.\n\n";
            $ctx .= "\nSe l'utente chiede come usare Docum24 o le sue funzionalità, rispondi: 'Per questo ti consiglio il nostro **Supporto Docum24**! Vuoi che lo apra?' e usa action: apri_supporto con params: {\"domanda\": \"domanda originale\"}\n";
        }

        $ctx .= "Totale documenti: {$totalDocs}\n\n";

        if ($proyectos->isEmpty()) {
            $ctx .= "Nessun progetto ancora creato.\n";
        } else {
            $ctx .= "=== PROGETTI E STRUTTURA ===\n";
            foreach ($proyectos as $p) {
                $docCount = Documento::where('proyecto_id', $p->id)->count();
                $ctx .= "📁 Progetto: {$p->nombre} (ID:{$p->id}) — {$docCount} documenti\n";
                foreach ($p->categorias as $cat) {
                    $catDocs = Documento::where('categoria_id', $cat->id)->count();
                    $ctx .= "  📂 Categoria: {$cat->nombre} (ID:{$cat->id}) — {$catDocs} documenti\n";
                    foreach ($cat->tipologias as $tip) {
                        $tipDocs = Documento::where('tipologia_id', $tip->id)->count();
                        $ctx .= "    🏷️ Tipologia: {$tip->nombre} (ID:{$tip->id}) — {$tipDocs} documenti\n";
                    }
                }
            }
        }

        // Últimos 5 documentos
        $ultimos = Documento::where('user_id', $user->id)
            ->with(['categoria', 'tipologia', 'proyecto'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($ultimos->isNotEmpty()) {
            $ctx .= "\n=== ULTIMI DOCUMENTI CARICATI ===\n";
            foreach ($ultimos as $doc) {
                $ctx .= "• {$doc->nombre} → {$doc->proyecto->nombre} / {$doc->categoria->nombre} / {$doc->tipologia->nombre}";
                if ($doc->fecha_documento) $ctx .= " ({$doc->fecha_documento})";
                $ctx .= "\n";
            }
        }

        return $ctx;
    }

    private function callClaude(array $historial, string $contexto, $user, bool $esNuevo = false): array
    {
        $isPrimoMessaggio = $esNuevo && count($historial) === 1;

        if ($isPrimoMessaggio) {
            $onboardingBlock = 'ONBOARDING - PRIMO ACCESSO:
Ignora TUTTE le altre regole. Questo è il primo accesso dell\'utente.
Rispondi SOLO con questo JSON esatto (nessun testo fuori):
{"message":"Benvenuto in Docum24! 👋 Come vuoi iniziare?","action":"onboarding_start","params":{}}';
        } else {
            $onboardingBlock = 'ONBOARDING (solo se nel CONTESTO risulta "Nessun progetto ancora creato"):

REGOLA PRIORITARIA: Durante l\'onboarding NON aprire mai form, NON usare crea_progetto,
NON usare nessuna azione diversa da quelle onboarding. Seguire i passi in ordine ESATTO.

PASSO 0b — L\'utente descrive il suo settore/attività/business (qualsiasi descrizione):
{"message":"Perfetto! Sto configurando Docum24 per [tipo business]... 🔧","action":"onboarding_genera_prompt","params":{"business_type":"descrizione esatta scritta dall\'utente"}}

PASSO 1 — L\'utente invia "Il mio business è: X. Suggeriscimi un nome per il progetto.":
{"message":"In Docum24, un **Progetto** è il contenitore principale dei tuoi documenti — pensalo come una cartella principale del tuo studio. 📁\n\nOgni progetto può avere **Categorie** (es: Pazienti, Fatture, Contratti) e ogni categoria delle **Tipologie** (es: Contratto, Ricevuta, Referto).\n\nStiamo creando il tuo **primo progetto** per iniziare — potrai crearne altri in qualsiasi momento! 🚀\n\nEcco 3 nomi che ti suggerisco:","action":"onboarding_nome_progetto","params":{"tipo":"tipo_business","nomi":["nome1","nome2","nome3"]}}

PASSO 2 — L\'utente ha scelto o scritto il nome del progetto (un nome qualsiasi):
{"message":"Ottimo nome! Ora creiamo il progetto...","action":"onboarding_crea_progetto","params":{"nome":"nome_scelto"}}

PASSO 3 — Subito dopo la creazione del progetto, suggerisci 5 categorie specifiche per il business:
{"message":"Ora aggiungiamo le categorie. Ecco quelle che ti consiglio per il tuo business:","action":"onboarding_suggerisci_categorie","params":{"categorie":["cat1","cat2","cat3","cat4","cat5"]}}

PASSO 4 — L\'utente conferma con "Ho scelto queste categorie: X, Y, Z. Crea tutto.":
{"message":"Perfetto! Creo tutto ora...","action":"onboarding_crea_tutto","params":{"nome_progetto":"nome scelto nei passi precedenti","categorie":["cat1","cat2"],"tipologie_per_categoria":{"cat1":["tip1","tip2"],"cat2":["tip1","tip2"]}}}';
        }

        $reglasGenerales = $esNuevo ? '' : 'REGOLA CRITICA SULLE AZIONI:
Quando l\'utente esprime QUALSIASI intenzione di creare, modificare, eliminare o aprire qualcosa,
devi IMMEDIATAMENTE restituire l\'azione corrispondente. NON chiedere conferme o dettagli aggiuntivi.
NON spiegare i passi. Apri direttamente il form.
- Se l\'utente chiede come usare l\'app, come fare qualcosa, o hai dubbi → action: apri_supporto

Esempi:
- "crea progetto" / "nuovo progetto" → crea_progetto
- "crea categoria" / "nuova categoria" → crea_categoria
- "crea tipologia" / "nuova tipologia" → crea_tipologia
- "modifica/elimina progetto" → gestisci_progetti
- "modifica/elimina categoria" → gestisci_categorie
- "modifica/elimina tipologia" → gestisci_tipologie
- "apri progetto X" → apri_progetto con params.nome
- "genera report" / "voglio un report" / "crea un report" / "esporta" / "analisi documenti" / "scarica report" → avvia_report
- "come carico" / "come si caricano" / "come faccio upload" / "caricare file" / "come carico i file" → guida_carica_file
- "come interrogo" / "come faccio domande ai file" / "chattare con i file" / "analizzare documenti" / "fare domande ai documenti" → guida_interroga_file

REGOLA SUGGERIMENTO:
Per domande informative su COME fare qualcosa, aggiungi alla fine: " Vuoi che apra direttamente il form?"
- domanda su categoria → action: suggerisci_categoria
- domanda su tipologia → action: suggerisci_tipologia
- domanda su progetto  → action: suggerisci_progetto';

        $systemPrompt = "Sei Docum24 AI, l'assistente per la gestione documentale di {$user->name}.

REGOLE FONDAMENTALI:
- Rispondi SEMPRE in italiano
- Rispondi SEMPRE e SOLO con JSON valido, nessun testo fuori dal JSON
- Sii diretto e conciso nel campo 'message'

AZIONI DISPONIBILI:
- crea_progetto, gestisci_progetti
- crea_categoria, gestisci_categorie
- crea_tipologia, gestisci_tipologie
- apri_progetto
- onboarding_start, onboarding_genera_prompt, onboarding_nome_progetto
- onboarding_crea_progetto, onboarding_suggerisci_categorie, onboarding_crea_tutto
- suggerisci_progetto, suggerisci_categoria, suggerisci_tipologia
- nessuna
- avvia_report
- guida_carica_file
- guida_interroga_file

FORMATO RISPOSTA — SOLO JSON:
{
  \"message\": \"risposta in italiano\",
  \"action\": \"nome_azione\",
  \"params\": {}
}

{$reglasGenerales}

{$onboardingBlock}

{$contexto}";

       $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1000,
                'system'     => $systemPrompt,
                'messages'   => $historial,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->status());
        }

        $text = $response->json('content.0.text');

        preg_match('/\{.*\}/s', $text, $matches);
        $data = isset($matches[0]) ? json_decode($matches[0], true) : null;

        if (!$data || !isset($data['message'])) {
            return ['message' => $text, 'action' => 'nessuna', 'params' => []];
        }

        // Registrar tokens consumidos
        $usage = $response->json('usage');
        if ($usage) {
            $this->tokenService->registrar(
                $user,
                'chat',
                $usage['input_tokens']  ?? 0,
                $usage['output_tokens'] ?? 0,
                'claude-haiku-4-5-20251001'
            );
        }

        return [
            'message' => $data['message'],
            'action'  => $data['action'] ?? 'nessuna',
            'params'  => $data['params'] ?? [],
        ];
    }

    public function generarBusinessPrompt(Request $request)
    {
        $request->validate(['business_type' => 'required|string|max:200']);

        $user         = Auth::user();
        $businessType = $request->business_type;

        // Llamada a Claude para generar el prompt personalizado
        $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 600,
                'system'     => 'Sei un esperto nella creazione di system prompt per assistenti AI. Rispondi SOLO con il testo del prompt, senza spiegazioni, senza JSON, senza backtick.',
                'messages'   => [[
                    'role'    => 'user',
                    'content' => "Crea un system prompt in italiano per un assistente AI specializzato in: \"{$businessType}\".
                Il prompt deve:
                - Iniziare con 'Sei un esperto assistente specializzato in {$businessType}...'
                - Definire il ruolo professionale specifico (es: avvocato immobiliare, commercialista, medico...)
                - Elencare le competenze chiave del settore
                - Indicare come deve rispondere (tono professionale, terminologia del settore)
                - Specificare che aiuta nella gestione documentale del settore
                - Massimo 150 parole
                - Solo il testo del prompt, nulla altro"
                ]],
            ]);

        if (!$response->successful()) {
            return response()->json(['success' => false], 500);
        }

        $aiPrompt = trim($response->json('content.0.text'));

        // Guardar en el usuario
        $user->update([
            'business_type' => $businessType,
            'ai_prompt'     => $aiPrompt,
        ]);

        return response()->json(['success' => true, 'prompt' => $aiPrompt]);
    }

    public function generarBusinessPromptPublic(Request $request)
    {
        $request->validate(['business_type' => 'required|string|max:200']);

        $businessType = $request->business_type;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(20)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 600,
                'system'     => 'Sei un esperto nella creazione di system prompt per assistenti AI. Rispondi SOLO con il testo del prompt, senza spiegazioni, senza JSON, senza backtick.',
                'messages'   => [[
                    'role'    => 'user',
                    'content' => "Crea un system prompt in italiano per un assistente AI specializzato in: \"{$businessType}\".
                Il prompt deve:
                - Iniziare con 'Sei un esperto assistente specializzato in {$businessType}...'
                - Definire il ruolo professionale specifico
                - Elencare le competenze chiave del settore
                - Indicare come deve rispondere (tono professionale, terminologia del settore)
                - Specificare che aiuta nella gestione documentale del settore
                - Massimo 150 parole
                - Solo il testo del prompt, nulla altro",
                ]],
            ]);

        if (!$response->successful()) {
            return response()->json(['success' => false], 500);
        }

        $aiPrompt = trim($response->json('content.0.text'));

        return response()->json(['success' => true, 'prompt' => $aiPrompt]);
    }

    private function callClaudeDeepSearch(array $historial, string $contexto, $user, string $message): array
    {
        $deepService = new \App\Services\DeepSearchService();

        // 1. Obtener URLs favoritas del usuario
        $urlsFavoritas = \App\Models\UrlFavorita::where('user_id', $user->id)->get();

        $fuentesContenido = [];
        $referencias = [];

        // 2. Fetch de URLs favoritas
        foreach ($urlsFavoritas as $urlFav) {
            $contenido = $deepService->fetchUrl($urlFav->url);
            if ($contenido) {
                $fuentesContenido[] = [
                    'nombre'    => $urlFav->nombre,
                    'url'       => $urlFav->url,
                    'contenido' => $contenido,
                ];
            }
        }

        // 3. Construir contexto DeepSearch
        $deepContext = '';
        if (!empty($fuentesContenido)) {
            $deepContext .= "\n\nFONTI PREFERITE DELL'UTENTE (cerca prima qui):\n";
            foreach ($fuentesContenido as $fonte) {
                $deepContext .= "\n--- FONTE: {$fonte['nome']} ({$fonte['url']}) ---\n";
                $deepContext .= $fonte['contenido'] . "\n";
            }
        }

        // 4. System prompt DeepSearch
        $systemPrompt = "Sei Docum24 AI in modalità DeepSearch per {$user->name}.

REGOLE:
- Rispondi SEMPRE in italiano
- Rispondi SEMPRE e SOLO con JSON valido
- Usa le FONTI PREFERITE come fonte primaria
- Se trovi informazioni rilevanti nelle fonti, citale con [Fonte: nome_fonte]
- Indica SEMPRE da dove hai preso le informazioni

FORMATO RISPOSTA:
{
  \"message\": \"risposta dettagliata con citazioni [Fonte: X]\",
  \"action\": \"nessuna\",
  \"params\": {},
  \"fonti_usate\": [\"url1\", \"url2\"]
}

{$contexto}
{$deepContext}";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 2000,
                'system'     => $systemPrompt,
                'messages'   => $historial,
                'tools'      => [
                    [
                        'type' => 'web_search_20250305',
                        'name' => 'web_search',
                    ]
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Claude DeepSearch API error: ' . $response->status());
        }

        // Procesar respuesta con tool use
        $content = $response->json('content');
        $text = '';
        $fontiUsate = [];

        foreach ($content as $block) {
            if ($block['type'] === 'text') {
                $text .= $block['text'];
            }
            if ($block['type'] === 'web_search_tool_result') {
                if (isset($block['content'])) {
                    foreach ((array)$block['content'] as $item) {
                        if (isset($item['url'])) {
                            $fontiUsate[] = $item['url'];
                        }
                    }
                }
            }
        }

        preg_match('/\{.*\}/s', $text, $matches);
        $data = isset($matches[0]) ? json_decode($matches[0], true) : null;

        // Registrar tokens
        $usage = $response->json('usage');
        if ($usage) {
            $this->tokenService->registrar(
                $user,
                'deep_search',
                $usage['input_tokens']  ?? 0,
                $usage['output_tokens'] ?? 0,
                'claude-haiku-4-5-20251001'
            );
        }

        // Construir referencias para el frontend
        foreach ($fuentesContenido as $fonte) {
            $referencias[] = [
                'nombre' => $fonte['nombre'],
                'url'    => $fonte['url'],
                'tipo'   => 'favorita',
            ];
        }
        foreach ($fontiUsate as $url) {
            $referencias[] = [
                'nombre' => parse_url($url, PHP_URL_HOST) ?? $url,
                'url'    => $url,
                'tipo'   => 'web',
            ];
        }

        return [
            'message'     => $data['message'] ?? $text,
            'action'      => $data['action']  ?? 'nessuna',
            'params'      => $data['params']  ?? [],
            'referencias' => $referencias,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SupportChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:1000',
            'historial' => 'nullable|array',
        ]);

        $user     = Auth::user();
        $message  = $request->message;
        $historial = $request->historial ?? [];

        $systemPrompt = "Sei l'assistente di supporto di Docum24, un'app di gestione documentale con AI.

Il tuo compito è aiutare l'utente {$user->name} a usare l'app in modo semplice e diretto.

FUNZIONALITÀ DI DOCUM24:
- Progetti: contenitori principali dei documenti
- Categorie: sezioni dentro ogni progetto (es. Contratti, Fatture)
- Tipologie: tipi di documento dentro ogni categoria
- DataRoom: visualizzatore dei documenti del progetto
- Upload AI: carica file e l'AI li categorizza automaticamente
- Report: genera PDF o Word con analisi dei documenti
- Chat Documenti: chatta con i tuoi documenti selezionati
- DeepSearch: ricerca nel web e nelle fonti preferite
- Notifiche scadenza: promemoria email per documenti in scadenza
- Cronologia: storico delle attività e delle conversazioni

AZIONI DISPONIBILI (restituisci nel campo 'action'):
- apri_report → apre il wizard del report
- apri_progetto → apre il pannello dei progetti
- crea_progetto → apre il form crea progetto
- crea_categoria → apre il form crea categoria
- apri_cronologia → apre la cronologia attività
- apri_impostazioni → apre le impostazioni
- nessuna → solo risposta testuale

REGOLE:
- Rispondi SEMPRE in italiano
- Sii breve, amichevole e pratico
- Se l'utente chiede come fare qualcosa, spiegalo in 2-3 passi e offri di aprire direttamente il form
- Rispondi SOLO con JSON valido
- IMPORTANTE: Usa SEMPRE l'azione corrispondente quando l'utente chiede di vedere o aprire qualcosa:
  * 'cronologia' / 'attività' / 'storico' → action: apri_cronologia
  * 'report' → action: apri_report
  * 'progetto' / 'progetti' → action: apri_progetto
  * 'impostazioni' / 'configurazione' → action: apri_impostazioni
  * 'crea progetto' → action: crea_progetto
  * 'crea categoria' → action: crea_categoria
- Se non riesci ad aiutare o non conosci la risposta → action: suggerisci_supporto
- NON rispondere mai che non hai un\'azione — usa SEMPRE l\'azione più vicina disponibile

ESEMPI OBBLIGATORI:
User: \"come vedo la cronologia\" → {\"response\": \"Apro subito la cronologia per te! 📋\", \"action\": \"apri_cronologia\"}
User: \"fammi vedere i progetti\" → {\"response\": \"Ecco i tuoi progetti! 📁\", \"action\": \"apri_progetto\"}
User: \"voglio fare un report\" → {\"response\": \"Avvio il wizard del report! 📊\", \"action\": \"apri_report\"}
User: \"apri le impostazioni\" → {\"response\": \"Apro le impostazioni! ⚙️\", \"action\": \"apri_impostazioni\"}
User: \"come funziona il deepSearch?\" → {\"response\": \"DeepSearch ti permette di cercare nel web...\", \"action\": \"nessuna\"}

FORMATO:
{
  \"response\": \"risposta in italiano\",
  \"action\": \"nome_azione o nessuna\"
}";

        $historial[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withHeaders([
                    'x-api-key'         => config('services.anthropic.key'),
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ])
                ->timeout(30)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model'      => 'claude-haiku-4-5-20251001',
                    'max_tokens' => 500,
                    'system'     => $systemPrompt,
                    'messages'   => $historial,
                ]);

            if (!$response->successful()) {
                throw new \Exception('API error');
            }

            $text = $response->json('content.0.text');
            preg_match('/\{.*\}/s', $text, $matches);
            $data = isset($matches[0]) ? json_decode($matches[0], true) : null;

            // Registrar tokens
            $usage = $response->json('usage');
            if ($usage) {
                $tokenService = new \App\Services\TokenService();
                $tokenService->registrar(
                    $user, 'support_chat',
                    $usage['input_tokens']  ?? 0,
                    $usage['output_tokens'] ?? 0,
                    'claude-haiku-4-5-20251001'
                );
            }

            return response()->json([
                'success'  => true,
                'response' => $data['response'] ?? $text,
                'action'   => $data['action']   ?? 'nessuna',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'response' => 'Errore temporaneo. Riprova tra un momento.',
            ], 500);
        }
    }
}

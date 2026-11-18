<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppController extends Controller
{
    /**
     * Enviar documento(s) por WhatsApp
     */
    public function sendDocument(Request $request)
    {
        $request->validate([
            'documento_ids' => 'required|array|min:1',
            'documento_ids.*' => 'integer|exists:documentos,id',
            'telefono' => 'required|string|min:8',
            'mensaje' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $service = new WhatsAppService();
        $results = [];

        // Enviar mensaje de texto primero si hay mensaje
        if ($request->mensaje) {
            $results[] = $service->sendText(
                $request->telefono,
                "📄 *{$user->name}* ti ha inviato dei documenti tramite Docum24:\n\n{$request->mensaje}"
            );
        }

        // Enviar cada documento
        foreach ($request->documento_ids as $docId) {
            $doc = Documento::findOrFail($docId);

            // Generar URL temporal (válida 24h)
            $url = URL::temporarySignedRoute(
                'documento.preview.public',
                now()->addHours(24),
                ['documento' => $doc->id]
            );

            $results[] = $service->sendDocument(
                $request->telefono,
                $url,
                $doc->nombre_original ?? $doc->nombre . '.' . $doc->extension,
                "Inviato da {$user->name} via Docum24"
            );
        }

        // Verificar errores
        $errors = collect($results)->filter(fn($r) => isset($r['error']))->values();

        if ($errors->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'error' => $errors->first()['error']['message'] ?? 'Errore invio WhatsApp',
                'details' => $errors,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Documenti inviati via WhatsApp',
            'count' => count($request->documento_ids),
        ]);
    }

    /**
     * Enviar mensaje simple por WhatsApp
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'telefono' => 'required|string|min:8',
            'mensaje' => 'required|string|max:4000',
        ]);

        $service = new WhatsAppService();
        $result = $service->sendText($request->telefono, $request->mensaje);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']['message'] ?? 'Errore invio WhatsApp',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Messaggio inviato via WhatsApp',
        ]);
    }
}

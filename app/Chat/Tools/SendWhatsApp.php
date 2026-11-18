<?php

namespace App\Chat\Tools;

use App\Chat\ChatTool;
use App\Models\Documento;
use App\Services\WhatsAppService;

class SendWhatsApp implements ChatTool
{
    public function definition(): array
    {
        return [
            'name' => 'send_whatsapp',
            'description' => "Invia uno o più documenti tramite WhatsApp a un numero di telefono. Usa questo tool quando l'utente chiede di inviare, mandare o condividere un file/documento su WhatsApp. Richiede il numero di telefono del destinatario (con prefisso internazionale, es: +39 333 1234567) e l'ID o nome del documento.",
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'telefono' => [
                        'type' => 'string',
                        'description' => 'Numero di telefono del destinatario con prefisso internazionale (es: +393331234567, +1555123456)',
                    ],
                    'documento_ids' => [
                        'type' => 'array',
                        'items' => ['type' => 'integer'],
                        'description' => 'Array di ID dei documenti da inviare',
                    ],
                    'file_name' => [
                        'type' => 'string',
                        'description' => 'Nome del file da cercare (se non hai gli ID)',
                    ],
                    'messaggio' => [
                        'type' => 'string',
                        'description' => 'Messaggio opzionale da accompagnare al documento',
                    ],
                ],
                'required' => ['telefono'],
            ],
        ];
    }

    public function execute(array $input, $user): array
    {
        $telefono = $input['telefono'] ?? '';

        if (empty($telefono)) {
            return [
                'tool_result' => 'Serve il numero di telefono del destinatario con prefisso internazionale (es: +39 333 1234567).',
            ];
        }

        // Recoger los documentos a enviar
        $docs = collect();

        if (!empty($input['documento_ids'])) {
            $docs = Documento::whereIn('id', $input['documento_ids'])
                ->where('user_id', $user->id)
                ->get();
        } elseif (!empty($input['file_name'])) {
            $doc = Documento::where('user_id', $user->id)
                ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower(trim($input['file_name'])) . '%'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($doc) {
                $docs = collect([$doc]);
            }
        }

        if ($docs->isEmpty()) {
            return [
                'tool_result' => 'Nessun documento trovato da inviare. Specifica il nome o ID del file.',
            ];
        }

        $service = new WhatsAppService();
        $sent = 0;
        $errors = [];

        // Enviar mensaje de texto primero si hay mensaje
        $messaggio = $input['messaggio'] ?? '';
        if ($messaggio) {
            $result = $service->sendText(
                $telefono,
                "📄 *{$user->name}* ti ha inviato dei documenti tramite Docum24:\n\n{$messaggio}"
            );
            if (isset($result['error'])) {
                return [
                    'tool_result' => 'Errore invio WhatsApp: ' . ($result['error']['message'] ?? 'Errore sconosciuto') . '. Verifica il numero di telefono.',
                ];
            }
        }

        // Enviar cada documento
        foreach ($docs as $doc) {
            $token = encrypt([
                'id' => $doc->id,
                'expires' => now()->addHours(24)->timestamp,
            ]);
            $url = url('/preview-public?token=' . urlencode($token));

            $filename = $doc->nombre_original ?? ($doc->nombre . '.' . $doc->extension);

            $result = $service->sendDocument(
                $telefono,
                $url,
                $filename,
                "Inviato da {$user->name} via Docum24"
            );

            if (isset($result['error'])) {
                $errors[] = $doc->nombre . ': ' . ($result['error']['message'] ?? 'errore');
            } else {
                $sent++;
            }
        }

        if ($sent === 0 && !empty($errors)) {
            return [
                'tool_result' => 'Errore invio WhatsApp: ' . implode(', ', $errors),
            ];
        }

        $docNames = $docs->pluck('nombre')->implode(', ');

        return [
            'tool_result' => "Ho inviato {$sent} documento/i ({$docNames}) via WhatsApp al numero {$telefono}.",
            'frontend_action' => 'whatsapp_sent',
            'frontend_payload' => [
                'telefono' => $telefono,
                'count' => $sent,
                'documents' => $docs->pluck('nombre')->toArray(),
            ],
        ];
    }
}

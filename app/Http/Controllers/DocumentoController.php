<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Proyecto;
use App\Models\Categoria;
use App\Models\Tipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentiCondivisi;
use App\Models\Actividad;


class DocumentoController extends Controller
{
    // Documentos por proyecto (para el DataRoom)
    public function porProyecto(Proyecto $proyecto)
    {
        $user = Auth::user();

        // Verificar acceso: owner o miembro del team
        $tieneAccesso = $proyecto->user_id === $user->id;

        if (!$tieneAccesso && $proyecto->team_id) {
            $team = \App\Models\Team::find($proyecto->team_id);
            $tieneAccesso = $team && $team->hasUser($user);
        }

        if (!$tieneAccesso) {
            return response()->json([], 403);
        }

        // Miembros viewer no pueden ver documentos de otros usuarios
        // Editors y admin ven todos los documentos del proyecto
        $role = null;
        if ($proyecto->team_id) {
            $team = \App\Models\Team::find($proyecto->team_id);
            $role = $team?->userRole($user);
        }

        $categorias = $proyecto->categorias->map(function ($categoria) use ($user, $role) {
            $query = Documento::where('categoria_id', $categoria->id)
                ->with('tipologia')
                ->orderBy('created_at', 'desc');

            // Viewer solo ve sus propios documentos
            if ($role === 'viewer') {
                $query->where('user_id', $user->id);
            }

            return [
                'id'         => $categoria->id,
                'nombre'     => $categoria->nombre,
                'documentos' => $query->get()->map(fn($d) => [
                    'id'              => $d->id,
                    'nombre'          => $d->nombre,
                    'descripcion'     => $d->descripcion,
                    'fecha_documento' => $d->fecha_documento,
                    'mime_type'       => $d->mime_type,
                    'estado'          => $d->estado,
                    'tipologia'       => $d->tipologia?->nombre,
                ]),
            ];
        });

        return response()->json([
            'proyecto'   => $proyecto->nombre,
            'categorias' => $categorias,
        ]);
    }

    // Subir documento
    public function store(Request $request)
    {
        $request->validate([
            'archivo'          => 'required|file|max:20480',
            'proyecto_id'      => 'required|exists:proyectos,id',
            'categoria_id'     => 'required|exists:categorias,id',
            'tipologia_id'     => 'required|exists:tipologias,id',
            'nombre'           => 'nullable|string|max:255',
            'descripcion'      => 'nullable|string|max:500',
            'fecha_documento'  => 'nullable|date',
        ]);

        // Verificar límite de storage
        $tokenService = new \App\Services\TokenService();
        $fileSize     = $request->file('archivo')->getSize();
        if (!$tokenService->puedeSubirArchivo(Auth::user(), $fileSize)) {
            return response()->json([
                'success' => false,
                'error'   => 'storage_esaurito',
                'message' => 'Hai esaurito lo spazio disponibile per il tuo piano.',
            ], 403);
        }

        $proyecto = Proyecto::findOrFail($request->proyecto_id);
        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $file     = $request->file('archivo');
        $nombre   = $request->nombre ?: $file->getClientOriginalName();
        $path     = $file->store("proyectos/{$request->proyecto_id}", 'local');

        // Extraer texto
        $extractor     = new \App\Services\TextExtractorService();
        $contenidoTexto = $extractor->extract($path, $file->getMimeType());

        $documento = Documento::create([
            'proyecto_id'     => $request->proyecto_id,
            'categoria_id'    => $request->categoria_id,
            'tipologia_id'    => $request->tipologia_id,
            'user_id'         => Auth::id(),
            'nombre'          => $nombre,
            'archivo'         => $path,
            'mime_type'       => $file->getMimeType(),
            'descripcion'     => $request->descripcion,
            'fecha_documento' => $request->fecha_documento,
            'estado'          => 'pendiente',
            'contenido_texto' => $contenidoTexto,
        ]);

        Actividad::registrar(Auth::id(), 'File_creato', "Creato File: {$nombre}", $proyecto);

        return response()->json([
            'success'   => true,
            'documento' => [
                'id'              => $documento->id,
                'nombre'          => $documento->nombre,
                'descripcion'     => $documento->descripcion,
                'fecha_documento' => $documento->fecha_documento,
                'mime_type'       => $documento->mime_type,
                'estado'          => $documento->estado,
                'tipologia'       => Tipologia::find($documento->tipologia_id)?->nombre,
            ],
        ]);
    }

    // Eliminar documento
    public function destroy(Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $nombre     = $documento->nombre;
        $proyectoId = $documento->proyecto_id;

        Storage::disk('local')->delete($documento->archivo);
        $documento->delete();

        Actividad::registrar(Auth::id(), 'file_rimosso', "Rimosso file: {$nombre}", $proyectoId);

        return response()->json(['success' => true]);
    }

    // Descargar documento
    public function download(Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) {
            abort(403);
        }

        return Storage::disk('local')->download($documento->archivo, $documento->nombre);
    }

    public function info(Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) {
            return response()->json([], 403);
        }
        return response()->json($documento);
    }

    public function preview(Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) abort(403);
        return Storage::disk('local')->response(
            $documento->archivo,
            $documento->nombre,
            ['Content-Type' => $documento->mime_type, 'Content-Disposition' => 'inline; filename="' . $documento->nombre . '"']
        );
    }

    public function update(Request $request, Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'nombre'          => 'required|string|max:255',
            'categoria_id'    => 'required|exists:categorias,id',
            'tipologia_id'    => 'required|exists:tipologias,id',
            'fecha_documento' => 'nullable|date',
            'descripcion'     => 'nullable|string',
        ]);

        $documento->update($request->only(['nombre', 'categoria_id', 'tipologia_id', 'fecha_documento', 'descripcion']));

        Actividad::registrar(Auth::id(), 'file_modificato', "Modificato file: {$documento->nombre}", $documento->proyecto_id);

        return response()->json(['success' => true]);
    }

    public function previewToken(Documento $documento)
    {
        if ($documento->user_id !== Auth::id()) abort(403);

        // Token temporal de 5 minutos
        $token = encrypt([
            'id'      => $documento->id,
            'expires' => now()->addMinutes(5)->timestamp,
        ]);

        return response()->json(['token' => $token]);
    }

    public function previewPublic(Request $request)
    {
        try {
            $data = decrypt($request->token);
            if (now()->timestamp > $data['expires']) abort(410);

            $documento = Documento::findOrFail($data['id']);
            return Storage::disk('local')->response(
                $documento->archivo,
                $documento->nombre,
                ['Content-Type' => $documento->mime_type, 'Content-Disposition' => 'inline']
            );
        } catch (\Exception $e) {
            abort(403);
        }
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $docs = Documento::with(['proyecto', 'categoria', 'tipologia'])
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhere('categoria_ia', 'like', "%{$q}%")
                    ->orWhere('tipologia_ia', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($d) => [
                'id'          => $d->id,
                'nombre'      => $d->nombre,
                'mime_type'   => $d->mime_type,
                'categoria'   => $d->categoria?->nombre,
                'tipologia'   => $d->tipologia?->nombre,
                'proyecto_id' => $d->proyecto_id,
                'proyecto'    => $d->proyecto?->nombre,
                'descripcion' => $d->descripcion,
            ]);

        return response()->json($docs);
    }

    public function downloadMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) abort(400);

        // Filtrar solo documentos del usuario
        $documentos = Documento::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->with(['proyecto', 'categoria', 'tipologia'])
            ->get();

        if ($documentos->isEmpty()) abort(404);

        // Si es uno solo, descarga directa
        if ($documentos->count() === 1) {
            $doc = $documentos->first();
            return Storage::disk('local')->download($doc->archivo, $doc->nombre);
        }

        // Varios → ZIP
        $zipNombre = 'documenti_' . now()->format('Ymd_His') . '.zip';
        $zipPath   = storage_path('app/temp/' . $zipNombre);

        // Crear carpeta temp si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Impossibile creare il ZIP');
        }

        foreach ($documentos as $doc) {
            $filePath = Storage::disk('local')->path($doc->archivo);

            if (!file_exists($filePath)) continue;

            // Struttura: Progetto/Categoria/Tipologia/filename
            $progetto  = $doc->proyecto?->nombre  ?? 'Senza Progetto';
            $categoria = $doc->categoria?->nombre ?? 'Senza Categoria';
            $tipologia = $doc->tipologia?->nombre ?? 'Senza Tipologia';

            // Sanitize folder names
            $progetto  = preg_replace('/[\/\\\\:*?"<>|]/', '_', $progetto);
            $categoria = preg_replace('/[\/\\\\:*?"<>|]/', '_', $categoria);
            $tipologia = preg_replace('/[\/\\\\:*?"<>|]/', '_', $tipologia);

            $zipEntry = "{$progetto}/{$categoria}/{$tipologia}/{$doc->nombre}";

            $zip->addFile($filePath, $zipEntry);
        }

        $zip->close();

        return response()->download($zipPath, $zipNombre)->deleteFileAfterSend(true);
    }


    public function sendEmail(Request $request)
    {
        $request->validate([
            "ids"          => "required|array|min:1",
            "ids.*"        => "integer|exists:documentos,id",
            "destinatario" => "required|email",
            "oggetto"      => "required|string|max:255",
            "descrizione"  => "required|string|max:2000",
        ]);

        $documentos = Documento::whereIn("id", $request->ids)
            ->where("user_id", Auth::id())
            ->with(["proyecto", "categoria", "tipologia"])
            ->get();

        if ($documentos->isEmpty()) {
            return response()->json(["message" => "Nessun documento trovato"], 404);
        }

        // Build ZIP
        $zipNombre = "documenti_" . now()->format("Ymd_His") . ".zip";
        $zipPath   = storage_path("app/temp/" . $zipNombre);

        if (!file_exists(storage_path("app/temp"))) {
            mkdir(storage_path("app/temp"), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(["message" => "Impossibile creare il ZIP"], 500);
        }

        $fileCount = 0;
        foreach ($documentos as $doc) {
            $filePath = Storage::disk("local")->path($doc->archivo);
            if (!file_exists($filePath)) continue;

            $progetto  = preg_replace('/[\/\\\\:*?"<>|]/', '_', $doc->proyecto?->nombre ?? 'Senza Progetto');
            $categoria = preg_replace('/[\/\\\\:*?"<>|]/', '_', $doc->categoria?->nombre ?? 'Senza Categoria');
            $tipologia = preg_replace('/[\/\\\\:*?"<>|]/', '_', $doc->tipologia?->nombre ?? 'Senza Tipologia');

            $zip->addFile($filePath, "{$progetto}/{$categoria}/{$tipologia}/{$doc->nombre}");
            $fileCount++;
        }
        $zip->close();

        if ($fileCount === 0) {
            @unlink($zipPath);
            return response()->json(["message" => "Nessun file valido da inviare"], 400);
        }

        try {
            Mail::to($request->destinatario)->send(
                new DocumentiCondivisi(
                    oggetto:     $request->oggetto,
                    descrizione: $request->descrizione,
                    senderName:  Auth::user()->name,
                    zipPath:     $zipPath,
                    zipName:     $zipNombre,
                )
            );

            @unlink($zipPath);

            return response()->json(["message" => "Email inviata con successo a {$request->destinatario}"]);
        } catch (\Exception $e) {
            @unlink($zipPath);
            Log::error("Errore invio email documenti: " . $e->getMessage());
            return response()->json(["message" => "Errore durante l invio: " . $e->getMessage()], 500);
        }
    }
public function checkSize(Request $request)
    {
        $ids = $request->input('ids', []);

        $documentos = Documento::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->get();

        $details = [];
        $totalSizeMb = 0;
        $totalPages = 0;
        $leggibili = 0;
        $nonLeggibili = 0;
        $nonLeggibiliNomi = [];

        foreach ($documentos as $doc) {
            $isPdf = str_contains($doc->mime_type ?? '', 'pdf');
            $isImage = str_starts_with($doc->mime_type ?? '', 'image/');
            $hasText = !empty($doc->contenido_texto);

            $filePath = $doc->archivo;
            $fileExists = \Illuminate\Support\Facades\Storage::disk('local')->exists($filePath);
            $fileSize = $fileExists ? \Illuminate\Support\Facades\Storage::disk('local')->size($filePath) : 0;
            $fileSizeMb = round($fileSize / 1024 / 1024, 2);

            // Estimar páginas para PDFs
            $estimatedPages = $isPdf ? max(1, (int)ceil($fileSize / 50000)) : 0;

            // Determinar si es leggibile
            $canRead = false;
            $method = 'none';
            $reason = '';

            if ($isPdf && $fileExists) {
                $canRead = true;
                $method = 'pdf_nativo';
                $totalPages += $estimatedPages;
                $totalSizeMb += $fileSizeMb * 1.37; // base64 overhead
            } elseif ($isImage && $fileExists) {
                $canRead = true;
                $method = 'immagine';
                $totalSizeMb += $fileSizeMb * 1.37;
            } elseif ($hasText) {
                $canRead = true;
                $method = 'testo_estratto';
                $totalSizeMb += strlen($doc->contenido_texto) / 1024 / 1024;
            } else {
                if (!$fileExists) {
                    $reason = 'File non trovato nel server';
                } else {
                    $reason = 'Formato non supportato per lettura diretta';
                }
            }

            if ($canRead) {
                $leggibili++;
            } else {
                $nonLeggibili++;
                $nonLeggibiliNomi[] = $doc->nombre;
            }

            $details[] = [
                'id' => $doc->id,
                'nombre' => $doc->nombre,
                'mime_type' => $doc->mime_type,
                'size_mb' => $fileSizeMb,
                'pages' => $estimatedPages,
                'leggibile' => $canRead,
                'metodo' => $method,
                'motivo' => $reason,
            ];
        }

        // Validaciones de límite
        $warnings = [];
        if ($totalSizeMb > 30) {
            $warnings[] = "Dimensione totale ({$totalSizeMb} MB) supera il limite di 30 MB. Riduci il numero di documenti.";
        }
        if ($totalPages > 100) {
            $warnings[] = "Totale pagine PDF (~{$totalPages}) supera il limite di 100. Riduci il numero di PDF.";
        }

        return response()->json([
            'count'               => $documentos->count(),
            'leggibili'           => $leggibili,
            'non_leggibili'       => $nonLeggibili,
            'non_leggibili_nomi'  => $nonLeggibiliNomi,
            'total_size_mb'       => round($totalSizeMb, 2),
            'total_pages'         => $totalPages,
            'dentro_limite'       => $totalSizeMb <= 30 && $totalPages <= 100,
            'warnings'            => $warnings,
            'details'             => $details,
        ]);
    }

    public function chatConDocumenti(Request $request)
    {
        $request->validate([
            'ids'      => 'required|array',
            'message'  => 'required|string|max:2000',
            'historial' => 'nullable|array',
        ]);

        // Verificar quota
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeUsarAvanzato(Auth::user())) {
            return response()->json([
                'success' => false,
                'error'   => 'quota_esaurita',
                'message' => 'Hai esaurito i token disponibili.',
            ], 403);
        }

        $ids      = $request->input('ids');
        $message  = $request->input('message');
        $historial = $request->input('historial', []);

        // Verificar que los documentos pertenecen al usuario
        $count = Documento::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->count();

        if ($count === 0) {
            return response()->json(['success' => false, 'error' => 'Documenti non trovati'], 403);
        }

        try {
            $service = new \App\Services\ChatDocumentService();
            $result  = $service->chat($ids, $message, $historial);

            return response()->json([
                'success'   => true,
                'response'  => $result['response'],
                'meta'      => $result['meta'],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('chatConDocumenti error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AGREGAR ESTE MÉTODO EN DocumentoController.php
     * (después del método buscar() existente, antes de downloadMultiple())
     *
     * Endpoint dedicado para el chat — devuelve datos limpios con fecha
     */
    public function buscarParaChat(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json(['success' => true, 'docs' => []]);
    
        $docs = Documento::with(['proyecto', 'categoria', 'tipologia'])
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhere('categoria_ia', 'like', "%{$q}%")
                    ->orWhere('tipologia_ia', 'like', "%{$q}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
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
    
        return response()->json(['success' => true, 'docs' => $docs]);
    }

}

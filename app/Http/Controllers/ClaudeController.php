<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Proyecto;
use App\Models\Categoria;
use App\Models\Tipologia;
use App\Services\ClaudeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Actividad;


class ClaudeController extends Controller
{
    protected ClaudeService $claude;

    public function __construct(ClaudeService $claude)
    {
        $this->claude = $claude;
    }

    public function analizarYGuardar(Request $request)
    {
        $request->validate([
            'archivos'    => 'required|array|max:10',
            'archivos.*'  => 'file|max:30720',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        $proyecto = Proyecto::findOrFail($request->proyecto_id);
        $user = Auth::user();

        // Verificar acceso al proyecto
        $tieneAccesso = $proyecto->user_id === $user->id;

        if (!$tieneAccesso && $proyecto->team_id) {
            $team = \App\Models\Team::find($proyecto->team_id);
            $tieneAccesso = $team && $team->hasUser($user);

            // Viewers no pueden subir documentos
            if ($tieneAccesso && $team->userRole($user) === 'viewer') {
                return response()->json([
                    'success' => false,
                    'error'   => 'non_autorizzato',
                    'message' => 'I viewer non possono caricare documenti.',
                ], 403);
            }
        }

        if (!$tieneAccesso) {
            return response()->json(['success' => false], 403);
        }

        $resultados = [];

        foreach ($request->file('archivos') as $file) {
            try {
                $path     = $file->store("proyectos/{$proyecto->id}", 'local');
                $mimeType = $file->getMimeType();
                $nombre   = $file->getClientOriginalName();

                $analisis = $this->claude->analizarDocumento($path, $mimeType);

                $categoria = $this->resolverCategoria($analisis['categoria'], $proyecto->id);
                $tipologia = $this->resolverTipologia($analisis['tipologia'], $categoria->id);

                $documento = Documento::create([
                    'proyecto_id'     => $proyecto->id,
                    'categoria_id'    => $categoria->id,
                    'tipologia_id'    => $tipologia->id,
                    'user_id'         => Auth::id(),
                    'nombre'          => $nombre,
                    'archivo'         => $path,
                    'mime_type'       => $mimeType,
                    'descripcion'     => $analisis['descrizione'],
                    'fecha_documento' => $analisis['data_documento'],
                    'fecha_scadenza'  => $analisis['data_scadenza'],
                    'categoria_ia'    => $analisis['categoria'],
                    'tipologia_ia'    => $analisis['tipologia'],
                    'estado'          => $analisis['success'] ? 'completado' : 'error',
                ]);
                
                // Extraer texto del documento
                try {
                    $extractor = new \App\Services\TextExtractorService();
                    $contenidoTexto = $extractor->extract($path, $mimeType);
                    if ($contenidoTexto) {
                        $documento->update(['contenido_texto' => $contenidoTexto]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Estrazione testo fallita', ['error' => $e->getMessage()]);
                }

                Actividad::registrar(Auth::id(), 'documento_caricato', "Caricato: {$documento->nombre}", $proyecto->id);

                $resultados[] = [
                    'success'   => true,
                    'nombre'    => $nombre,
                    'categoria' => $categoria->nombre,
                    'tipologia' => $tipologia->nombre,
                    'estado'    => $documento->estado,
                    'id'        => $documento->id,
                    'mime_type' => $mimeType,
                    'fecha_scadenza' => $analisis['data_scadenza'],
                    'descrizione' => $analisis['descrizione'],
                ];
            } catch (\Exception $e) {
                Log::error('Error procesando archivo', ['error' => $e->getMessage()]);
                $resultados[] = [
                    'success' => false,
                    'nombre'  => $file->getClientOriginalName(),
                    'estado'  => 'error',
                ];
            }
        }

        return response()->json([
            'success'    => true,
            'resultados' => $resultados,
        ]);
    }

    private function resolverCategoria(string $nombre, int $proyectoId): Categoria
    {
        // Intentar encontrar la categoría existente primero
        $existing = Categoria::where('proyecto_id', $proyectoId)
            ->where('nombre', $nombre)
            ->first();

        if ($existing) return $existing;

        // Verificar límite antes de crear
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeCrearCategoria(Auth::user(), $proyectoId)) {
            // Límite alcanzado → usar "Senza Categoria"
            return Categoria::firstOrCreate([
                'proyecto_id' => $proyectoId,
                'nombre'      => 'Senza Categoria',
            ]);
        }

        return Categoria::create([
            'proyecto_id' => $proyectoId,
            'nombre'      => $nombre,
        ]);
    }

    private function resolverTipologia(string $nombre, int $categoriaId): Tipologia
    {
        // Intentar encontrar la tipología existente primero
        $existing = Tipologia::where('categoria_id', $categoriaId)
            ->where('nombre', $nombre)
            ->first();

        if ($existing) return $existing;

        // Verificar límite antes de crear
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeCrearTipologia(Auth::user(), $categoriaId)) {
            // Límite alcanzado → usar "Generale"
            return Tipologia::firstOrCreate([
                'categoria_id' => $categoriaId,
                'nombre'       => 'Generale',
            ]);
        }

        return Tipologia::create([
            'categoria_id' => $categoriaId,
            'nombre'       => $nombre,
        ]);
    }
}

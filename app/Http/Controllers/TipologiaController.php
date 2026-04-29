<?php

namespace App\Http\Controllers;

use App\Models\Tipologia;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Actividad;

class TipologiaController extends Controller
{
    public function index(Categoria $categoria)
    {

        if ($categoria->proyecto->user_id !== Auth::id()) {
            return response()->json([], 403);
        }

        return response()->json($categoria->tipologias);
    }

    public function store(Request $request)
    {
        // Verificar límite de tipologías
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeCrearTipologia(Auth::user(), $request->categoria_id)) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di tipologie per questa categoria.',
            ], 403);
        }

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        if ($categoria->proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $tipologia = Tipologia::create([
            'categoria_id' => $request->categoria_id,
            'nombre'       => $request->nombre,
        ]);

        Actividad::registrar(Auth::id(), 'tipologia_creata', "Creata Tipologia: {$tipologia->nombre}", $categoria->proyecto_id);

        return response()->json([
            'success'   => true,
            'tipologia' => $tipologia,
            'categoria' => $categoria,
        ]);
    }

    public function update(Request $request, Tipologia $tipologia)
    {
        if ($tipologia->categoria->proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tipologia->update(['nombre' => $request->nombre]);

        Actividad::registrar(Auth::id(), 'tipologia_modificata', "Modificata tipologia: {$tipologia->nombre}", $tipologia->categoria->proyecto_id);

        return response()->json([
            'success'   => true,
            'tipologia' => $tipologia,
        ]);
    }

    public function destroy(Tipologia $tipologia)
    {
        if ($tipologia->categoria->proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $nombre     = $tipologia->nombre;
        $proyectoId = $tipologia->categoria->proyecto_id;

        $tipologia->delete();

        Actividad::registrar(Auth::id(), 'tipologia_rimossa', "Rimossa tipologia: {$nombre}", $proyectoId);

        return response()->json(['success' => true]);
    }
}

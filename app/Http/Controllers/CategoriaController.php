<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Actividad;

class CategoriaController extends Controller
{
    public function store(Request $request)
    {
        // Verificar límite de categorías
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeCrearCategoria(Auth::user(), $request->proyecto_id)) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di categorie per questo progetto.',
            ], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        $proyecto = Proyecto::findOrFail($request->proyecto_id);

        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $categoria = Categoria::create([
            'proyecto_id' => $request->proyecto_id,
            'nombre' => $request->nombre,
        ]);

        Actividad::registrar(Auth::id(), 'categoria_creata', "Creata categoria: {$categoria->nombre}", $categoria->proyecto_id);

        return response()->json([
            'success' => true,
            'categoria' => $categoria,
            'proyecto' => $proyecto,
        ]);
    }

    public function index(Proyecto $proyecto)
    {
        $user = Auth::user();

        // Verificar acceso: owner o miembro del team del proyecto
        $tieneAccesso = $proyecto->user_id === $user->id;

        if (!$tieneAccesso && $proyecto->team_id) {
            $team = \App\Models\Team::find($proyecto->team_id);
            $tieneAccesso = $team && $team->hasUser($user);
        }

        if (!$tieneAccesso) {
            return response()->json([], 403);
        }

        return response()->json($proyecto->categorias);
    }

    public function destroy(Categoria $categoria)
    {
        $proyecto = $categoria->proyecto;
        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $categoria->delete();

        Actividad::registrar(Auth::id(), 'categoria_rimossa', "Rimossa categoria: {$categoria->nombre}", $categoria->proyecto_id);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $proyecto = $categoria->proyecto;
        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $categoria->update(['nombre' => $request->nombre]);

        Actividad::registrar(Auth::id(), 'categoria_modificata', "Modificata categoria: {$categoria->nombre}", $categoria->proyecto_id);

        return response()->json([
            'success' => true,
            'categoria' => $categoria,
        ]);
    }
}

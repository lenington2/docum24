<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Actividad;

class ProyectoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si tiene un team activo, mostrar proyectos del team
        if ($user->current_team_id) {
            $team = \App\Models\Team::find($user->current_team_id);
            if ($team && $team->hasUser($user)) {
                $proyectos = $team->proyectos()->withCount('categorias')->get();
                return response()->json($proyectos);
            }
        }

        // Sin team activo: mostrar solo los proyectos propios sin team
        $proyectos = \App\Models\Proyecto::where('user_id', $user->id)
            ->whereNull('team_id')
            ->withCount('categorias')
            ->get();

        return response()->json($proyectos);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Verificar límite de proyectos
        $tokenService = new \App\Services\TokenService();
        if (!$tokenService->puedeCrearProyecto($user)) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di progetti per il tuo piano.',
            ], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $proyecto = \App\Models\Proyecto::create([
            'user_id'     => $user->id,
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'team_id'     => $user->current_team_id ?? null,
        ]);

        Actividad::registrar($user->id, 'progetto_creato', "Creato progetto: {$proyecto->nombre}", $proyecto->id);

        return response()->json([
            'success' => true,
            'proyecto' => $proyecto,
            'message'  => "Progetto <strong>{$proyecto->nombre}</strong> creato con successo!"
        ]);
    }

    public function destroy(Proyecto $proyecto)
    {
        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        $proyecto->delete();

        Actividad::registrar(Auth::id(), 'progetto_rimosso', "Rimosso progetto: {$proyecto->nombre}", $proyecto->id);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        if ($proyecto->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['nombre' => 'required|string|max:255']);
        $proyecto->update(['nombre' => $request->nombre]);

        Actividad::registrar(Auth::id(), 'progetto_modificato', "Modificato progetto: {$proyecto->nombre}", $proyecto->id);

        return response()->json(['success' => true, 'proyecto' => $proyecto]);
    }
}

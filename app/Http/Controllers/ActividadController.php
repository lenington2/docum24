<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $query = Actividad::with('proyecto')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        // Filtro búsqueda texto
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('descripcion', 'like', "%{$q}%")
                    ->orWhere('tipo', 'like', "%{$q}%");
            });
        }

        // Filtro fecha desde
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        // Filtro fecha hasta
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $actividades = $query->limit(100)->get();

        return response()->json($actividades);
    }
}

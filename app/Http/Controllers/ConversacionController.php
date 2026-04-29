<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversacionController extends Controller
{
    // Listar conversaciones del usuario
    public function index()
    {
        $conversaciones = Conversacion::where('user_id', Auth::id())
            ->orderByDesc('ultimo_mensaje_at')
            ->get(['id', 'session_id', 'titulo', 'ultimo_mensaje_at']);

        return response()->json($conversaciones);
    }

    // Ver historial de una conversación
    public function show(Conversacion $conversacion)
    {
        if ($conversacion->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        return response()->json($conversacion);
    }

    // Eliminar
    public function destroy(Conversacion $conversacion)
    {
        if ($conversacion->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        $conversacion->delete();
        return response()->json(['success' => true]);
    }

    // Renombrar título
    public function update(Request $request, Conversacion $conversacion)
    {
        if ($conversacion->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        $request->validate(['titulo' => 'required|string|max:120']);
        $conversacion->update(['titulo' => $request->titulo]);

        return response()->json(['success' => true, 'conversacion' => $conversacion]);
    }
}

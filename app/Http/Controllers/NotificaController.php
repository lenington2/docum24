<?php

namespace App\Http\Controllers;

use App\Models\Notifica;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificaController extends Controller
{
    public function index()
    {
        $notifiche = Notifica::where('user_id', Auth::id())
            ->with('documento')
            ->orderBy('fecha_scadenza')
            ->get();

        return response()->json($notifiche);
    }

    public function store(Request $request)
    {
        $request->validate([
            'documento_id'  => 'required|exists:documentos,id',
            'fecha_scadenza'=> 'required|date',
            'dias_antes'    => 'integer|min:1|max:90',
            'email'         => 'required|email',
        ]);

        $notifica = Notifica::create([
            'user_id'        => Auth::id(),
            'documento_id'   => $request->documento_id,
            'fecha_scadenza' => $request->fecha_scadenza,
            'dias_antes'     => $request->dias_antes ?? 7,
            'email'          => $request->email,
            'estado'         => 'pendiente',
        ]);

        return response()->json(['success' => true, 'notifica' => $notifica]);
    }

    public function destroy(Notifica $notifica)
    {
        if ($notifica->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $notifica->delete();
        return response()->json(['success' => true]);
    }
}

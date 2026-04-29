<?php

namespace App\Http\Controllers;

use App\Models\UrlFavorita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlFavoritaController extends Controller
{
    public function index()
    {
        return response()->json(
            UrlFavorita::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'url'     => 'required|url|max:500',
            'nombre'  => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Max 10 URLs por usuario
        $count = UrlFavorita::where('user_id', Auth::id())->count();
        if ($count >= 10) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di 10 URL salvate.',
            ], 403);
        }

        $url = UrlFavorita::create([
            'user_id'     => Auth::id(),
            'url'         => $request->url,
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return response()->json(['success' => true, 'url' => $url]);
    }

    public function destroy(UrlFavorita $urlFavorita)
    {
        if ($urlFavorita->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $urlFavorita->delete();
        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function show()
    {
        return response()->json(
            Auth::user()->empresa ?? new Empresa()
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre'     => 'nullable|string|max:255',
            'logo'       => 'nullable|image|max:2048',
            'direccion'  => 'nullable|string|max:255',
            'telefono'   => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'website'    => 'nullable|string|max:255',
            'piva'       => 'nullable|string|max:50',
            'descripcion'=> 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'nombre', 'direccion', 'telefono',
            'email', 'website', 'piva', 'descripcion',
        ]);

        // Logo upload
        if ($request->hasFile('logo')) {
            $user = Auth::user();
            // Eliminar logo anterior
            if ($user->empresa?->logo) {
                Storage::disk('public')->delete($user->empresa->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa = Empresa::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json([
            'success' => true,
            'empresa' => $empresa,
            'logo_url' => $empresa->logoUrl(),
        ]);
    }
}

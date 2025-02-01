<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class PerfilViewController extends Controller{


    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Tienda/Perfil', [
            'user' => $user
        ]);
    }
    public function show()
    {
        $user = Auth::user();

        return Inertia::render('Tienda/Perfil', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $user->update([
            ...$request->all(),
            ...$request->validate([
                'name' => ['required', 'string'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'telefono' => ['required', 'string'],
                'direccion_envio' => ['required', 'string'],
                'direccion_facturacion' => ['required', 'string'],
            ])
        ]);

        return redirect()->route('perfil.show');
    }
}

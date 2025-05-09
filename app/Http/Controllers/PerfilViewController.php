<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class PerfilViewController extends Controller
{


    public function index()
    {
        $userId = Auth::user();
        $user = User::find($userId->id)->load('articulos_favoritos.imagenes');
        return Inertia::render('Tienda/Perfil', [
            'user' => $user,
            'categorias' => \App\Models\Categoria::with('subcategorias')->whereNull('categoria_padre_id')->get()
        ]);
    }

    public function show()
    {
        $user = Auth::user();
        $user->load('articulos_favoritos.imagenes');

        return Inertia::render('Tienda/Perfil', [
            'user' => $user,
            'categorias' => \App\Models\Categoria::with('subcategorias')->whereNull('categoria_padre_id')->get()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $user->update([
            ...$request->all(),
            ...$request->validate([
                'name' => ['required', 'string'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'telefono' => ['required', 'digits:9'],
                'direccion_envio' => ['required', 'string'],
                'direccion_facturacion' => ['required', 'string'],
            ])
        ]);

        return redirect()->route('perfil.show');
    }
}

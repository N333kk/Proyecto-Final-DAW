<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Articulo;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if ($user->rol !== 'admin') {
            return redirect()->route('no-auth');
        }

        $articulos = Articulo::select('id', 'nombre', 'descripcion_short', 'precio', 'descuento', 'updated_at')->get();
        $articulosFormateados = $articulos->map(function ($articulo) {
            $categorias = $articulo->categoria;
            $categoriaNombre = count($categorias) > 0 ? $categorias[0]->nombre : 'Sin categoría';

            return [
                'id' => $articulo->id,
                'nombre' => $articulo->nombre,
                'descripcion_short' => $articulo->descripcion_short,
                'precio' => $articulo->precio,
                'descuento' => $articulo->descuento,
                'categoria' => $categoriaNombre,
                'updated_at' => $articulo->updated_at
            ];
        });

        $pedidos = Pedido::all();
        $users = User::all();

        return Inertia::render('Admin/Dashboard', [
            'articulos' => $articulosFormateados,
            'pedidos' => $pedidos,
            'users' => $users
        ]);
    }
}

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

        // Cargar artículos con sus tallas para calcular el stock
        $articulos = Articulo::with(['tallas', 'categoria'])->get();

        $articulosFormateados = $articulos->map(function ($articulo) {
            $categorias = $articulo->categoria;
            $categoriaNombre = count($categorias) > 0 ? $categorias[0]->nombre : 'Sin categoría';

            // Calcular el stock total sumando el stock de todas las tallas
            $stockTotal = $articulo->tallas->sum(function ($talla) {
                return $talla->pivot->stock;
            });

            return [
                'id' => $articulo->id,
                'nombre' => $articulo->nombre,
                'descripcion_short' => $articulo->descripcion_short,
                'precio' => $articulo->precio,
                'descuento' => $articulo->descuento,
                'categoria' => $categoriaNombre,
                'stockTotal' => $stockTotal,
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

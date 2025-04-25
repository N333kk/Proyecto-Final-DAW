<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Articulo;
use App\Models\Categoria;

class TiendaViewController extends Controller
{

    public function index()
    {
        return Inertia::render('Tienda/PrincipalTienda', [
            'articulos' => Articulo::with(['imagenes'])->orderBy('id', 'desc')->take(4)->get(),
            'categorias' => Categoria::with('subcategorias')->whereNull('categoria_padre_id')->get()
        ]);
    }
}

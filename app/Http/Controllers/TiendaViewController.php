<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Articulo;

class TiendaViewController extends Controller
{

    public function index(){
        return Inertia::render('Tienda/PrincipalTienda', [
            'articulos' => Articulo::with(['imagenes'])->orderBy('id','desc')->take(4)->get()
        ]);
    }
}

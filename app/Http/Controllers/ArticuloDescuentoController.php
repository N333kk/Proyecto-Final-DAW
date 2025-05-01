<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticuloDescuentoController extends Controller
{
    /**
     * Actualiza el descuento de un artículo específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articulo  $articulo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Articulo $articulo)
    {
        $validated = $request->validate([
            'descuento' => 'required|integer|min:0|max:100',
        ]);

        $articulo->update([
            'descuento' => $validated['descuento'],
        ]);

        return redirect()->back();
    }
}

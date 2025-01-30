<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ArticuloViewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Admin/ListadoArticulos', [
            'articulos' => Articulo::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/AñadirArticulo');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Articulo::create([
            ...$request->all(),
            ...$request->validate([
                'nombre' => ['required', 'string'],
                'imagen' => ['required', 'string'],
                'categoria' => ['required', 'string'],
                'descripcion' => ['required', 'string'],
                'precio' => ['required', 'min:0', 'max:9999.99', 'numeric']
            ])
        ]);

        return redirect()->route('articulos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Articulo $articulo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articulo;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articulos = Articulo::all();

        return response()->json([
            'articulos' => $articulos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $articulo = new Articulo;

        $articulo->name = $request->input('name');
        $articulo->precio = $request->input('precio');

        $articulo->save();

        return response()->json([
            'message' => 'Articulo añadido correctamente',
            'articulo' => $articulo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $articulo = Articulo::find($id);

        return response()->json([
            'articulo' => $articulo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $articulo = Articulo::find($id);

        $articulo->update($request->all());

        return response()->json([
            'message' => 'Articulo actualizado',
            'articulo' => $articulo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $articulo = Articulo::find($id);

        $articulo->delete();

        return response()->json([
            'message' => 'Articulo eliminado'
        ]);
    }
}

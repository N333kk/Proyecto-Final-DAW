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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $articulo = new Articulo;
        $articulo->nombre = $request->nombre;
        $articulo->descripcion = $request->descripcion;
        $articulo->precio = $request->precio;
        $articulo->save();

        if ($request->categoria_id) {
            $articulo->categoria()->attach($request->categoria_id);
        }

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('articulos', 'gcs');
            $articulo->imagenes()->create(['ruta' => $path]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Articulo añadido correctamente',
                'articulo' => $articulo
            ]);
        }

        return redirect()->route('articulos.index')->with('success', 'Artículo creado correctamente');
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
    public function update(Request $request, $id)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $request->validate($rules);

        $articulo = Articulo::findOrFail($id);

        $articulo->nombre = $request->nombre;
        $articulo->descripcion = $request->descripcion;
        $articulo->precio = $request->precio;

        if ($request->categoria_id) {
            $articulo->categoria()->sync([$request->categoria_id]);
        }

        $articulo->save();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('articulos', 'gcs');
            $articulo->imagenes()->create(['ruta' => $path, 'articulo_id' => $articulo->id]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Artículo actualizado']);
        }

        return redirect()->back()->with('success', 'Artículo actualizado correctamente');
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

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
            'articulos' => Articulo::with(['imagenes', 'categoria'])->get()
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
                'imagen' => ['required', 'image', 'mimes:jpeg,png,jpg,gif|max:2048'],
                'categoria_id' => ['required', 'string'],
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

        $articulo->load('imagenes');
        $articulo->load('categoria');

        return Inertia::render('Tienda/VerArticulo', [
            'articulo' => $articulo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        return Inertia::render('Admin/EditarArticulo', [
            'articulo' => $articulo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $request->validate($rules);

        $articulo->nombre = $request->nombre;
        $articulo->descripcion = $request->descripcion;
        $articulo->precio = $request->precio;

        if ($request->categoria_id) {
            $articulo->categoria()->sync([$request->categoria_id]);
        }

        $articulo->save();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('articulos', 'public');
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
    public function destroy(Articulo $articulo)
    {
        $articulo->delete();
        return redirect()->back()->with('success', 'Articulo eliminado correctamente');
    }
}

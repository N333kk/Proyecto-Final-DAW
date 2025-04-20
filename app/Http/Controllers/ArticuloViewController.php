<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Imagen;
use Illuminate\Support\Facades\Storage;
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
        $validated = $request->validate([
            'nombre' => ['required', 'string'],
            'imagen' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'categoria_id' => ['required', 'string'],
            'descripcion' => ['required', 'string'],
            'precio' => ['required', 'min:0', 'max:9999.99', 'numeric']
        ]);

        $articulo = Articulo::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'categoria_id' => $validated['categoria_id'],
        ]);

        if ($request->categoria_id) {
            $articulo->categoria()->sync([$request->categoria_id]);
        }

        if ($request->hasFile('imagen')) {
            $disk = Storage::disk('gcs');
            $path = $disk->putFile('articulos', $request->file('imagen'));

            $url = $disk->url($path);

            Imagen::create([
                'articulo_id' => $articulo->id,
                'ruta' => $url,
            ]);
        }

        return redirect()->route('articulos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Articulo $articulo)
    {
        $articulo->load('imagenes');
        $articulo->load('categoria');

        $esFavorito = false;

        // Comprobar si el artículo está en favoritos del usuario actual
        if (auth()->check()) {
            $esFavorito = auth()->user()->articulos_favoritos()
                ->where('articulo_id', $articulo->id)
                ->exists();
        }

        return Inertia::render('Tienda/VerArticulo', [
            'articulo' => $articulo,
            'esFavorito' => $esFavorito
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        return Inertia::render('Admin/EditarArticulo', [
            'articulo' => $articulo->load(['imagenes', 'categoria'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'nuevas_imagenes.*' => 'nullable|image|max:2048',
            'imagenes_a_eliminar' => 'nullable|array',
            'imagenes_a_eliminar.*' => 'exists:imagenes,id',
        ]);
        $disk = Storage::disk('gcs');
        // Actualizar datos básicos del artículo
        $articulo->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'categoria_id' => $validated['categoria_id'],
        ]);

        if ($request->categoria_id) {
            $articulo->categoria()->sync([$request->categoria_id]);
        }

        // Eliminar imágenes marcadas para eliminar
        if (isset($validated['imagenes_a_eliminar']) && count($validated['imagenes_a_eliminar']) > 0) {
            foreach ($validated['imagenes_a_eliminar'] as $imagenId) {
                $imagen = Imagen::find($imagenId);
                if ($imagen && $imagen->articulo_id === $articulo->id) {
                    // Eliminar archivo físico
                    $disk->delete($imagen->ruta);
                    // Eliminar registro
                    $imagen->delete();
                }
            }
        }

        // Guardar nuevas imágenes
        if ($request->hasFile('nuevas_imagenes')) {
            foreach ($request->file('nuevas_imagenes') as $file) {
                $path = $disk->putFile('articulos', $file);

                // Generar la URL pública del archivo
                $url = $disk->url($path);

                // Crear nuevo registro de imagen con la URL completa
                Imagen::create([
                    'articulo_id' => $articulo->id,
                    'ruta' => $url,
                ]);
            }
        }

        return redirect()->route('articulos.index');
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

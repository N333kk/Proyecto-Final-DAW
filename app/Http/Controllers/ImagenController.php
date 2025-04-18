<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{

    /**
     * Store a newly created image in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $path = $request->file('imagen')->store('articulos', 'public');

        return response()->json([
            'message' => 'Imagen almacenada correctamente',
            'ruta' => $path
        ]);
    }

    public function destroy($id)
{
    $imagen = Imagen::findOrFail($id);

    Storage::disk('public')->delete($imagen->ruta);

    $imagen->delete();

    return response()->json(['message' => 'Imagen eliminada']);
}
}

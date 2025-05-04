<?php

namespace App\Http\Controllers;

use App\Models\Tallas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TallaController extends Controller
{
    /**
     * Muestra la lista de tallas.
     */
    public function index()
    {
        $tallas = Tallas::all();

        return Inertia::render('Admin/Tallas/Index', [
            'tallas' => $tallas
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva talla.
     */
    public function create()
    {
        return Inertia::render('Admin/Tallas/Create');
    }

    /**
     * Almacena una nueva talla en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'talla' => 'required|string|unique:tallas,talla|max:20',
        ]);

        Tallas::create($validated);

        return redirect()->route('tallas.index')
            ->with('message', 'Talla creada correctamente.');
    }

    /**
     * Muestra el formulario para editar una talla.
     */
    public function edit(Tallas $talla)
    {
        return Inertia::render('Admin/Tallas/Edit', [
            'talla' => $talla
        ]);
    }

    /**
     * Actualiza una talla en la base de datos.
     */
    public function update(Request $request, Tallas $talla)
    {
        $validated = $request->validate([
            'talla' => 'required|string|unique:tallas,talla,' . $talla->id . '|max:20',
        ]);

        $talla->update($validated);

        return redirect()->route('tallas.index')
            ->with('message', 'Talla actualizada correctamente.');
    }

    /**
     * Elimina una talla de la base de datos.
     */
    public function destroy(Tallas $talla)
    {
        // Verificar si la talla está siendo utilizada en algún artículo
        if ($talla->articulos()->count() > 0) {
            return redirect()->route('tallas.index')
                ->with('error', 'No se puede eliminar esta talla porque está siendo utilizada por uno o más artículos.');
        }

        $talla->delete();

        return redirect()->route('tallas.index')
            ->with('message', 'Talla eliminada correctamente.');
    }
}

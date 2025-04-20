<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ArticuloFavorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticuloFavoritoController extends Controller
{
    /**
     * Añade o elimina un artículo de los favoritos del usuario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleFavorito($id)
    {
        $articulo = Articulo::findOrFail($id);
        $user = Auth::user();

        $favorito = ArticuloFavorito::where('user_id', $user->id)
            ->where('articulo_id', $articulo->id)
            ->first();

        if ($favorito) {
            // Si ya está en favoritos, lo eliminamos
            $favorito->delete();
            $mensaje = 'Artículo eliminado de favoritos';
        } else {
            // Si no está en favoritos, lo añadimos
            $favorito = new ArticuloFavorito();
            $favorito->user_id = $user->id;
            $favorito->articulo_id = $articulo->id;
            $favorito->save();
            $mensaje = 'Artículo añadido a favoritos';
        }

        return redirect()->back()->with('message', $mensaje);
    }

    /**
     * Muestra la lista de artículos favoritos del usuario.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        $favoritos = $user->articulos_favoritos()->get();

        return inertia('Favoritos/Index', [
            'favoritos' => $favoritos
        ]);
    }
}

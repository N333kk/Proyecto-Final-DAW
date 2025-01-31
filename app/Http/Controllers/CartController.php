<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Articulo;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $cartItems = CartItem::with('articulo')->where('user_id', $user->id)->get();

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems
        ]);
    }

    public function store($id)
    {
        $user = Auth::user();
        $articulo = Articulo::findOrFail($id);

        $cartItem = CartItem::firstOrCreate(
            ['user_id' => $user->id, 'articulo_id' => $id],
            ['cantidad' => 0, 'precio' => $articulo->precio]
        );

        $cartItem->increment('cantidad', 1);

        return redirect()->route('cart.show');
    }

    public function removeFromCart($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('articulo_id', $id)->where('user_id', $user->id)->firstOrFail();
        $cartItem->delete();

        return redirect()->back()->with('success', 'Artículo eliminado del carrito.');
    }
}

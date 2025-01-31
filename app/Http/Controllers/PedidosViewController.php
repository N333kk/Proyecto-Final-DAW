<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class PedidosViewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with('user')->get();
        $usuariosConPedidos = User::has('pedidos')->get();

        return Inertia::render('Admin/ListadoPedidos', [
            'pedidos' => $pedidos,
            'usuarios' => $usuariosConPedidos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tienda/AñadirPedido');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $pedido = new Pedido();
        $pedido->estado = "Creado";
        $pedido->direccion_envio = Auth::user()->direccion_envio;
        $pedido->user_id = Auth::id();
        $pedido->save();

        $cartItems = CartItem::where('user_id', Auth::id())->get();

        foreach ($cartItems as $cartItem) {
            $pedido->articulos()->attach($cartItem->articulo_id, ['cantidad' => $cartItem->cantidad]);
        }

        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('tienda')->with('success', 'Pedido creado exitosamente.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}

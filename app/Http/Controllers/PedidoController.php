<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::all();

        return response()->json([
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newPedido = new Pedido;

        $newPedido->cliente_id = $request->input('cliente_id');
        $newPedido->cantidad = $request->input('cantidad');

        $newPedido->save();

        return response()->json(
            [
                'message' => 'Pedido añadido correctamente',
                'pedido' => $newPedido
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pedido = Pedido::find($id);

        return response()->json([
            'pedido' => $pedido
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pedido = Pedido::find($id);

        $pedido->update($request->all());

        return response()->json([
            'message' => 'Pedido actualizado',
            'pedido' => $pedido
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pedido = Pedido::find($id);

        $pedido->delete();

        return response()->json([
            'message' => `Pedido con id $id eliminado`
        ]);
    }
}

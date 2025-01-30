<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'usuarios' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newCliente = new User;



        $newCliente->nombre = $request->input('nombre');
        $newCliente->email = $request->input('email');
        $newCliente->telefono = $request->input('telefono');
        $newCliente->direccion_envio = $request->input('direccion_envio');
        $newCliente->direccion_facturacion = $request->input('direccion_facturacion');
        $newCliente->password = bcrypt($request->input('password'));

        $newCliente->save();

        return response()->json([
            'message' => 'Usuario añadido correctamente',
            'cliente' => $newCliente
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = User::find($id);

        return response()->json([
            'cliente' => $cliente
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = User::find($id);

        $cliente->update($request->all());

        return response()->json([
            'message' => 'Cliente actualizado',
            'cliente' => $cliente
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = User::find($id);

        $cliente->delete();

        return response()->json([
            'message' => `Cliente con id $id eliminado`
        ]);
    }
}

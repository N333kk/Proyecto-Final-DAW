<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Categoria;
use App\Models\Tallas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;


class PedidoViewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $pedidos = Pedido::with('user')->get();
        $usuariosConPedidos = User::has('pedidos')->get();

        $filteredPedidos = $pedidos->filter(function ($pedido) use ($user) {
            return Gate::allows('view', $pedido);
        });

        return Inertia::render('Pedidos/ListadoPedidos', [
            'pedidos' => $filteredPedidos,
            'usuarios' => $usuariosConPedidos,
            'categorias' => Categoria::with('subcategorias')->whereNull('categoria_padre_id')->get()
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
        if (Auth::user()->direccion_envio == null) {
            return redirect()->route('perfil')->with('error', 'Tienes que tener una direccion de envio!');
        }
        $pedido = new Pedido();
        $pedido->estado = "Pendiente";
        $pedido->direccion_envio = Auth::user()->direccion_envio;
        $pedido->user_id = Auth::id();
        $pedido->save();

        // Obtener los ítems del carrito del usuario
        $cartItems = CartItem::where('cart_id', function ($query) {
            $query->select('id')
                ->from('carts')
                ->where('user_id', Auth::id());
        })->get();

        foreach ($cartItems as $cartItem) {
            // Calcular precio con descuento si existe
            $precioUnitario = $cartItem->articulo->precio;
            if (!empty($cartItem->articulo->descuento) && $cartItem->articulo->descuento > 0) {
                $precioUnitario = $precioUnitario * (1 - ($cartItem->articulo->descuento / 100));
            }

            // Guardar el artículo en el pedido con su precio y talla
            $pedido->articulos()->attach($cartItem->articulo_id, [
                'cantidad' => $cartItem->cantidad,
                'precio' => round($precioUnitario, 2),
                'talla_id' => $cartItem->talla_id // Guardar el ID de la talla
            ]);
        }

        // Vaciar el carrito del usuario
        CartItem::where('cart_id', function ($query) {
            $query->select('id')
                ->from('carts')
                ->where('user_id', Auth::id());
        })->delete();

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
     * Mostrar los detalles de un pedido específico.
     */
    public function detalle($id)
    {
        // Obtener el pedido por ID
        $pedido = Pedido::findOrFail($id);

        // Verificar que el usuario tiene permiso para ver el pedido
        if (Gate::denies('view', $pedido)) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        // Cargar el pedido con todas las relaciones necesarias
        $pedido->load(['user', 'articulos' => function ($query) {
            $query->with(['imagenes', 'tallas']);
        }]);

        // Transformar la relación pivot para obtener los datos necesarios
        $articulosPedido = $pedido->articulos->map(function ($articulo) {
            // Obtener la talla si existe
            $talla = null;
            if ($articulo->pivot->talla_id) {
                $talla = Tallas::find($articulo->pivot->talla_id);
            }

            return [
                'id' => $articulo->id,
                'articulo' => $articulo,
                'cantidad' => $articulo->pivot->cantidad,
                'precio' => $articulo->pivot->precio ?: $articulo->precio, // Si no hay precio en el pivot, usar el precio actual
                'talla' => $talla ? $talla->talla : null, // Incluir el nombre de la talla
                'talla_id' => $articulo->pivot->talla_id
            ];
        });

        // Asignar los artículos transformados al pedido
        $pedido->articulos_pedido = $articulosPedido;

        return Inertia::render('Pedidos/DetallePedido', [
            'pedido' => $pedido,
            'categorias' => Categoria::with('subcategorias')->whereNull('categoria_padre_id')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        return Inertia::render('Admin/EditarPedido', [
            'pedido' => $pedido
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $pedido->update([
            'direccion_envio' => $request->direccion_envio,
            'estado' => $request->estado,
        ]);

        return redirect()->route('pedidos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->back()->with('success', 'Pedido cancelado correctamente');
    }
}

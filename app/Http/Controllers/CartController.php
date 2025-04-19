<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Pedido;
use App\Models\Articulo;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CartController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);
        $cartItems = CartItem::with(['articulo.imagenes'])->where('cart_id', $cart->id)->get();

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'cart' => $cart
        ]);
    }

    public function store($id)
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);
        $articulo = Articulo::findOrFail($id);

        $cartItem = CartItem::firstOrCreate(
            ['cart_id' => $cart->id, 'articulo_id' => $id],
            ['cantidad' => 0, 'precio' => $articulo->precio]
        );

        $cartItem->increment('cantidad', 1);

        return redirect()->route('cart.show');
    }

    public function update($id)
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);
        $articulo = Articulo::findOrFail($id);

        $cartItem = CartItem::firstOrCreate(
            ['cart_id' => $cart->id, 'articulo_id' => $id],
            ['cantidad' => 0, 'precio' => $articulo->precio]
        );
        if ($cartItem->cantidad > 1) {
            $cartItem->decrement('cantidad', 1);
        } else {
            $cartItem->delete();
        }


        return redirect()->route('cart.show');
    }

    public function removeFromCart($id)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first(); // Added first() to get the cart instance
        $cartItem = CartItem::where('articulo_id', $id)->where('cart_id', $cart->id)->firstOrFail();
        $cartItem->delete();

        return redirect()->back()->with('success', 'Artículo eliminado del carrito.');
    }

    public function clearCart()
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);
        $cartItems = CartItem::where('cart_id', $cart->id)->delete();

        return redirect()->back()->with('success', 'Carrito vaciado.');
    }

    public function checkout()
{
    $user = Auth::user();

    if (empty($user->direccion_envio)) {
        // Devolver un error JSON
        return response()->json(['error' => 'Necesitas añadir una dirección de envío.'], 400);
    }

    $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    $cartItems = CartItem::with('articulo')->where('cart_id', $cart->id)->get();

    if ($cartItems->isEmpty()) {
        // Devolver un error JSON
        return response()->json(['error' => 'El carrito está vacío.'], 400);
    }

    Stripe::setApiKey(config('services.stripe.secret'));
    // Puedes quitar el logger si no lo necesitas para depurar
    // Stripe::setLogger(new \Monolog\Logger('stripe'));

    try {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $this->prepareLineItems($cartItems),
            'mode' => 'payment',
            'success_url' => route('cart.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.checkout.cancel'),
            'customer_email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'cart_id' => $cart->id
            ]
        ]);

        session(['stripe_session_id' => $session->id]);

        // *** ¡Importante! Devolver la URL como JSON ***
        return response()->json(['url' => $session->url]);

    } catch (\Stripe\Exception\ApiErrorException $e) {
        \Log::error('Stripe API Error: ' . $e->getMessage());
        return response()->json(['error' => 'Error de Stripe: ' . $e->getMessage()], 500);
    } catch (\Exception $e) {
        \Log::error('Checkout Error: ' . $e->getMessage());
        return response()->json(['error' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
    }
}
    private function prepareLineItems($cartItems)
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->articulo->nombre,
                        'images' => null,
                        'description' => $item->articulo->descripcion,
                    ],
                    'unit_amount' => $item->precio * 100, // Stripe usa céntimos...
                ],
                'quantity' => $item->cantidad,
            ];
        }

        return $lineItems;
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        $stripeSessionId = session('stripe_session_id');

        // Verificar que el ID de sesión coincide con el almacenado
        if ($sessionId !== $stripeSessionId) {
            return redirect()->route('cart.show')->with('error', 'Error en la validación del pago.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Recuperar la sesión de Stripe
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('cart.show')->with('error', 'El pago no ha sido completado.');
            }

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->first();
            $cartItems = CartItem::with('articulo')->where('cart_id', $cart->id)->get();

            // Crear el pedido
            $pedido = new Pedido();
            $pedido->user_id = $user->id;
            $pedido->estado = 'Pendiente';
            $pedido->direccion_envio = $user->direccion_envio;
            $pedido->save();

            // Añadir los artículos al pedido
            foreach ($cartItems as $item) {
                $pedido->articulos()->attach($item->articulo_id, [
                    'cantidad' => $item->cantidad,
                    'precio' => $item->precio
                ]);
            }

            // Limpiamos el carrito, no usamos la funcion clearCart() para evitar el redirect
            CartItem::where('cart_id', $cart->id)->delete();

            return redirect()->route('pedidos.index')->with('success', 'Pedido realizado con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function checkoutCancel()
    {
        return redirect()->route('cart.show')->with('info', 'Pago cancelado.');
    }
    // TODO: Funciones auxiliares para el checkout
}

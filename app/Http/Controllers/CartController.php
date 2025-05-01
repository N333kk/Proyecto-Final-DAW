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
        $cartItems = CartItem::with(['articulo.imagenes'])->where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            // Devolver un error JSON
            return response()->json(['error' => 'El carrito está vacío.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

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
            // Verificar si el artículo tiene imágenes y crear un array con ellas
            $images = [];
            if (!empty($item->articulo->imagenes) && count($item->articulo->imagenes) > 0) {
                // Usar la URL completa de la imagen
                $images[] = $item->articulo->imagenes[0]->ruta;
            } else {
                // Usar una imagen placeholder si no hay imágenes
                $images[] = asset('images/placeholder.png');
            }

            // Calcular el precio con descuento si existe
            $precioUnitario = $item->articulo->precio;
            $descuentoTexto = "";

            if (!empty($item->articulo->descuento) && $item->articulo->descuento > 0) {
                $precioOriginal = $precioUnitario;
                $precioUnitario = $precioUnitario * (1 - ($item->articulo->descuento / 100));
                $descuentoTexto = " (-{$item->articulo->descuento}% descuento)";
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->articulo->nombre . $descuentoTexto,
                        'images' => $images,
                        'description' => $item->articulo->descripcion_short,
                    ],
                    'unit_amount' => round($precioUnitario * 100), // Stripe usa céntimos
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

            if (!$cart) {
                return redirect()->route('cart.show')->with('error', 'No se encontró el carrito de compras.');
            }

            $cartItems = CartItem::with('articulo')->where('cart_id', $cart->id)->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.show')->with('error', 'El carrito está vacío.');
            }

            // Crear el pedido
            $pedido = new Pedido();
            $pedido->user_id = $user->id;
            $pedido->estado = 'Pendiente';
            $pedido->direccion_envio = $user->direccion_envio;
            $pedido->save();

            // Añadir los artículos al pedido
            foreach ($cartItems as $item) {
                // Calcular el precio con descuento
                $precioUnitario = $item->articulo->precio;

                if (!empty($item->articulo->descuento) && $item->articulo->descuento > 0) {
                    $precioUnitario = $precioUnitario * (1 - ($item->articulo->descuento / 100));
                }

                $pedido->articulos()->attach($item->articulo_id, [
                    'cantidad' => $item->cantidad,
                    'precio' => round($precioUnitario, 2), // Guardamos el precio con descuento ya aplicado
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Limpiamos el carrito - Nos aseguramos de que funcione correctamente
            CartItem::where('cart_id', $cart->id)->delete();

            // Forzamos una regeneración de la sesión para evitar problemas de caché
            $request->session()->regenerate();

            // Log para debugging
            \Log::info('Pedido creado con éxito. ID: ' . $pedido->id);
            \Log::info('Redirigiendo a: ' . route('pedidos.index'));

            // Redirigimos usando el nombre de ruta correcto
            return redirect()->to('/pedidos')->with('success', 'Pedido realizado con éxito.');
        } catch (\Exception $e) {
            \Log::error('Error en checkout success: ' . $e->getMessage());
            return redirect()->route('cart.show')->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function checkoutCancel()
    {
        return redirect()->route('cart.show')->with('info', 'Pago cancelado.');
    }
    // TODO: Funciones auxiliares para el checkout
}

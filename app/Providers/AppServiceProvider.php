<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Cart;
use App\Models\Categoria;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir el conteo de artículos del carrito con todas las vistas de Inertia
        Inertia::share('cartItemsCount', function () {
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
                return $cart ? $cart->cartItems->sum('cantidad') : 0;
            }
            return 0;
        });

        // Compartir las categorías con todas las páginas
        Inertia::share([
            'categorias' => function () {
                return Categoria::with('subcategorias')
                    ->whereNull('categoria_padre_id')
                    ->get();
            },
        ]);
    }
}

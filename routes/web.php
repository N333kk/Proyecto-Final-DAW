<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticuloViewController;
use App\Http\Controllers\TiendaViewController;
use App\Http\Controllers\PedidosViewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PerfilViewController;
use App\Http\Controllers\NoAuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ArticuloFavoritoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::resource('/articulos', ArticuloViewController::class);

Route::resource('/pedidos', PedidosViewController::class);

Route::resource('/perfil', PerfilViewController::class);

Route::get('/no-auth', [NoAuthController::class, 'index'])->name('no-auth');

Route::get('/tienda', function () {
    $controller = new TiendaViewController();
    return $controller->index();
})->name('tienda');

Route::get('/dashboard/{id}/ban', [ClienteController::class, 'ban']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'show'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('cart/checkout/success', [CartController::class, 'checkoutSuccess'])->name('cart.checkout.success');
    Route::get('cart/checkout/cancel', [CartController::class, 'checkoutCancel'])->name('cart.checkout.cancel');
    Route::post('/cart/{id}', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Rutas para favoritos
    Route::post('/favoritos/{id}', [ArticuloFavoritoController::class, 'toggleFavorito'])->name('favoritos.toggle');
    Route::get('/favoritos', [ArticuloFavoritoController::class, 'index'])->name('favoritos.index');
});

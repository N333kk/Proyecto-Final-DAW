<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticuloViewController;
use App\Http\Controllers\TiendaViewController;
use App\Http\Controllers\PedidosViewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NoAuthController;
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

Route::get('/no-auth', [NoAuthController::class, 'index'])->name('no-auth');

Route::get('/tienda', function(){
    $controller = new TiendaViewController();
    return $controller->index();
})->name('tienda');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'show'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/{id}', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'RemoveFromCart'])->name('cart.remove');
});

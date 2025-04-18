<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\PedidosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/falta-per-a/{fecha}', function ($fecha) {
    $fechaObjetivo =    Carbon::parse($fecha);
    $tiempoRestante =    now()->diffForHumans($fechaObjetivo,    ['parts' =>
    3]);

    return response()->json(['message' =>
    "Falten	$tiempoRestante per a $fecha"]);
});

Route::delete('/imagenes/{id}', [ImagenController::class, 'destroy']);

Route::apiResource('/clientes', ClienteController::class);

Route::apiResource('/pedidos', PedidosController::class);

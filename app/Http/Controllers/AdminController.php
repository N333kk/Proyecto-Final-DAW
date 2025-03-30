<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Articulo;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if($user->rol !== 'admin'){
            return redirect()->route('no-auth');
        } else {
            return Inertia::render('Admin/Dashboard', [
                'users' => User::orderBy('updated_at','desc')->get(),
                'articulos' => Articulo::orderBy('updated_at','desc')->get(),
                'pedidos' => Pedido::orderBy('updated_at','desc')->get(),
            ]);
    }
}
}

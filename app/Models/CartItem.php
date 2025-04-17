<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'articulo_id', 'cantidad', 'precio'];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}

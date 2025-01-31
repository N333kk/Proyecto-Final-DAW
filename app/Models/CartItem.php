<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'articulo_id', 'cantidad', 'precio'];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

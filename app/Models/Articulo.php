<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'imagen', 'categoria', 'descripcion', 'precio'];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'articulo_pedido', 'articulo_id');
    }

    public function favByUsers()
    {
        return $this->belongsToMany(User::class, 'articulos_favoritos', 'articulo_id', 'user_id')
        ->withTimestamps();
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }
}

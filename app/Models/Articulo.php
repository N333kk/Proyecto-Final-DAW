<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'categoria', 'descripcion', 'precio'];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'articulo_pedido', 'articulo_id');
    }

    public function favByUsers()
    {
        return $this->belongsToMany(User::class, 'articulos_favoritos', 'articulo_id', 'user_id')
        ->withTimestamps();
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class);
    }

    public function categoria()
    {
        return $this->belongsToMany(Categoria::class, 'articulo_categoria');
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'descripcion_short', 'precio', 'descuento'];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'articulo_pedido', 'articulo_id', 'pedido_id')
            ->withPivot('cantidad', 'precio');
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

    public function tallas()
    {
        return $this->belongsToMany(Tallas::class, 'articulo_tallas', 'articulo_id', 'talla_id')
            ->withPivot('stock');
    }

    public function categoria()
    {
        return $this->belongsToMany(Categoria::class, 'articulo_categoria', 'articulo_id');
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }
}

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
}

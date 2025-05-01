<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloPedido extends Model
{
    protected $table = 'articulo_pedido';

    protected $fillable = ['articulo_id', 'pedido_id', 'cantidad', 'precio'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}

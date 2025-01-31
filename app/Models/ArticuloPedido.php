<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloPedido extends Model
{

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

}

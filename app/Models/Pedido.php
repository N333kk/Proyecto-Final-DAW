<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['direccion_envio', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'articulo_pedido', 'pedido_id', 'articulo_id')
            ->withPivot('cantidad', 'precio');
    }
}

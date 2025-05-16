<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tallas extends Model
{
    protected $table = 'tallas';
    protected $fillable = [
        'talla',
        'stock',
    ];
    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'articulo_tallas', 'talla_id', 'articulo_id');
    }
}

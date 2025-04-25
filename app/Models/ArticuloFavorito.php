<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloFavorito extends Model
{
    protected $fillable = [
        'user_id',
        'articulo_id',
    ];

    protected $table = "articulos_favoritos";
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

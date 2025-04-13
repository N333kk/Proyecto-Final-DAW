<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloFavorito extends Model
{
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

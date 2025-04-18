<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}

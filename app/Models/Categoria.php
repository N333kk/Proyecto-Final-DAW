<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'categoria_padre_id'];

    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre_id');
    }

    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre_id');
    }

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'articulo_categoria');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categoría padre: Ropa
        $ropa = Categoria::create(['nombre' => 'Ropa']);

        Categoria::insert([
            ['nombre' => 'Pantalones', 'categoria_padre_id' => $ropa->id],
            ['nombre' => 'Sudaderas', 'categoria_padre_id' => $ropa->id],
            ['nombre' => 'Camisetas', 'categoria_padre_id' => $ropa->id],
        ]);

        // Categoría padre: Skateboards
        $skate = Categoria::create(['nombre' => 'Skateboards']);

        Categoria::insert([
            ['nombre' => 'Tablas', 'categoria_padre_id' => $skate->id],
            ['nombre' => 'Ejes', 'categoria_padre_id' => $skate->id],
            ['nombre' => 'Ruedas', 'categoria_padre_id' => $skate->id],
            ['nombre' => 'Rodamientos', 'categoria_padre_id' => $skate->id],
        ]);

        // Categoría padre: Accesorios
        $accesorios = Categoria::create(['nombre' => 'Accesorios']);

        Categoria::insert([
            ['nombre' => 'Gorras', 'categoria_padre_id' => $accesorios->id],
            ['nombre' => 'Mochilas', 'categoria_padre_id' => $accesorios->id],
            ['nombre' => 'Calcetines', 'categoria_padre_id' => $accesorios->id],
        ]);
    }
}

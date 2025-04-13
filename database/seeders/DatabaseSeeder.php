<?php

namespace Database\Seeders;

use App\Models\Articulo;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@caveman.com',
            'rol' => 'admin',
            'direccion_envio' => 'Calle Falsa 123',
            'direccion_facturacion' => 'Calle Falsa 123',
            'telefono' => '600000000',
        ]);

        $this->call(UserSeeder::class);
        $this->call(ArticuloSeeder::class);
        $this->call(PedidosSeeder::class);
    }
}

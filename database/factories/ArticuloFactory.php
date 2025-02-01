<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Articulo>
 */
class ArticuloFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'imagen' => $this->faker->imageUrl(),
            'categoria' => $this->faker->randomElement(['Tecnologia', 'Ropa', 'Hogar', 'Comida']),
            'descripcion' => $this->faker->text(),
            'precio' => $this->faker->numberBetween(1,500),
        ];
    }
}

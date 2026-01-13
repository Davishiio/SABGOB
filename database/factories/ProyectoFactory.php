<?php

namespace Database\Factories;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para crear instancias de Proyecto en pruebas.
 * 
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyecto>
 */
class ProyectoFactory extends Factory
{
    protected $model = Proyecto::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'estado' => fake()->randomElement(['pendiente', 'completado']),
            'fecha_inicio' => fake()->dateTimeBetween('now', '+1 month'),
            'fecha_limite' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'idUsuario' => User::factory(),
        ];
    }

    /**
     * Estado: Proyecto pendiente.
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Estado: Proyecto completado.
     */
    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completado',
        ]);
    }
}

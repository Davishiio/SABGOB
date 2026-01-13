<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear usuario Supervisor
        User::firstOrCreate(
            ['email' => 'supervisor@test.com'],
            [
                'name' => 'Supervisor',
                'password' => bcrypt('password'),
                'role' => 'Supervisor',
            ]
        );

        // 2. Crear usuario "Usuario"
        $usuario = User::firstOrCreate(
            ['email' => 'usuario@test.com'],
            [
                'name' => 'Usuario',
                'password' => bcrypt('password'),
                'role' => 'Usuario',
            ]
        );

        // 3. Asignar datos al "Usuario"
        // Crear 1 Proyecto
        $proyecto = \App\Models\Proyecto::create([
            'titulo' => 'Proyecto de Prueba',
            'descripcion' => 'Descripción del proyecto asignado al usuario.',
            'estado' => 'pendiente', // Lowercase and valid value
            'idUsuario' => $usuario->id,
        ]);

        // Crear 3 Tareas para ese proyecto
        for ($i = 1; $i <= 3; $i++) {
            $tarea = \App\Models\Tarea::create([
                'titulo' => "Tarea $i del Proyecto",
                'descripcion' => "Descripción de la tarea $i",
                'estado' => 'pendiente',
                'idProyecto' => $proyecto->id,
            ]);

            // Crear 2 Subtareas para cada tarea
            for ($j = 1; $j <= 2; $j++) {
                \App\Models\Subtarea::create([
                    'titulo' => "Subtarea $j de la Tarea $i",
                    'descripcion' => "Detalle de la subtarea $j",
                    'estado' => 'pendiente',
                    'idTarea' => $tarea->id,
                ]);
            }
        }
    }
}

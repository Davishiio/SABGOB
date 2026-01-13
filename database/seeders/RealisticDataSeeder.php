<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\Subtarea;
use App\Models\Comentario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RealisticDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Limpiar tablas (opcional, para evitar duplicados si se corre varias veces)
        // Schema::disableForeignKeyConstraints();
        // ... truncate ...
        
        // 2. Crear Usuarios con Roles
        $pm = User::updateOrCreate(
            ['email' => 'gerente@sabgob.com'],
            [
                'name' => 'Carla Gerente',
                'password' => Hash::make('password'),
                'role' => 'supervisor' 
            ]
        );

        $dev = User::updateOrCreate(
            ['email' => 'dev@sabgob.com'],
            [
                'name' => 'David Desarrollador',
                'password' => Hash::make('password'),
                'role' => 'usuario'
            ]
        );

        $designer = User::updateOrCreate(
            ['email' => 'diseño@sabgob.com'],
            [
                'name' => 'Ana Diseñadora',
                'password' => Hash::make('password'),
                'role' => 'usuario'
            ]
        );

        // --- PROYECTO 1: Rediseño Sitio Web (Asignado al Dev) ---
        $p1 = Proyecto::create([
            'titulo' => 'Rediseño Portal Corporativo 2026',
            'descripcion' => 'Actualización completa del front-end con React y mejora de performance.',
            'estado' => 'pendiente',
            'idUsuario' => $dev->id, // El dev es el dueño/responsable en este caso
        ]);

        // Comentarios Proyecto 1
        $p1->comments()->create([
            'idUsuario' => $pm->id, // El jefe comenta
            'cuerpo' => 'David, prioriza la versión móvil.',
            'estado' => 'enviado',
            'tipoComentario' => 'Proyecto'
        ]);

        // Tarea 1.1
        $t1 = Tarea::create([
            'titulo' => 'Implementar Design System',
            'descripcion' => 'Crear componentes base (Botones, Inputs, Cards).',
            'estado' => 'pendiente',
            'fecha_limite' => now()->addDays(5),
            'idProyecto' => $p1->id
        ]);

        // Subtareas 1.1
        Subtarea::create(['titulo' => 'Configurar TailwindCSS', 'estado' => 'completado', 'idTarea' => $t1->id]);
        Subtarea::create(['titulo' => 'Crear Botón Primario y Secundario', 'estado' => 'pendiente', 'idTarea' => $t1->id]);
        
        // Comentarios Tarea 1.1
        $t1->comments()->create([
            'idUsuario' => $designer->id,
            'cuerpo' => 'Ya subí los tokens de color a Figma.',
            'estado' => 'enviado',
            'tipoComentario' => 'Tarea'
        ]);

        // Tarea 1.2
        $t2 = Tarea::create([
            'titulo' => 'Integración API de Usuarios',
            'descripcion' => 'Conectar login y perfil de usuario.',
            'estado' => 'pendiente',
            'fecha_limite' => now()->addDays(10),
            'idProyecto' => $p1->id
        ]);


        // --- PROYECTO 2: Campaña de Marketing Q1 (Asignado a Diseñadora) ---
        $p2 = Proyecto::create([
            'titulo' => 'Campaña Redes Sociales Q1',
            'descripcion' => 'Creación de assets gráficos para Instagram y LinkedIn.',
            'estado' => 'pendiente',
            'idUsuario' => $designer->id,
        ]);

        $t2_1 = Tarea::create([
            'titulo' => 'Diseñar Banners LinkedIn',
            'descripcion' => '3 tamaños diferentes para posts y cabecera.',
            'estado' => 'pendiente',
            'idProyecto' => $p2->id
        ]);

        $t2_1->comments()->create([
            'idUsuario' => $pm->id,
            'cuerpo' => 'Usa las fotos del evento de diciembre.',
            'estado' => 'enviado',
            'tipoComentario' => 'Tarea'
        ]);


        // --- PROYECTO 3: Migración Base de Datos (Asignado al Gerente/Supervisor) ---
        $p3 = Proyecto::create([
            'titulo' => 'Migración a PostgreSQL',
            'descripcion' => 'Migrar datos históricos desde MySQL viejo.',
            'estado' => 'completado',
            'idUsuario' => $pm->id,
        ]);

        $t3_1 = Tarea::create([
            'titulo' => 'Backup Inicial',
            'descripcion' => 'Realizar dump completo antes de empezar.',
            'estado' => 'completado',
            'idProyecto' => $p3->id
        ]);
        
        $s3_1 = Subtarea::create(['titulo' => 'Verificar integridad del backup', 'estado' => 'completado', 'idTarea' => $t3_1->id]);

        $s3_1->comments()->create([
            'idUsuario' => $dev->id,
            'cuerpo' => 'Backup verificado, todo ok. SHA256 coincide.',
            'estado' => 'leido',
            'tipoComentario' => 'Subtarea'
        ]);

        $this->command->info('Seed Realista Completado: Usuarios, Proyectos, Tareas, Subtareas y Comentarios creados.');
    }
}

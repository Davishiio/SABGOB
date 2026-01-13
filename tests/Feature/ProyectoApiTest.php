<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para la API de Proyectos.
 * 
 * Verifica el funcionamiento correcto del CRUD de proyectos,
 * incluyendo autenticación y autorización.
 */
class ProyectoApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test_token')->plainTextToken;
    }

    /**
     * Test: Usuario autenticado puede crear un proyecto.
     */
    public function test_usuario_puede_crear_proyecto(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/proyectos', [
                'titulo' => 'Proyecto de Prueba',
                'descripcion' => 'Descripción del proyecto',
                'fecha_inicio' => '2025-01-01',
                'fecha_limite' => '2025-01-31',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'titulo',
                'descripcion',
                'estado',
                'fecha_inicio',
                'fecha_limite',
            ]);

        $this->assertDatabaseHas('proyectos', [
            'titulo' => 'Proyecto de Prueba',
            'idUsuario' => $this->user->id,
        ]);
    }

    /**
     * Test: Usuario autenticado puede listar sus proyectos.
     */
    public function test_usuario_puede_listar_sus_proyectos(): void
    {
        // Crear proyectos para el usuario
        Proyecto::factory()->count(3)->create([
            'idUsuario' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/proyectos');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test: Usuario no puede ver proyectos de otros usuarios.
     */
    public function test_usuario_no_puede_ver_proyectos_ajenos(): void
    {
        // Crear otro usuario con proyecto
        $otroUsuario = User::factory()->create();
        $proyectoAjeno = Proyecto::factory()->create([
            'idUsuario' => $otroUsuario->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/proyectos/' . $proyectoAjeno->id);

        $response->assertStatus(404); // No encontrado para este usuario
    }

    /**
     * Test: Usuario puede actualizar su proyecto.
     */
    public function test_usuario_puede_actualizar_su_proyecto(): void
    {
        $proyecto = Proyecto::factory()->create([
            'idUsuario' => $this->user->id,
            'titulo' => 'Titulo Original',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/proyectos/' . $proyecto->id, [
                'titulo' => 'Titulo Actualizado',
                'estado' => 'completado',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'titulo' => 'Titulo Actualizado',
                'estado' => 'completado',
            ]);
    }

    /**
     * Test: Validación de fecha_limite debe ser posterior a fecha_inicio.
     */
    public function test_validacion_fechas(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/proyectos', [
                'titulo' => 'Proyecto con fechas inválidas',
                'fecha_inicio' => '2025-01-31',
                'fecha_limite' => '2025-01-01', // Antes de fecha_inicio
            ]);

        $response->assertStatus(422) // Validation error
            ->assertJsonValidationErrors(['fecha_limite']);
    }

    /**
     * Test: Acceso sin autenticación es rechazado.
     */
    public function test_acceso_sin_autenticacion_rechazado(): void
    {
        $response = $this->getJson('/api/proyectos');

        $response->assertStatus(401);
    }
}

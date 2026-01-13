<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar Proyectos.
 * 
 * Maneja las operaciones CRUD para proyectos del usuario autenticado.
 * Cada proyecto pertenece a un único usuario y puede contener múltiples tareas.
 */
class ProyectoController extends Controller
{
    // LISTAR (GET /api/proyectos)
    public function index()
    {
        // Usamos la relación 'proyectos' que acabamos de crear en el User
        return Auth::user()->proyectos;
    }

    // CREAR (POST /api/proyectos)
    public function store(Request $request)
    {
        // Validamos los campos en ESPAÑOL
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Creamos el registro usando los nombres de TU base de datos
        $proyecto = Auth::user()->proyectos()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_limite' => $request->fecha_limite,
            'estado' => 'pendiente' // Valor por defecto
        ]);

        return response()->json($proyecto, 201);
    }

    // VER UNO (GET /api/proyectos/{id})
    public function show($id)
    {
        // Buscamos solo entre los proyectos del usuario (Seguridad)
        return Auth::user()->proyectos()->findOrFail($id);
    }

    // ACTUALIZAR (PUT /api/proyectos/{id})
    public function update(Request $request, $id)
    {
        $proyecto = Auth::user()->proyectos()->findOrFail($id);

        $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'in:pendiente,completado',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Actualiza todos los campos que vengan en el request
        $proyecto->update($request->all());

        return response()->json($proyecto);
    }

    // OBTENER DATA COMPLETA (GET /api/proyectos/{id}/completo)
    public function completo($id)
    {
        $proyecto = Auth::user()->proyectos()
            ->with([
                'comments',                     // Comentarios del Proyecto
                'tasks.comments',               // Comentarios de las Tareas
                'tasks.subtareas.comments'      // Comentarios de las Subtareas
            ])
            ->findOrFail($id);

        return response()->json($proyecto);
    }

    // ELIMINAR (DELETE /api/proyectos/{id})
    public function destroy($id)
    {
        $proyecto = Auth::user()->proyectos()->findOrFail($id);
        $proyecto->delete();

        return response()->json(['message' => 'Proyecto eliminado correctamente'], 204);
    }
}

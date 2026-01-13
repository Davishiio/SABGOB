<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    // LISTAR (GET /api/tareas)
    // Opcional: Puede recibir ?idProyecto=1 para filtrar
    public function index(Request $request)
    {
        if ($request->has('idProyecto')) {
            // Verifica que el proyecto sea del usuario
            $proyecto = Auth::user()->proyectos()->find($request->idProyecto);

            if (!$proyecto) {
                return response()->json(['error' => 'Proyecto no encontrado o no autorizado'], 403);
            }
            return $proyecto->tasks; // Usa el nombre de la relación en tu modelo Proyecto
        }

        // Si no envía filtro, devuelve TODAS las tareas de todos los proyectos del usuario
        return Tarea::whereHas('proyecto', function($query) {
            $query->where('idUsuario', Auth::id());
        })->get();
    }

    // LISTAR TAREAS DE UN PROYECTO (GET /api/proyectos/{id}/tareas)
    public function indexByProject(Proyecto $proyecto)
    {
        // Verificar que el proyecto pertenezca al usuario autenticado
        if ($proyecto->idUsuario !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $proyecto->tasks;
    }

    // CREAR (POST /api/tareas)
    public function store(Request $request)
    {
        $request->validate([
            'idProyecto' => 'required|exists:proyectos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // SEGURIDAD: Verificar que el proyecto pertenece al usuario autenticado
        // No basta con que el proyecto exista, debe ser MÍO.
        $proyecto = Auth::user()->proyectos()->find($request->idProyecto);

        if (!$proyecto) {
            return response()->json(['message' => 'No tienes permiso para agregar tareas a este proyecto'], 403);
        }

        // Creamos la tarea vinculada al proyecto
        $tarea = $proyecto->tasks()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_limite' => $request->fecha_limite,
            'estado' => 'pendiente'
        ]);

        return response()->json($tarea, 201);
    }

    // VER UNA (GET /api/tareas/{id})
    public function show($id)
    {
        // Busca la tarea asegurando que el proyecto padre sea del usuario
        $tarea = Tarea::whereHas('proyecto', function($query) {
            $query->where('idUsuario', Auth::id());
        })->findOrFail($id);

        return $tarea;
    }

    // ACTUALIZAR (PUT /api/tareas/{id})
    public function update(Request $request, $id)
    {
        // Buscamos con seguridad
        $tarea = Tarea::whereHas('proyecto', function($query) {
            $query->where('idUsuario', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'in:pendiente,completado',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $tarea->update($request->all());

        return response()->json($tarea);
    }

    // ELIMINAR (DELETE /api/tareas/{id})
    public function destroy($id)
    {
        $tarea = Tarea::whereHas('proyecto', function($query) {
            $query->where('idUsuario', Auth::id());
        })->findOrFail($id);

        $tarea->delete();
        // Si hay subtareas, se borrarán en cascada por la BD

        return response()->json(null, 204);
    }
}

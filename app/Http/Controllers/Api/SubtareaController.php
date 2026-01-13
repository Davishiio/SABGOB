<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subtarea;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubtareaController extends Controller
{
    // LISTAR SUBTAREAS DE UNA TAREA (GET /api/tareas/{id}/subtareas)
    public function indexByTask(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca a un proyecto del usuario
        $esPropia = Tarea::where('id', $tarea->id)
            ->whereHas('proyecto', function($query) {
                $query->where('idUsuario', Auth::id());
            })->exists();

        if (!$esPropia) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $tarea->subtareas;
    }

    // CREAR (POST /api/subtareas)
    public function store(Request $request)
    {
        $request->validate([
            'idTarea' => 'required|exists:tareas,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        // SEGURIDAD: Verificar que la Tarea pertenece a un Proyecto del Usuario
        $tarea = Tarea::whereHas('proyecto', function($q) {
            $q->where('idUsuario', Auth::id());
        })->find($request->idTarea);

        if (!$tarea) {
            return response()->json(['message' => 'No tienes permiso para agregar subtareas aquí'], 403);
        }

        $subtarea = $tarea->subtareas()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => 'pendiente'
        ]);

        return response()->json($subtarea, 201);
    }

    // ACTUALIZAR (PUT /api/subtareas/{id})
    public function update(Request $request, $id)
    {
        // Validación de propiedad en cadena (Subtarea->Tarea->Proyecto->Usuario)
        $subtarea = Subtarea::whereHas('tarea.proyecto', function($q) {
            $q->where('idUsuario', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'in:pendiente,completado'
        ]);

        $subtarea->update($request->all());

        return response()->json($subtarea);
    }

    // ELIMINAR (DELETE /api/subtareas/{id})
    public function destroy($id)
    {
        $subtarea = Subtarea::whereHas('tarea.proyecto', function($q) {
            $q->where('idUsuario', Auth::id());
        })->findOrFail($id);

        $subtarea->delete();

        return response()->json(null, 204);
    }
}

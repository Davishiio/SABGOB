<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\Subtarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar Comentarios.
 * 
 * Maneja las operaciones CRUD para comentarios polimórficos.
 * Los comentarios pueden asociarse a Proyectos, Tareas o Subtareas.
 */
class ComentarioController extends Controller
{
    // LISTAR MIS COMENTARIOS (GET /api/comentarios)
    public function index()
    {
        // Retorna todos los comentarios hechos por el usuario autenticado
        return Comentario::where('idUsuario', Auth::id())->get();
    }

    // AGREGAR COMENTARIO (POST /api/comentarios)
    public function store(Request $request)
    {
        $request->validate([
            'cuerpo' => 'required|string',
            'tipo' => 'required|in:Proyecto,Tarea,Subtarea',
            'id_referencia' => 'required|integer',
        ]);

        // Mapear el tipo a la Clase del Modelo (usando el MorphMap de Laravel)
        // Relation::getMorphedModel($request->tipo) devolvería "App\Models\Proyecto"
        $claseModelo = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($request->tipo);

        if (!$claseModelo) {
            return response()->json(['error' => 'Tipo inválido'], 400);
        }

        // Verificar que la entidad existe
        $entidad = $claseModelo::find($request->id_referencia);

        if (!$entidad) {
            return response()->json(['error' => 'Entidad no encontrada'], 404);
        }

        // Crear el comentario usando la relación polimórfica
        // Esto automáticamente usa el Morph Map configurado ('Proyecto', 'Tarea', etc.)
        $comentario = $entidad->comments()->create([
            'idUsuario' => Auth::id(),
            'cuerpo' => $request->cuerpo,
            // 'estado' es 'enviado' por defecto
        ]);

        return response()->json($comentario, 201);
    }

    // ELIMINAR COMENTARIO (DELETE /api/comentarios/{id})
    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);

        // SOLO EL DUEÑO PUEDE ELIMINAR SU COMENTARIO
        if ($comentario->idUsuario !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para eliminar este comentario'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado'], 200);
    }

    // VER UN COMENTARIO (GET /api/comentarios/{id})
    public function show($id)
    {
        return Comentario::findOrFail($id);
    }

    // EDITAR COMENTARIO (PUT /api/comentarios/{id})
    public function update(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        // SOLO EL DUEÑO PUEDE EDITAR SU COMENTARIO
        if ($comentario->idUsuario !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para editar este comentario'], 403);
        }

        $request->validate([
            'cuerpo' => 'required|string',
        ]);

        $comentario->update([
            'cuerpo' => $request->cuerpo
        ]);

        return response()->json($comentario);
    }
}

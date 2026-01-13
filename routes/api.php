<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// Importamos tus controladores nuevos
use App\Http\Controllers\Api\ProyectoController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\SubtareaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Rutas Públicas (No requieren Token) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- Rutas Protegidas (Requieren Token Bearer) ---
Route::middleware('auth:sanctum')->group(function () {

    // Obtener usuario actual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- CRUDs del Sistema Jerárquico ---

    // 1. Proyectos (Nivel Superior)
    // Genera: GET, POST, PUT, DELETE para /api/proyectos
    Route::apiResource('proyectos', ProyectoController::class);

    // 2. Tareas (Nivel Intermedio)
    // Genera: GET, POST, PUT, DELETE para /api/tareas
    Route::apiResource('tareas', TareaController::class);

    // 3. Subtareas (Nivel Inferior)
    Route::apiResource('subtareas', SubtareaController::class);

    // --- Rutas Anidadas para Filtrado ---
    Route::get('/proyectos/{proyecto}/tareas', [TareaController::class, 'indexByProject']);
    Route::get('/tareas/{tarea}/subtareas', [SubtareaController::class, 'indexByTask']);

    // Ruta para obtener proyecto completo (con tareas, subtareas y comentarios)
    Route::get('/proyectos/{id}/completo', [ProyectoController::class, 'completo']);

    // --- COMENTARIOS ---
    Route::get('/comentarios', [App\Http\Controllers\Api\ComentarioController::class, 'index']); // Nueva ruta index
    Route::post('/comentarios', [App\Http\Controllers\Api\ComentarioController::class, 'store']);
    Route::get('/comentarios/{id}', [App\Http\Controllers\Api\ComentarioController::class, 'show']);
    Route::put('/comentarios/{id}', [App\Http\Controllers\Api\ComentarioController::class, 'update']);
    Route::delete('/comentarios/{id}', [App\Http\Controllers\Api\ComentarioController::class, 'destroy']);


});

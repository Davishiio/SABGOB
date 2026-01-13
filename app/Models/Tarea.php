<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas'; // Nombre explícito de la tabla

    protected $fillable = ['idProyecto', 'titulo', 'descripcion', 'estado'];

    //Relación: Una Tarea pertenece a un Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'idProyecto');
    }

    // Relación: Una Tarea tiene muchas Subtareas
    public function subtareas()
    {
        return $this->hasMany(Subtarea::class, 'idTarea');
    }

    // Polimórfica para comentarios (RF-1)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

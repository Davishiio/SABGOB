<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtarea extends Model
{
    use HasFactory;

    protected $table = 'subtareas'; // Tabla explícita

    protected $fillable = ['idTarea', 'titulo', 'descripcion', 'estado'];

    // Relación: Pertenece a una Tarea
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'idTarea');
    }

    // Relación: Comentarios (RF-1)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

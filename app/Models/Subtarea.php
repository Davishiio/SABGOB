<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtarea extends Model
{
    use HasFactory;

    protected $table = 'subtareas'; // Tabla explícita

    protected $fillable = ['idTarea', 'titulo', 'descripcion', 'estado'];

    protected $hidden = ['created_at', 'updated_at'];

    // Relación: Pertenece a una Tarea
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'idTarea');
    }

    // Relación: Comentarios (RF-1)
    protected $appends = ['has_comments'];

    // Relación: Comentarios (RF-1)
    public function comments()
    {
        return $this->morphMany(Comentario::class, 'comentable', 'tipoComentario', 'idComentario');
    }

    public function getHasCommentsAttribute()
    {
        return $this->comments()->exists();
    }
}

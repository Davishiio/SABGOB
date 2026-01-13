<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas'; // Nombre explícito de la tabla

    protected $fillable = ['idProyecto', 'titulo', 'descripcion', 'estado', 'fecha_inicio', 'fecha_limite'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite' => 'date',
    ];

    protected $hidden = ['created_at', 'updated_at'];

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
    protected $appends = ['has_comments'];

    // Polimórfica para comentarios (RF-1)
    public function comments()
    {
        return $this->morphMany(Comentario::class, 'comentable', 'tipoComentario', 'idComentario');
    }

    public function getHasCommentsAttribute()
    {
        return $this->comments()->exists();
    }
}

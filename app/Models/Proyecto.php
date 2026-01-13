<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'estado', 'idUsuario'];

    protected $hidden = ['created_at', 'updated_at'];

    // Relación: Pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Tiene muchas Tareas (Si se borra un proyecto, se borran las tareas gracias al cascade de la BD)
    public function tasks()
    {
        return $this->hasMany(Tarea::class, 'idProyecto');
    }

    // Relación: Comentarios (Polimórfica)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    // Relación: Un usuario tiene muchos proyectos
    public function proyectos()
    {
        // 1. Modelo: Proyecto
        // 2. Llave foránea en la tabla proyectos: 'idUsuario'
        return $this->hasMany(Proyecto::class, 'idUsuario');
    }
}

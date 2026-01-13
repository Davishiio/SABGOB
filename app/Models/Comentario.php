<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['idUsuario', 'cuerpo', 'idComentario', 'tipoComentario', 'estado'];

    // Relación polimórfica inversa
    public function comentable()
    {
        return $this->morphTo(null, 'tipoComentario', 'idComentario');
    }
}

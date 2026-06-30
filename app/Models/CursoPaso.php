<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoPaso extends Model
{
    protected $table = 'curso_pasos';

    protected $fillable = [
        'curso_id',
        'paso_numero',
        'porcentaje',
        'nombre',
        'documento_url',
        'documento_nombre',
        'documento_tipo',
        'documento_tamano'
    ];

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }
}
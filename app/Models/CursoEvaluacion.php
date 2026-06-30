<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoEvaluacion extends Model
{
    protected $table = 'curso_evaluaciones';

    protected $fillable = [
        'curso_id',
        'tipo_evaluacion',
        'nombre',
        'url',
        'drive_url'
    ];

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }
}
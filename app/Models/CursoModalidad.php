<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoModalidad extends Model
{
    protected $table = 'curso_modalidades';
    public $timestamps = false;

    protected $fillable = [
        'curso_id',
        'modalidad'
    ];

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }
}
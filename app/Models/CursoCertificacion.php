<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoCertificacion extends Model
{
    protected $table = 'curso_certificaciones';

    protected $fillable = [
        'curso_id',
        'tipo_certificacion',
        'tiene_firma',
        'fecha_registro',
        'nombre',
        'url',
        'drive_url'
    ];

    protected $casts = [
        'tiene_firma' => 'boolean'
    ];

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }
}
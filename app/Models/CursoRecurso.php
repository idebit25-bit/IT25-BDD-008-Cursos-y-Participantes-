<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoRecurso extends Model
{
    protected $table = 'curso_recursos';

    protected $fillable = [
        'curso_id',
        'tipo_recurso',
        'nombre',
        'url',
        'drive_url',
        'descripcion'
    ];

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }

    public function getArchivoPublicoUrlAttribute(): ?string
    {
        if (empty($this->url)) {
            return null;
        }

        if (str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://')) {
            return $this->url;
        }

        $relative = preg_replace('#^storage/#', '', ltrim($this->url, '/'));

        if (preg_match('#^cursos/(\d+)/(.+)$#', $relative, $matches)) {
            return route('cursos.archivo', [
                'curso' => $matches[1],
                'filename' => $matches[2],
            ]);
        }

        return asset('storage/' . $relative);
    }
}
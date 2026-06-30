<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cursos extends Model
{
    protected $fillable = [
        'parent_id',
        'nomenclatura',
        'nombre',
        'descripcion',
        'costo',
        'instructor_responsable',
        'fecha_inicio',
        'fecha_termino',
        'modalidad',
        'sin_fecha',
        'status',
        'duracion',
    ];

    // Relaciones
    public function subcursos()
    {
        return $this->hasMany(Cursos::class, 'parent_id');
    }

    public function cursoPadre()
    {
        return $this->belongsTo(Cursos::class, 'parent_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'curso_id');
    }

    public function participantes()
    {
        return $this->belongsToMany(
            Participantes::class,
            'inscripciones',
            'curso_id',
            'participante_id'
        );
    }

    // Relaciones con las nuevas tablas
    public function modalidades()
    {
        return $this->hasMany(CursoModalidad::class, 'curso_id');
    }

    public function recursos()
    {
        return $this->hasMany(CursoRecurso::class, 'curso_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(CursoEvaluacion::class, 'curso_id');
    }

    public function certificaciones()
    {
        return $this->hasMany(CursoCertificacion::class, 'curso_id');
    }

    public function pasos()
    {
        return $this->hasMany(CursoPaso::class, 'curso_id');
    }

    // Accessors para mantener compatibilidad con las vistas
    public function getNomenclaturaAttribute($value) { return $value ?? ''; }
    public function getNombredelCursoAttribute($value) { return $this->attributes['nombre'] ?? ''; }
    public function getDescripciondeCursoAttribute($value) { return $this->attributes['descripcion'] ?? ''; }
    public function getCostodelCursoAttribute($value) { return $this->attributes['costo'] ?? ''; }
    public function getInstructorResponsableAttribute($value) { return $this->attributes['instructor_responsable'] ?? ''; }
    public function getFechadeInicioAttribute($value) { return $this->attributes['fecha_inicio'] ?? ''; }
    public function getFechadeTerminoAttribute($value) { return $this->attributes['fecha_termino'] ?? ''; }
    public function getDuracioncursoAttribute($value) { return $this->attributes['duracion'] ?? ''; }

    // Accessor para modalidad (para las vistas)
    public function getModalidadTextoAttribute()
    {
        $map = [
            'virtual' => 'Virtual',
            'presencial' => 'Presencial',
            'mixto' => 'Mixto'
        ];
        return $map[$this->modalidad] ?? 'No definida';
    }

    public function getEstatusProgresoAttribute()
    {
        $campos = [
            'nomenclatura', 'nombre', 'descripcion', 'costo', 
            'instructor_responsable', 'fecha_inicio', 'fecha_termino', 
            'duracion', 'modalidad'
        ];

        $llenos = 0;
        foreach ($campos as $campo) {
            if (!empty($this->attributes[$campo])) $llenos++;
        }

        $porcentaje = (count($campos) > 0) ? ($llenos / count($campos)) * 100 : 0;

        if ($porcentaje == 100) {
            return ['texto' => 'Completado', 'color' => 'bg-success'];
        } elseif ($porcentaje >= 30) {
            return ['texto' => 'En progreso', 'color' => 'bg-warning'];
        } else {
            return ['texto' => 'Pendiente de actualización', 'color' => 'bg-danger'];
        }
    }

    protected $dates = [
        'fecha_inicio',
        'fecha_termino',
    ];

    // Método auxiliar para obtener recurso por tipo
    public function getRecurso($tipo)
    {
        return $this->recursos()->where('tipo_recurso', $tipo)->first();
    }

    // Método auxiliar para obtener evaluación por tipo
    public function getEvaluacion($tipo)
    {
        return $this->evaluaciones()->where('tipo_evaluacion', $tipo)->first();
    }

    // Método auxiliar para obtener certificación por tipo
    public function getCertificacion($tipo)
    {
        return $this->certificaciones()->where('tipo_certificacion', $tipo)->first();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CourseActionLog extends Model
{
    protected $fillable = [
        'curso_id',
        'nombre_curso',
        'user_id',
        'accion',
        'detalles',
        'fecha_accion',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el curso
    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }

    protected $casts = [
        'fecha_accion' => 'datetime', // Asegúrate de que se maneje como una instancia de Carbon
    ];

    public function getFechaAccionAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/Mexico_City')->format('h:i A');
    }
}

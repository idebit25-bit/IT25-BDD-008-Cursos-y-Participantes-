<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $participante_id
 * @property int $curso_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\cursos $curso
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\participantes $participante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCursoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereParticipanteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Inscripcion extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'inscripciones';

    // Desactivar timestamps si no usas las columnas created_at y updated_at
    public $timestamps = false;

    // Definir las relaciones
    public function participante()
    {
        return $this->belongsTo(Participantes::class, 'participante_id');
    }

    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'curso_id');
    }
}

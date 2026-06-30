<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'nombre_postulante',
        'correo',
        'accion',
        'user_id',
        'detalles',
        'fecha_accion',
    ];

    // Relación con el usuario que realizó la acción
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el participante afectado
    public function participant()
    {
        return $this->belongsTo(Participantes::class, 'participant_id');
    }
}

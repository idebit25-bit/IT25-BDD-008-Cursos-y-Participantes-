<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaLocal extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'rutas_locales';

    // Desactivar timestamps si no se utilizan created_at y updated_at
    public $timestamps = true;

    // Columnas que se pueden llenar masivamente (fillable)
    protected $fillable = [
        'id_cursos',
        'nombre_carpeta',
        'ruta_nombre_carpeta',
        'rutacompleta',
        'rutaformatosflyer',
        'rutaSinFecha',
        'rutaFacebook',
        'rutaLinkedIn',
        'rutaInstagram',
        'rutaTemario',
        'rutaItinerario',
        'rutaPlaneacion',
        'rutaMaterialdeapoyo',
        'rutacursoenlinea',
        'rutapresentacion',
        'rutaevaluaciones',
        'rutaEvaluacionDiagnostica',
        'rutaEvaluacionSatisfaccion',
        'rutaEvaluacionFinal',
        'rutaDC5',
        'rutacarpetaDC5',
        'rutaCertificadoComprobacion',
        'rutacartapoder',
        'rutaUdemy',
    ];

    // Relación con el modelo Curso
    public function curso()
    {
        return $this->belongsTo(Cursos::class, 'id_cursos', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

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

    // ============================================
    // BORRADORES DE CREACIÓN
    // ============================================
    // Al iniciar un curso o subcurso se crea un registro "TEMP-..." que recibe su nomenclatura
    // real en el paso 1. No es un curso todavía: no se lista ni se ofrece para inscripción.
    public const PREFIJO_BORRADOR = 'TEMP-';
    private const HORAS_VIDA_BORRADOR = 24;

    public function scopeSinBorradores($query)
    {
        return $query->where('nomenclatura', 'not like', self::PREFIJO_BORRADOR . '%');
    }

    public function esBorrador(): bool
    {
        return str_starts_with((string) $this->nomenclatura, self::PREFIJO_BORRADOR);
    }

    // Nomenclatura sugerida para el siguiente subcurso: ID-A20 → ID-A20.1, ID-A20.2, ...
    public function siguienteNomenclaturaSubcurso(): string
    {
        $prefijo = $this->nomenclatura . '.';

        $ultimo = $this->subcursos()->pluck('nomenclatura')
            ->filter(fn ($nomenclatura) => str_starts_with($nomenclatura, $prefijo))
            ->map(fn ($nomenclatura) => substr($nomenclatura, strlen($prefijo)))
            ->filter(fn ($consecutivo) => ctype_digit($consecutivo))
            ->map(fn ($consecutivo) => (int) $consecutivo)
            ->max() ?? 0;

        // Por si el número ya lo ocupa otro curso (capturado a mano), se toma el siguiente libre
        do {
            $sugerida = $prefijo . ++$ultimo;
        } while (static::where('nomenclatura', $sugerida)->exists());

        return $sugerida;
    }

    // Borra el borrador junto con los archivos que alcanzó a subir (las tablas relacionadas caen en cascada)
    public function eliminarBorrador(): void
    {
        File::deleteDirectory(storage_path('app/public/cursos/' . $this->id));
        $this->delete();
    }

    // Borradores que alguien dejó a medias (cerró la pestaña sin terminar ni cancelar)
    public static function purgarBorradoresAbandonados(): int
    {
        $abandonados = static::where('nomenclatura', 'like', self::PREFIJO_BORRADOR . '%')
            ->where('created_at', '<', now()->subHours(self::HORAS_VIDA_BORRADOR))
            ->get();

        $abandonados->each->eliminarBorrador();

        return $abandonados->count();
    }

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

    // ============================================
    // DOCUMENTACIÓN STPS COMPARTIDA (PASO 7)
    // ============================================
    // Un subcurso usa la documentación STPS de su curso inicial cuando éste ya tiene
    // el alta (fecha de registro STPS) y el subcurso no capturó datos propios.
    // UDEMY no se comparte: cada subcurso lo captura por separado.
    public const STPS_CERTIFICACIONES = ['fecha_registro', 'dc5', 'certificado_comprobacion', 'carta_poder'];
    public const STPS_RECURSOS = ['dc5_archivo', 'certificado_archivo', 'carta_poder_archivo'];

    public function tieneAltaStps(): bool
    {
        return $this->certificaciones()
            ->where('tipo_certificacion', 'fecha_registro')
            ->whereNotNull('fecha_registro')
            ->exists();
    }

    // Al guardar el paso 7 se crean filas aunque estén vacías (p. ej. tiene_firma = false),
    // por eso se revisa que haya algún valor capturado y no sólo que exista la fila.
    public function tieneStpsPropio(): bool
    {
        $conDatos = $this->certificaciones()
            ->whereIn('tipo_certificacion', self::STPS_CERTIFICACIONES)
            ->get()
            ->contains(function ($certificacion) {
                return !empty($certificacion->fecha_registro)
                    || !empty($certificacion->nombre)
                    || !empty($certificacion->drive_url)
                    || !empty($certificacion->tiene_firma);
            });

        return $conDatos || $this->recursos()
            ->whereIn('tipo_recurso', self::STPS_RECURSOS)
            ->whereNotNull('url')
            ->exists();
    }

    public function heredaStps(): bool
    {
        return !is_null($this->parent_id)
            && $this->cursoPadre
            && $this->cursoPadre->tieneAltaStps()
            && !$this->tieneStpsPropio();
    }

    // Curso del que se toman los datos STPS: el curso inicial si se heredan, o el propio
    public function cursoStps(): Cursos
    {
        return $this->heredaStps() ? $this->cursoPadre : $this;
    }

    // ============================================
    // ESTADO DC3 AUTOMÁTICO (PASO 6)
    // ============================================
    // Se calcula con la documentación STPS del paso 7 (heredada en subcursos).
    // "No se entrega" es la única decisión manual: cursos que no otorgan DC3.
    public const DC3_SE_ENTREGA = 'Se entrega DC3';
    public const DC3_PENDIENTE = 'Entrega pendiente de DC3';
    public const DC3_NO_SE_ENTREGA = 'No se entrega DC3';

    public function estadoDc3(): array
    {
        $origen = $this->cursoStps();
        $certificaciones = $origen->certificaciones()
            ->whereIn('tipo_certificacion', self::STPS_CERTIFICACIONES)
            ->get()
            ->keyBy('tipo_certificacion');

        $requisitos = [
            'Fecha de registro STPS' => !empty($certificaciones->get('fecha_registro')?->fecha_registro),
            'Formato DC5 firmado' => (bool) $certificaciones->get('dc5')?->tiene_firma,
            'Certificado de comprobación obtenido' => $certificaciones->get('certificado_comprobacion')?->nombre === 'Ya obtenida',
            'Carta poder firmada' => (bool) $certificaciones->get('carta_poder')?->tiene_firma,
        ];

        $noOtorga = $this->certificaciones()
            ->where('tipo_certificacion', 'dc3')
            ->where('nombre', self::DC3_NO_SE_ENTREGA)
            ->exists();

        if ($noOtorga) {
            $estado = self::DC3_NO_SE_ENTREGA;
        } elseif (!in_array(false, $requisitos, true)) {
            $estado = self::DC3_SE_ENTREGA;
        } else {
            $estado = self::DC3_PENDIENTE;
        }

        return [
            'estado' => $estado,
            'requisitos' => $requisitos,
            'faltantes' => array_keys(array_filter($requisitos, fn ($cumplido) => !$cumplido)),
            'noOtorga' => $noOtorga,
            'heredadoDe' => $origen->is($this) ? null : $origen,
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string|null $N
 * @property string $NombredelPostulante
 * @property string $Correo
 * @property string $Telefono
 * @property string $Edad
 * @property string $Direccion
 * @property string $Escolaridad
 * @property string $Curp
 * @property string $RazónSocial
 * @property string $Empresa
 * @property string $RFCEmpresa
 * @property string $Puesto
 * @property string $Pago
 * @property string $EstadoDePago
 * @property string $FechadelCurso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\cursos> $cursos
 * @property-read int|null $cursos_count
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereCurp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereEdad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereEscolaridad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereEstadoDePago($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereFechadelCurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereN($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereNombredelPostulante($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes wherePago($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes wherePuesto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereRFCEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereRazónSocial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|participantes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Participantes extends Model
{
    use HasFactory;

    protected $table = 'participantes';

    protected $primaryKey = 'id';
    public $incrementing = true; // Asegura que el ID sea autoincremental


    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'edad',
        'direccion',
        'escolaridad',
        'curp',
        'razon_social',
        'empresa',
        'rfc_empresa',
        'puesto',
        'ocupacion',
        'pago',
        'estado_pago',
        'fecha_curso',
        'estatus',
        'N'
    ];

    /**
     * Mutator para el estado de pago.
     * Asegura que el valor esté dentro de los permitidos.
     */
    public function setEstadoPagoAttribute($value)
    {
        $allowedValues = ['Pendiente', 'Pagado', 'Anticipo', 'Cancelado'];

        if (in_array($value, $allowedValues)) {
            $this->attributes['estado_pago'] = $value;

            // Lógica adicional: si es cancelado, el pago es 0
            if ($value === 'Cancelado') {
                $this->attributes['pago'] = 0;
            }
        } else {
            // Valor por defecto más seguro
            $this->attributes['estado_pago'] = 'Pendiente';
        }
    }

    /**
     * Mutator alternativo para soportar CamelCase si es necesario.
     */
    public function setEstadoDePagoAttribute($value)
    {
        $this->setEstadoPagoAttribute($value);
    }

    // Accessors para mantener compatibilidad con la vista
    public function getNombredelPostulanteAttribute() { return $this->attributes['nombre'] ?? ''; }
    public function getCorreoAttribute() { return $this->attributes['correo'] ?? ''; }
    public function getTelefonoAttribute() { return $this->attributes['telefono'] ?? ''; }
    public function getEdadAttribute() { return $this->attributes['edad'] ?? ''; }
    public function getDireccionAttribute() { return $this->attributes['direccion'] ?? ''; }
    public function getEscolaridadAttribute() { return $this->attributes['escolaridad'] ?? ''; }
    public function getCurpAttribute() { return $this->attributes['curp'] ?? ''; }
    public function getRazónSocialAttribute() { return $this->attributes['razon_social'] ?? ''; }
    public function getEmpresaAttribute() { return $this->attributes['empresa'] ?? ''; }
    public function getRFCEmpresaAttribute() { return $this->attributes['rfc_empresa'] ?? ''; }
    public function getPuestoAttribute() { return $this->attributes['puesto'] ?? ''; }
    public function getOcupacionAttribute() { return $this->attributes['ocupacion'] ?? ''; }
    
    /**
     * Accessor para EstadoDePago (usado en vistas).
     */
    public function getEstadoDePagoAttribute() 
    { 
        return $this->attributes['estado_pago'] ?? 'Pendiente'; 
    }

    public function getFechadelCursoAttribute() { return $this->attributes['fecha_curso'] ?? ''; }
    public function getNAttribute() { return $this->attributes['N'] ?? $this->attributes['id'] ?? ''; }
    public function getPagoAttribute() { return $this->attributes['pago'] ?? 0; }

    /**
     * Calcula el costo total sumando el costo de cada curso en el que está inscrito.
     */
    public function getTotalACobrarAttribute()
    {
        return $this->cursos->sum('costo');
    }

    // Mutator para convertir CURP a mayúsculas automáticamente
    public function setCurpAttribute($value)
    {
        $this->attributes['curp'] = strtoupper($value);
    }

    public function cursos()
    {
        return $this->belongsToMany(
            Cursos::class,         // Modelo relacionado
            'inscripciones',       // Tabla intermedia
            'participante_id',     // Clave foránea en la tabla intermedia para participantes
            'curso_id'             // Clave foránea en la tabla intermedia para cursos
        );
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $apellido
 * @property int|null $edad
 * @property string|null $telefono
 * @property string $email
 * @property string|null $puesto
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEdad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePlainPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePuesto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido',
        'email',
        'password',
        'puesto',
        'telefono',
        'edad',
        'estatus'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============================================
    // CONTRASEÑAS
    // ============================================
    // Misma regla para recuperación, alta y edición de usuarios
    public const TEXTO_REGLA_CONTRASENA = 'Mínimo 8 caracteres, con al menos una letra y un número.';

    public static function reglasContrasena(): array
    {
        return ['string', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/', 'confirmed'];
    }

    public static function mensajesContrasena(): array
    {
        return [
            'password.required' => 'Escribe la nueva contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una letra y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    // Tras cambiar la contraseña nadie debe seguir dentro con una sesión anterior
    // (sesiones guardadas en la tabla "sessions" y cookies de "recordarme")
    public function cerrarSesiones(?string $exceptoSesion = null): void
    {
        DB::table('sessions')->where('user_id', $this->id)
            ->when($exceptoSesion, fn ($q) => $q->where('id', '!=', $exceptoSesion))
            ->delete();
        $this->setRememberToken(Str::random(60));
    }
}

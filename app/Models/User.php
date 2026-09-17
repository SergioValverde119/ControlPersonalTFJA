<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Role;


/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $activo
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'email',
    'password',
    'persona_id',
    'activo',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones de Roles y Autorización
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string ...$claves): bool
    {
        return $this->roles->pluck('clave')->intersect($claves)->isNotEmpty();
    }

    public function hasAnyRole(array|string ...$claves): bool
    {
        $roles = is_array($claves[0] ?? null) ? $claves[0] : $claves;

        return $this->roles->pluck('clave')->intersect($roles)->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones de Adscripción Territorial
    |--------------------------------------------------------------------------
    */

    // Relación con la identidad civil perenne
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Titularidades o encargadurías que ejerce en el grafo organizacional
    public function titularidades()
    {
        return $this->hasMany(Titularidad::class);
    }

    // Titularidad activa actual
    public function titularidadActiva()
    {
        return $this->hasOne(Titularidad::class)->where('activo', true);
    }

    // Firmas pendientes asignadas a este usuario
    public function firmasPendientes()
    {
        return $this->hasMany(TramiteFirma::class, 'firmante_user_id')
                    ->where('estatus', 'PENDIENTE');
    }

}

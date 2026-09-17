<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plaza extends Model
{
    protected $table = 'plazas';

    protected $fillable = [
        'codigo_plaza',
        'unidad_organizacional_id',
        'puesto_id',
        'estatus',
        'disponible_desde',
    ];

    protected function casts(): array
    {
        return [
            'disponible_desde' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Negocio
    |--------------------------------------------------------------------------
    */

    /**
     * Filtra las vacantes adscritas estrictamente a la oficina base del usuario.
     */
    public function scopeVacantesParaUsuario(Builder $query, User $user): Builder
    {
        $query->where('estatus', 'VACANTE');

        if ($user->hasRole('ADMIN_DGTIC')) {
            return $query;
        }

        $unidadId = $user->titularidadActiva?->unidad_organizacional_id;

        if (! $unidadId) {
            return $query->whereRaw('1 = 0');
        }

        // Coincidencia estricta: solo su oficina directa
        return $query->where('unidad_organizacional_id', $unidadId);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class);
    }

    public function tramitesDestino(): HasMany
    {
        return $this->hasMany(Tramite::class, 'plaza_destino_id');
    }

    public function tramitesOrigen(): HasMany
    {
        return $this->hasMany(Tramite::class, 'plaza_origen_id');
    }
}

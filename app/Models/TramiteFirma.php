<?php

// app/Models/TramiteFirma.php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteFirma extends Model
{
    protected $table = 'tramite_firmas';

    protected $fillable = [
        'tramite_id',
        'orden',
        'etiqueta_rol',
        'unidad_organizacional_id',
        'firmante_user_id',
        'estatus',
        'motivo_devolucion',
        'firmado_en',
    ];

    protected function casts(): array
    {
        return [
            'orden'      => 'integer',
            'firmado_en' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Negocio
    |--------------------------------------------------------------------------
    */

    /**
     * Filtra únicamente las firmas pendientes que ya pueden ser atendidas
     * (sin firmas previas pendientes o rechazadas en la cadena).
     */
    public function scopeTurnoActivo(Builder $query, User $user): Builder
    {
        return $query->where('firmante_user_id', $user->id)
            ->where('estatus', 'PENDIENTE')
            ->whereHas('tramite.estatus', fn (Builder $q) => $q->where('clave', 'AUTORIZACION_JERARQUICA'))
            ->whereNotExists(function ($subquery) {
                $subquery->selectRaw(1)
                    ->from('tramite_firmas as tf_previa')
                    ->whereColumn('tf_previa.tramite_id', 'tramite_firmas.tramite_id')
                    ->whereColumn('tf_previa.orden', '<', 'tramite_firmas.orden')
                    ->where('tf_previa.estatus', '!=', 'AUTORIZADO');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id');
    }

    public function firmante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'firmante_user_id');
    }
}

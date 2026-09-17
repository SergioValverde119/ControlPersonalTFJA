<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tramite extends Model
{
    protected $table = 'tramites';

    protected $fillable = [
        'folio',
        'tipo_movimiento',
        'persona_id',
        'plaza_destino_id',
        'plaza_origen_id',
        'usuario_solicitante_id',
        'estatus_id',
        'corte_jga_id',
        'fecha_efectos_propuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_efectos_propuesta' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Negocio
    |--------------------------------------------------------------------------
    */

    /**
     * Filtra los trámites visibles para el usuario en sesión:
     * 1. Administrador DGTIC ve todos los trámites.
     * 2. El usuario ve los trámites que él mismo inició como solicitante.
     * 3. El usuario ve los trámites donde interviene o intervino como firmante.
     * 4. El usuario ve los trámites destinados a su propia oficina de adscripción.
     */
    public function scopeVisiblesParaUsuario(Builder $query, User $user): Builder
    {
        if ($user->hasRole('ADMIN_DGTIC')) {
            return $query;
        }

        $unidadId = $user->titularidadActiva?->unidad_organizacional_id;

        return $query->where(function (Builder $q) use ($user, $unidadId) {
            // Trámites capturados directamente por el usuario
            $q->where('usuario_solicitante_id', $user->id);

            // Trámites donde participa en la escalera de firmas
            $q->orWhereHas('firmas', fn (Builder $f) => $f->where('firmante_user_id', $user->id));

            // Trámites dirigidos a plazas de su propia oficina
            if ($unidadId) {
                $q->orWhereHas('plazaDestino', fn (Builder $p) => $p->where('unidad_organizacional_id', $unidadId));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function plazaDestino(): BelongsTo
    {
        return $this->belongsTo(Plaza::class, 'plaza_destino_id');
    }

    public function plazaOrigen(): BelongsTo
    {
        return $this->belongsTo(Plaza::class, 'plaza_origen_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(CatalogoEstatusTramite::class, 'estatus_id');
    }

    public function corteJga(): BelongsTo
    {
        return $this->belongsTo(CorteJga::class, 'corte_jga_id');
    }

    public function firmas(): HasMany
    {
        return $this->hasMany(TramiteFirma::class)->orderBy('orden');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(TramiteDocumento::class);
    }

    public function revisionesRubros(): HasMany
    {
        return $this->hasMany(TramiteRevisionRubro::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(TramiteEvaluacion::class);
    }

    public function resolucionJga(): HasOne
    {
        return $this->hasOne(TramiteResolucionJga::class);
    }
}

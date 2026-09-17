<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function plazaDestino()
    {
        return $this->belongsTo(Plaza::class, 'plaza_destino_id');
    }

    public function plazaOrigen()
    {
        return $this->belongsTo(Plaza::class, 'plaza_origen_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function estatus()
    {
        return $this->belongsTo(CatalogoEstatusTramite::class, 'estatus_id');
    }

    public function corteJga()
    {
        return $this->belongsTo(CorteJga::class, 'corte_jga_id');
    }

    public function firmas()
    {
        return $this->hasMany(TramiteFirma::class)->orderBy('orden');
    }

    public function documentos()
    {
        return $this->hasMany(TramiteDocumento::class);
    }

    public function revisionesRubros()
    {
        return $this->hasMany(TramiteRevisionRubro::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(TramiteEvaluacion::class);
    }

    public function resolucionJga()
    {
        return $this->hasOne(TramiteResolucionJga::class);
    }
}
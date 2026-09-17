<?php

// app/Models/TramiteFirma.php
namespace App\Models;

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
            'orden' => 'integer',
            'firmado_en' => 'datetime',
        ];
    }

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
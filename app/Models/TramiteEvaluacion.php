<?php

// app/Models/TramiteEvaluacion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteEvaluacion extends Model
{
    protected $table = 'tramite_evaluaciones';

    protected $fillable = [
        'tramite_id',
        'evaluador_user_id',
        'tipo_evaluacion',
        'puntaje',
        'resultado',
        'reporte_archivo_path',
        'observaciones',
        'evaluado_en',
    ];

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
            'evaluado_en' => 'datetime',
        ];
    }

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluador_user_id');
    }
}
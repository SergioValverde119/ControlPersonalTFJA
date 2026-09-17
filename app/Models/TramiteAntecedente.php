<?php

// app/Models/TramiteAntecedente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteAntecedente extends Model
{
    protected $table = 'tramite_antecedentes';

    protected $fillable = [
        'tramite_id',
        'tipo_validacion',
        'unidad_cedente_id',
        'titular_evaluador_user_id',
        'resultado',
        'oficio_delegado_path',
        'comentarios_recomendacion',
        'respondido_en',
    ];

    protected function casts(): array
    {
        return [
            'respondido_en' => 'datetime',
        ];
    }

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function unidadCedente(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_cedente_id');
    }

    public function titularEvaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'titular_evaluador_user_id');
    }
}
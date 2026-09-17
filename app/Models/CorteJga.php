<?php

// app/Models/CorteJga.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorteJga extends Model
{
    protected $table = 'cortes_jga';

    protected $fillable = [
        'numero_corte',
        'fecha_cierre',
        'fecha_sesion_jga',
        'estatus',
        'concentrado_excel_path',
        'concentrado_pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cierre' => 'date',
            'fecha_sesion_jga' => 'date',
        ];
    }

    public function tramites(): HasMany
    {
        return $this->hasMany(Tramite::class, 'corte_jga_id');
    }

    public function resoluciones(): HasMany
    {
        return $this->hasMany(TramiteResolucionJga::class, 'corte_jga_id');
    }
}
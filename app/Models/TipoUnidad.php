<?php

// app/Models/TipoUnidad.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoUnidad extends Model
{
    protected $table = 'tipos_unidad';

    protected $fillable = [
        'clave',
        'nombre',
        'escala_inmediato',
    ];

    protected function casts(): array
    {
        return [
            'escala_inmediato' => 'boolean',
        ];
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(UnidadOrganizacional::class, 'tipo_unidad_id');
    }
}
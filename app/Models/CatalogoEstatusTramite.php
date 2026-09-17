<?php

// app/Models/CatalogoEstatusTramite.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoEstatusTramite extends Model
{
    protected $table = 'catalogo_estatus_tramites';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'badge_color',
        'orden_flujo',
        'permite_edicion',
        'es_terminal',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'orden_flujo' => 'integer',
            'permite_edicion' => 'boolean',
            'es_terminal' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function tramites(): HasMany
    {
        return $this->hasMany(Tramite::class, 'estatus_id');
    }
}
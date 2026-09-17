<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Titularidad extends Model
{
    protected $table = 'titularidades';

    protected $fillable = [
        'unidad_organizacional_id',
        'user_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
            'activo'       => 'boolean',
        ];
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_organizacional_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

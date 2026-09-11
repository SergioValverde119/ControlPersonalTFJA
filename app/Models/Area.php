<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'sala_id',
        'clave',
        'nombre',
        'tipo',
        'numero',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    /**
     * Sala a la que pertenece el área u órgano resolutor.
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    /**
     * Personal adscrito formalmente a esta ponencia o área común.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers y Lógica Institucional
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el área corresponde a una Ponencia jurisdiccional (1, 2 o 3).
     */
    public function esPonencia(): bool
    {
        return $this->tipo === 'PONENCIA';
    }
}

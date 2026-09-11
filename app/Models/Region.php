<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $table = 'regiones';

    protected $fillable = [
        'clave',
        'nombre',
        'sede',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    /**
     * Salas regionales que pertenecen a esta circunscripción.
     */
    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class);
    }

    /**
     * Personal adscrito a esta región territorial.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

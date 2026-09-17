<?php

// app/Models/Puesto.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puesto extends Model
{
    protected $table = 'puestos';

    protected $fillable = [
        'clave',
        'nombre',
        'nivel_tabular',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function plazas(): HasMany
    {
        return $this->hasMany(Plaza::class);
    }
}
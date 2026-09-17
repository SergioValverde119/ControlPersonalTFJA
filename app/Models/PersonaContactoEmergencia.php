<?php

// app/Models/PersonaContactoEmergencia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaContactoEmergencia extends Model
{
    protected $table = 'persona_contactos_emergencia';

    protected $fillable = [
        'persona_id',
        'parentesco',
        'nombre_completo',
        'telefono',
        'telefono_alterno',
        'direccion_completa',
        'es_beneficiario',
    ];

    protected function casts(): array
    {
        return [
            'es_beneficiario' => 'boolean',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
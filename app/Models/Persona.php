<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'curp',
        'rfc',
        'nombre',
        'primer_apellido',
        'segundo_apellido',
        'telefono',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function documentos()
    {
        return $this->hasMany(PersonaDocumento::class);
    }

    public function contactosEmergencia()
    {
        return $this->hasMany(PersonaContactoEmergencia::class);
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    // Nombre completo formateado
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->primer_apellido} {$this->segundo_apellido}");
    }
}
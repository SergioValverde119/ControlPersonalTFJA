<?php

// app/Models/PersonaDocumento.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaDocumento extends Model
{
    protected $table = 'persona_documentos';

    protected $fillable = [
        'persona_id',
        'tipo_documento_id',
        'archivo_path',
        'hash_sha256',
        'descripcion_adicional',
        'vigencia_fin',
        'validado_por_rh',
    ];

    protected function casts(): array
    {
        return [
            'vigencia_fin' => 'date',
            'validado_por_rh' => 'boolean',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(CatalogoTipoDocumento::class, 'tipo_documento_id');
    }
}
<?php

// app/Models/TramiteDocumento.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TramiteDocumento extends Model
{
    protected $table = 'tramite_documentos';

    protected $fillable = [
        'tramite_id',
        'tipo_documento_id',
        'archivo_path',
        'nombre_original',
        'hash_sha256',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(CatalogoTipoDocumento::class, 'tipo_documento_id');
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(TramiteRevisionRubro::class, 'tramite_documento_id');
    }
}
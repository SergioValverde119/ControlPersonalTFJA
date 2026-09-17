<?php

// app/Models/TramiteRevisionRubro.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteRevisionRubro extends Model
{
    protected $table = 'tramite_revision_rubros';

    protected $fillable = [
        'tramite_id',
        'revisor_user_id',
        'carril_origen',
        'tipo_rubro',
        'clave_rubro',
        'tramite_documento_id',
        'es_valido',
        'observacion',
        'subsanado',
        'subsanado_en',
    ];

    protected function casts(): array
    {
        return [
            'es_valido' => 'boolean',
            'subsanado' => 'boolean',
            'subsanado_en' => 'datetime',
        ];
    }

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_user_id');
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(TramiteDocumento::class, 'tramite_documento_id');
    }
}
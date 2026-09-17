<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoTipoDocumento extends Model
{
    protected $table = 'catalogo_tipos_documento';

    protected $fillable = [
        'clave',
        'nombre',
        'alcance',
        'es_obligatorio',
        'requiere_vigencia',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'es_obligatorio'    => 'boolean',
            'requiere_vigencia' => 'boolean',
            'activo'            => 'boolean',
        ];
    }

    public function personaDocumentos(): HasMany
    {
        return $this->hasMany(PersonaDocumento::class, 'tipo_documento_id');
    }

    public function tramiteDocumentos(): HasMany
    {
        return $this->hasMany(TramiteDocumento::class, 'tipo_documento_id');
    }
}
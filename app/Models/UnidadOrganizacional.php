<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadOrganizacional extends Model
{
    protected $table = 'unidades_organizacionales';

    protected $fillable = [
        'tipo_unidad_id',
        'padre_id',
        'path',
        'clave',
        'nombre',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function tipo()
    {
        return $this->belongsTo(TipoUnidad::class, 'tipo_unidad_id');
    }

    public function padre()
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(UnidadOrganizacional::class, 'padre_id');
    }

    public function titularidades()
    {
        return $this->hasMany(Titularidad::class);
    }

    // Resuelve quién es el titular o encargado activo en este momento exacto
    public function titularActual()
    {
        return $this->hasOne(Titularidad::class)
                    ->where('activo', true)
                    ->latest();
    }

    // Método helper para obtener la colección de ancestros usando el path en 1 sola consulta
    public function getAncestros()
    {
        $ids = array_filter(explode('/', trim($this->path, '/')));
        
        return self::with(['tipo', 'titularActual.user'])
            ->whereIn('id', $ids)
            ->orderByRaw('LENGTH(path) DESC')
            ->get();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    protected $fillable = [
        'region_id',
        'clave',
        'nombre',
        'tipo',
        'magistrado_visitador',
        'magistrado_visitador_id',
        'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Scope para filtrar únicamente las ponencias de la Sala
     */
    public function ponencias(): HasMany
    {
        return $this->hasMany(Area::class)->where('tipo', 'PONENCIA');
    }

    public function magistradoVisitador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'magistrado_visitador_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

<?php
// app/Models/TramiteResolucionJga.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteResolucionJga extends Model
{
    protected $table = 'tramite_resoluciones_jga';

    protected $fillable = [
        'tramite_id',
        'corte_jga_id',
        'sentido',
        'numero_acuerdo',
        'fecha_acuerdo',
        'oficio_resolucion_path',
        'sincronizado_sigpagk',
        'sincronizado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha_acuerdo' => 'date',
            'sincronizado_sigpagk' => 'boolean',
            'sincronizado_en' => 'datetime',
        ];
    }

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function corteJga(): BelongsTo
    {
        return $this->belongsTo(CorteJga::class, 'corte_jga_id');
    }
}
<?php

namespace App\Actions\Workflow;

use App\Models\CatalogoEstatusTramite;
use App\Models\Tramite;
use App\Models\TramiteFirma;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class ProcesarFirmaAction
{
    /**
     * Procesa la firma del turno actual, aplica candados y actualiza el estado del trámite.
     *
     * @throws DomainException
     */
    public function ejecutar(
        TramiteFirma $firma,
        string $decision,
        User $firmante,
        ?string $motivo = null
    ): Tramite {
        return DB::transaction(function () use ($firma, $decision, $firmante, $motivo) {
            $tramite = $firma->tramite;

            // 1. REGLAS DE TURNO Y ESTADO
            if ($firma->estatus !== 'PENDIENTE') {
                throw new DomainException("Este turno de firma ya fue procesado previamente ({$firma->estatus}).");
            }

            // Validar que no existan firmas previas sin autorizar
            $tieneFirmasPreviasIncompletas = $tramite->firmas()
                ->where('orden', '<', $firma->orden)
                ->where('estatus', '!=', 'AUTORIZADO')
                ->exists();

            if ($tieneFirmasPreviasIncompletas) {
                throw new DomainException("No es posible firmar: existen turnos jerárquicos previos sin concluir.");
            }

            // 2. CASO: RECHAZO (Venia denegada o negativa jerárquica)
            if ($decision === 'RECHAZADO') {
                $firma->update([
                    'estatus'           => 'RECHAZADO',
                    'motivo_devolucion' => $motivo,
                    'firmado_en'        => now(),
                ]);

                // Cancelar todos los turnos posteriores en la cadena
                $tramite->firmas()
                    ->where('orden', '>', $firma->orden)
                    ->update(['estatus' => 'CANCELADO']);

                // Cancelación definitiva del trámite desde el origen
                $estatusRechazado = CatalogoEstatusTramite::where('clave', 'RECHAZADO')->firstOrFail();
                $tramite->update(['estatus_id' => $estatusRechazado->id]);

                return $tramite->fresh(['estatus', 'firmas']);
            }

            // 3. CASO: AUTORIZACIÓN
            $firma->update([
                'estatus'           => 'AUTORIZADO',
                'motivo_devolucion' => $motivo,
                'firmado_en'        => now(),
            ]);

            // Comprobar si restan firmas pendientes posteriores
            $faltanFirmas = $tramite->firmas()
                ->where('orden', '>', $firma->orden)
                ->where('estatus', 'PENDIENTE')
                ->exists();

            // Si ya concluyó la escalera de firmas, transita a Fase 2 (Mesa Técnica de RRHH)
            if (! $faltanFirmas) {
                $estatusRh = CatalogoEstatusTramite::where('clave', 'MESA_TECNICA_RH')->firstOrFail();
                $tramite->update(['estatus_id' => $estatusRh->id]);
            }

            return $tramite->fresh(['estatus', 'firmas']);
        });
    }
}

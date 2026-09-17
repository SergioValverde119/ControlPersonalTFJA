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
    public const DECISION_APROBAR = 'APROBADO';
    public const DECISION_DEVOLVER = 'DEVUELTO_OBSERVADO';

    /**
     * Procesa la firma del turno actual, valida la precedencia secuencial
     * y transiciona el estatus general del trámite.
     *
     * @param  TramiteFirma  $firma       Registro de la firma en turno
     * @param  User          $firmante    Usuario autenticado que ejecuta la acción
     * @param  string        $decision    'APROBADO' o 'DEVUELTO_OBSERVADO'
     * @param  string|null   $motivo      Obligatorio si la decisión es devolución
     * @return Tramite
     *
     * @throws DomainException
     */
    public function ejecutar(
        TramiteFirma $firma,
        User $firmante,
        string $decision,
        ?string $motivo = null
    ): Tramite {
        return DB::transaction(function () use ($firma, $firmante, $decision, $motivo) {
            $tramite = $firma->tramite()->lockForUpdate()->firstOrFail();

            // 1. Candado de Identidad: El firmante debe coincidir con el asignado
            if ($firma->firmante_user_id !== $firmante->id) {
                throw new DomainException(
                    "Acceso denegado: este turno de firma corresponde a otro servidor público."
                );
            }

            // 2. Candado de Estado: La firma no debe haber sido procesada previamente
            if ($firma->estatus !== 'PENDIENTE') {
                throw new DomainException(
                    "Este turno ya fue resuelto previamente con estado: {$firma->estatus}."
                );
            }

            // 3. Candado Secuencial: No se puede firmar si existen turnos previos sin aprobar
            $pendientesPrevios = $tramite->firmas()
                ->where('orden', '<', $firma->orden)
                ->where('estatus', '!=', self::DECISION_APROBAR)
                ->exists();

            if ($pendientesPrevios) {
                throw new DomainException(
                    "Violación de orden de prelación: aún existen firmas jerárquicas previas pendientes."
                );
            }

            // 4. Procesar según la decisión
            if ($decision === self::DECISION_DEVOLVER) {
                return $this->procesarDevolucion($firma, $tramite, $motivo);
            }

            if ($decision === self::DECISION_APROBAR) {
                return $this->procesarAprobacion($firma, $tramite);
            }

            throw new DomainException("Decisión inválida: {$decision}. Valores permitidos: APROBADO, DEVUELTO_OBSERVADO.");
        });
    }

    /**
     * Aplica la devolución con observaciones y regresa el trámite a estado OBSERVADO.
     */
    protected function procesarDevolucion(TramiteFirma $firma, Tramite $tramite, ?string $motivo): Tramite
    {
        if (empty(trim((string) $motivo))) {
            throw new DomainException(
                "Para devolver o rechazar un turno con observaciones, es obligatorio detallar el motivo."
            );
        }

        $firma->update([
            'estatus'           => self::DECISION_DEVOLVER,
            'motivo_devolucion' => trim($motivo),
            'firmado_en'        => now(),
        ]);

        $estatusObservado = CatalogoEstatusTramite::where('clave', 'OBSERVADO')->firstOrFail();
        $tramite->update(['estatus_id' => $estatusObservado->id]);

        return $tramite->fresh(['estatus', 'firmas']);
    }

    /**
     * Aplica el visto bueno y transiciona al siguiente turno o a Mesa Técnica de RH.
     */
    protected function procesarAprobacion(TramiteFirma $firma, Tramite $tramite): Tramite
    {
        $firma->update([
            'estatus'           => self::DECISION_APROBAR,
            'motivo_devolucion' => null,
            'firmado_en'        => now(),
        ]);

        // Buscar si existe un siguiente turno de firma pendiente
        $siguienteFirma = $tramite->firmas()
            ->where('orden', '>', $firma->orden)
            ->where('estatus', 'PENDIENTE')
            ->orderBy('orden')
            ->first();

        if ($siguienteFirma) {
            // El estatus depende de si el siguiente es Visitador Regional o Presidencia
            $claveEstatus = $siguienteFirma->etiqueta_rol === 'MAGISTRADO_VISITADOR'
                ? 'DICTAMEN_REGIONAL'
                : 'AUTORIZACION_JERARQUICA';
        } else {
            // Se completó la cadena de firmas; pasa a revisión normativa central (Carril 6)
            $claveEstatus = 'MESA_TECNICA_RH';
        }

        $nuevoEstatus = CatalogoEstatusTramite::where('clave', $claveEstatus)->firstOrFail();
        $tramite->update(['estatus_id' => $nuevoEstatus->id]);

        return $tramite->fresh(['estatus', 'firmas']);
    }
}

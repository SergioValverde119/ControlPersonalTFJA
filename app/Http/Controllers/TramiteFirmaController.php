<?php

namespace App\Http\Controllers;

use App\Actions\Workflow\ProcesarFirmaAction;
use App\Http\Requests\ProcesarFirmaRequest;
use App\Models\TramiteFirma;
use DomainException;
use Illuminate\Http\JsonResponse;

class TramiteFirmaController extends Controller
{
    /**
     * Procesa el turno de firma (Aprobación o Devolución con observaciones).
     */
    public function procesar(
        ProcesarFirmaRequest $request,
        TramiteFirma $firma,
        ProcesarFirmaAction $action
    ): JsonResponse {
        try {
            $tramite = $action->ejecutar(
                firma: $firma,
                firmante: $request->user(),
                decision: $request->validated('decision'),
                motivo: $request->validated('motivo')
            );

            $esAprobacion = $request->validated('decision') === ProcesarFirmaAction::DECISION_APROBAR;

            return response()->json([
                'status'  => 'success',
                'mensaje' => $esAprobacion
                    ? 'Firma / Vo.Bo. registrado correctamente.'
                    : 'El trámite ha sido devuelto con observaciones.',
                'tramite' => [
                    'id'      => $tramite->id,
                    'folio'   => $tramite->folio,
                    'estatus' => $tramite->estatus->only(['clave', 'nombre', 'badge_color']),
                    'firmas'  => $tramite->firmas->map(fn ($f) => [
                        'orden'             => $f->orden,
                        'rol'               => $f->etiqueta_rol,
                        'estatus'           => $f->estatus,
                        'firmante'          => $f->firmante->name,
                        'firmado_en'        => $f->firmado_en?->toIso8601String(),
                        'motivo_devolucion' => $f->motivo_devolucion,
                    ]),
                ],
            ], 200);

        } catch (DomainException $e) {
            return response()->json([
                'status'  => 'error',
                'error'   => 'Regla de negocio no satisfecha',
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }
}

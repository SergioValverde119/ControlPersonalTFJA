<?php

namespace App\Http\Controllers;

use App\Actions\Workflow\ProcesarFirmaAction;
use App\Http\Requests\ProcesarFirmaRequest;
use App\Models\TramiteFirma;
use Illuminate\Http\RedirectResponse;

class FirmaTramiteController extends Controller
{
    public function update(
        ProcesarFirmaRequest $request,
        TramiteFirma $firma,
        ProcesarFirmaAction $action
    ): RedirectResponse {
        $tramite = $action->ejecutar(
            firma: $firma,
            decision: $request->validated('decision'),
            firmante: $request->user(),
            motivo: $request->validated('motivo')
        );

        $mensaje = $request->validated('decision') === 'AUTORIZADO'
            ? "Turno de firma avalado correctamente para el folio {$tramite->folio}."
            : "El trámite {$tramite->folio} ha sido cancelado definitivamente por rechazo.";

        return back()->with('success', $mensaje);
    }
}

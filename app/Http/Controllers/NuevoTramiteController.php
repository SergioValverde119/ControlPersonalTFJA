<?php

namespace App\Http\Controllers;

use App\Actions\Workflow\RegistrarAltaTramiteAction;
use App\Http\Requests\StoreAltaTramiteRequest;
use App\Models\Plaza;
use App\Models\Tramite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class NuevoTramiteController extends Controller
{
    /**
     * Catálogo de plazas vacantes exclusivas de la oficina base del usuario.
     */
    public function plazas(Request $request): Response
    {
        Gate::authorize('create', Tramite::class);

        $plazas = Plaza::query()
            ->vacantesParaUsuario($request->user())
            ->with([
                'puesto:id,clave,nombre,nivel_tabular',
                'unidad:id,clave,nombre',
            ])
            ->orderBy('codigo_plaza')
            ->get();

        return Inertia::render('nuevo-tramite/PlazasDisponibles', [
            'plazas' => $plazas,
        ]);
    }

    /**
     * Formulario de captura ágil (Fase 1: Solicitante).
     */
    public function alta(Request $request): Response
    {
        Gate::authorize('create', Tramite::class);

        $plazaSeleccionada = null;

        if ($request->filled('plaza_id')) {
            $plazaSeleccionada = Plaza::query()
                ->vacantesParaUsuario($request->user())
                ->with([
                    'puesto:id,clave,nombre,nivel_tabular',
                    'unidad:id,clave,nombre',
                ])
                ->find($request->integer('plaza_id'));
        }

        return Inertia::render('nuevo-tramite/Alta', [
            'plazaSeleccionada' => $plazaSeleccionada,
        ]);
    }

    /**
     * Persiste la propuesta, almacena oficio/CV y detona la cadena jerárquica.
     */
    public function store(
        StoreAltaTramiteRequest $request,
        RegistrarAltaTramiteAction $action
    ): RedirectResponse {
        $tramite = $action->ejecutar(
            datos: $request->safe()->except(['oficio_propuesta', 'curriculum_vitae']),
            oficio: $request->file('oficio_propuesta'),
            cv: $request->file('curriculum_vitae'),
            solicitante: $request->user()
        );

        return redirect()->route('estado-tramite.show', $tramite)
            ->with('success', "Trámite registrado con folio {$tramite->folio} y turnado a firmas.");
    }

    public function baja(): Response
    {
        Gate::authorize('create', Tramite::class);

        return Inertia::render('nuevo-tramite/Baja');
    }

    public function promocion(): Response
    {
        Gate::authorize('create', Tramite::class);

        return Inertia::render('nuevo-tramite/Promocion');
    }

    public function democion(): Response
    {
        Gate::authorize('create', Tramite::class);

        return Inertia::render('nuevo-tramite/Democion');
    }
}

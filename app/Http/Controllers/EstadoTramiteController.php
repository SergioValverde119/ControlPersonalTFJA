<?php

namespace App\Http\Controllers;

use App\Models\CatalogoEstatusTramite;
use App\Models\Tramite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EstadoTramiteController extends Controller
{
    /**
     * Listado general y filtrado de trámites visibles para el usuario autenticado.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Tramite::class);

        $tramites = Tramite::query()
            ->visiblesParaUsuario($request->user())
            ->with([
                'estatus:id,clave,nombre',
                'persona:id,curp,rfc,nombre,primer_apellido,segundo_apellido',
                'plazaDestino.puesto:id,clave,nombre,nivel_tabular',
                'plazaDestino.unidad:id,clave,nombre',
                'firmas' => fn ($q) => $q->orderBy('orden')->select([
                    'id',
                    'tramite_id',
                    'orden',
                    'etiqueta_rol',
                    'estatus',
                    'firmante_user_id',
                    'firmado_en',
                ]),
                'firmas.firmante:id,name',
            ])
            ->when($request->filled('buscar'), function (Builder $query) use ($request) {
                $termino = trim($request->string('buscar'));

                $query->where(function (Builder $sub) use ($termino) {
                    $sub->where('folio', 'like', "%{$termino}%")
                        ->orWhereHas('persona', function (Builder $p) use ($termino) {
                            $p->where('curp', 'like', "%{$termino}%")
                              ->orWhere('rfc', 'like', "%{$termino}%")
                              ->orWhere('nombre', 'like', "%{$termino}%")
                              ->orWhere('primer_apellido', 'like', "%{$termino}%");
                        });
                });
            })
            ->when($request->filled('estatus_id'), function (Builder $query) use ($request) {
                $query->where('estatus_id', $request->integer('estatus_id'));
            })
            ->when($request->filled('tipo_movimiento'), function (Builder $query) use ($request) {
                $query->where('tipo_movimiento', $request->string('tipo_movimiento'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('estado-tramite/Index', [
            'tramites'  => $tramites,
            'filtros'   => $request->only(['buscar', 'estatus_id', 'tipo_movimiento']),
            'catalogos' => [
                'estatus' => fn () => CatalogoEstatusTramite::all(['id', 'clave', 'nombre']),
            ],
        ]);
    }

    /**
     * Muestra el expediente transaccional completo y el avance de firmas.
     */
    public function show(Tramite $tramite): Response
    {
        Gate::authorize('view', $tramite);

        $tramite->load([
            'estatus:id,clave,nombre',
            'persona',
            'plazaDestino.puesto:id,clave,nombre,nivel_tabular',
            'plazaDestino.unidad:id,clave,nombre',
            'plazaOrigen.puesto:id,clave,nombre',
            'plazaOrigen.unidad:id,clave,nombre',
            'solicitante:id,name,email',
            'documentos.tipoDocumento:id,clave,nombre',
            'firmas.firmante:id,name,email',
            'firmas.unidad:id,clave,nombre',
        ]);

        return Inertia::render('estado-tramite/Show', [
            'tramite' => $tramite,
        ]);
    }
}

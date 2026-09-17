<?php

namespace App\Services\Workflow;

use App\Models\CatalogoEstatusTramite;
use App\Models\Tramite;
use App\Models\TramiteFirma;
use App\Models\UnidadOrganizacional;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GeneradorCadenaFirmas
{
    /**
     * Construye y persiste la cadena inmutable de firmas para el trámite.
     *
     * @param  Tramite  $tramite
     * @return Collection<int, TramiteFirma>
     *
     * @throws DomainException
     */
    public function procesar(Tramite $tramite): Collection
    {
        return DB::transaction(function () use ($tramite) {
            $unidadDestino = $tramite->plazaDestino->unidad;
            $ancestros = $this->obtenerAncestrosIndexados($unidadDestino);

            $candidatos = [];

            // 0. CANDADO DE VENIA (Si el servidor público ya está activo en otra área cedente)
            $unidadCedente = $this->resolverUnidadCedente($tramite);

            if ($unidadCedente && $unidadCedente->id !== $unidadDestino->id) {
                $candidatos[] = [
                    'etiqueta_rol' => 'TITULAR_SUPERIOR',
                    'unidad'       => $unidadCedente,
                ];
            }

            // 1. AUTORIDAD INMEDIATA (Área Compartida vs Ordinaria)
            if ($unidadDestino->tipo->escala_inmediato) {
                // Si es Archivo u Oficialía de Partes, escala a la Presidencia de Sede
                $sede = $this->extraerPorClaveTipo($ancestros, 'SEDE');
                if ($sede) {
                    $candidatos[] = [
                        'etiqueta_rol' => 'PRESIDENTE_SALA',
                        'unidad'       => $sede,
                    ];
                }
            } else {
                // Titular directo de la Ponencia o Unidad destino
                $candidatos[] = [
                    'etiqueta_rol' => 'TITULAR_SUPERIOR',
                    'unidad'       => $unidadDestino,
                ];
            }

            // 2. VISTO BUENO INSTITUCIONAL (Presidencia de Sede / Sala)
            $sede = $this->extraerPorClaveTipo($ancestros, 'SEDE');
            if ($sede && $sede->id !== $unidadDestino->id && ! $unidadDestino->tipo->escala_inmediato) {
                $candidatos[] = [
                    'etiqueta_rol' => 'PRESIDENTE_SALA',
                    'unidad'       => $sede,
                ];
            }

            // 3. DICTAMEN TÉCNICO REGIONAL (Visitaduría / Territorio)
            $territorio = $this->extraerPorClaveTipo($ancestros, 'TERRITORIO');
            if ($territorio && $territorio->id !== $unidadDestino->id) {
                $candidatos[] = [
                    'etiqueta_rol' => 'MAGISTRADO_VISITADOR',
                    'unidad'       => $territorio,
                ];
            }

            // 4. DEPURACIÓN DE REGLAS (anti-autofirma, deduplicación) Y PERSISTENCIA
            $firmasGeneradas = $this->guardarCadenaDepurada($tramite, $candidatos);

            // 5. ACTUALIZAR ESTATUS DEL TRÁMITE
            if ($firmasGeneradas->isEmpty()) {
                $estatusRh = CatalogoEstatusTramite::where('clave', 'MESA_TECNICA_RH')->firstOrFail();
                $tramite->update(['estatus_id' => $estatusRh->id]);
            } else {
                $estatusJerarquico = CatalogoEstatusTramite::where('clave', 'AUTORIZACION_JERARQUICA')->firstOrFail();
                $tramite->update(['estatus_id' => $estatusJerarquico->id]);
            }

            return $firmasGeneradas;
        });
    }

    /**
     * Identifica el área de adscripción actual de donde proviene el aspirante.
     */
    protected function resolverUnidadCedente(Tramite $tramite): ?UnidadOrganizacional
    {
        return $tramite->plazaOrigen?->unidad
            ?: $tramite->persona?->user?->titularidadActiva?->unidad;
    }

    /**
     * Recupera la jerarquía ascendente a partir de la ruta materializada (path) en una sola consulta SQL O(1).
     */
    protected function obtenerAncestrosIndexados(UnidadOrganizacional $unidad): Collection
    {
        $ids = array_filter(explode('/', trim($unidad->path, '/')));

        if (empty($ids)) {
            return collect();
        }

        return UnidadOrganizacional::with(['tipo', 'titularActual.user'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }

    protected function extraerPorClaveTipo(Collection $ancestros, string $claveTipo): ?UnidadOrganizacional
    {
        return $ancestros->first(fn ($u) => $u->tipo && $u->tipo->clave === $claveTipo);
    }

    /**
     * Resuelve titulares activos, omite autofirmas y persiste registros con orden consecutivo.
     */
    protected function guardarCadenaDepurada(Tramite $tramite, array $candidatos): Collection
    {
        $firmasPersistidas = collect();
        $usuariosAgregados = [];
        $orden = 1;

        $solicitanteId = $tramite->usuario_solicitante_id;

        foreach ($candidatos as $paso) {
            /** @var UnidadOrganizacional $unidad */
            $unidad = $paso['unidad'];

            // Resuelve quién ostenta la titularidad o suplencia activa
            $titularidad = $unidad->titularActual;

            if (! $titularidad || ! $titularidad->user) {
                throw new DomainException(
                    "No se puede generar la cadena de firmas: la unidad '{$unidad->nombre}' ({$unidad->clave}) no cuenta con titular o suplente activo asignado."
                );
            }

            $firmante = $titularidad->user;

            // REGLA 1: Omitir autofirma (el solicitante no se da Vo.Bo. a sí mismo)
            if ($firmante->id === $solicitanteId) {
                continue;
            }

            // REGLA 2: No duplicar firmante si ya fue añadido en un nivel previo
            if (in_array($firmante->id, $usuariosAgregados, true)) {
                continue;
            }

            $usuariosAgregados[] = $firmante->id;

            $firma = TramiteFirma::create([
                'tramite_id'               => $tramite->id,
                'orden'                    => $orden++,
                'etiqueta_rol'             => $paso['etiqueta_rol'],
                'unidad_organizacional_id' => $unidad->id,
                'firmante_user_id'         => $firmante->id,
                'estatus'                  => 'PENDIENTE',
            ]);

            $firmasPersistidas->push($firma);
        }

        return $firmasPersistidas;
    }
}

<?php

namespace App\Policies;

use App\Models\Tramite;
use App\Models\User;

class TramitePolicy
{
    /**
     * Determina si el usuario puede ver el listado de estado de trámites.
     */
    public function viewAny(User $user): bool
    {
        // Admin DGTIC o cualquier servidor con adscripción y titularidad activa
        return $user->hasRole('ADMIN_DGTIC') || $user->titularidadActiva()->exists();
    }

    /**
     * Determina si el usuario puede ver el expediente y trazabilidad de un trámite concreto.
     */
    public function view(User $user, Tramite $tramite): bool
    {
        if ($user->hasRole('ADMIN_DGTIC')) {
            return true;
        }

        // 1. Es el solicitante que capturó la propuesta
        if ($tramite->usuario_solicitante_id === $user->id) {
            return true;
        }

        // 2. Es o fue firmante en la escalera de autorización (incluye quien emitió venia)
        if ($tramite->firmas()->where('firmante_user_id', $user->id)->exists()) {
            return true;
        }

        // 3. Es el titular actual de la oficina donde radica la plaza destino
        $unidadPropiaId = $user->titularidadActiva?->unidad_organizacional_id;

        return $unidadPropiaId !== null
            && $tramite->plazaDestino?->unidad_organizacional_id === $unidadPropiaId;
    }

    /**
     * Determina si el usuario tiene facultades para iniciar una nueva propuesta.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN_DGTIC') || $user->titularidadActiva()->exists();
    }
}

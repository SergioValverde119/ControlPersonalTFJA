<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determina quién puede entrar a la pantalla de administración.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'ADMIN_DGTIC',
            'MAGISTRADO_VISITADOR',
            'MAGISTRADO_PRESIDENTE',
            'MAGISTRADO_PONENTE',
        ]);
    }

    /**
     * Solo DGTIC puede dar de alta nuevos usuarios al sistema.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN_DGTIC');
    }

    /**
     * DGTIC edita a cualquiera. Los Magistrados solo pueden editar
     * (cambio de password) al personal dentro de su jurisdicción en el grafo.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasRole('ADMIN_DGTIC')) {
            return true;
        }

        $unidadPropia = $user->titularidadActiva?->unidad;
        $unidadObjetivo = $model->titularidadActiva?->unidad;

        if (! $unidadPropia || ! $unidadObjetivo) {
            return false;
        }

        // Si el objetivo está en el mismo nodo o en un subnodo descendiente
        return str_starts_with($unidadObjetivo->path, $unidadPropia->path);
    }

    /**
     * Solo DGTIC puede dar de baja lógica a un usuario.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('ADMIN_DGTIC') && $user->id !== $model->id;
    }
}

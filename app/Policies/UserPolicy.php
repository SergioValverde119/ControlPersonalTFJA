<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Pase directo para el Administrador DGTIC en todo,
     * excepto en 'delete' para validar que no se elimine a sí mismo.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($ability === 'delete') {
            return null;
        }

        if ($user->hasRole('ADMIN_DGTIC')) {
            return true;
        }

        return null;
    }

    /**
     * Listado general:
     * Admin entra vía before().
     * Los Magistrados solo entran a consultar su padrón adscrito.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            'MAGISTRADO_PRESIDENTE',
            'MAGISTRADO_PONENTE',
            'MAGISTRADO_VISITADOR'
        );
    }

    /**
     * Consulta de un usuario específico:
     * Valida la jurisdicción territorial del Magistrado.
     */
    public function view(User $user, User $target): bool
    {
        if ($user->hasRole('MAGISTRADO_PRESIDENTE')) {
            return !is_null($user->sala_id) && $user->sala_id === $target->sala_id;
        }

        if ($user->hasRole('MAGISTRADO_PONENTE')) {
            return !is_null($user->area_id) && $user->area_id === $target->area_id;
        }

        if ($user->hasRole('MAGISTRADO_VISITADOR')) {
            return !is_null($target->sala_id) && $user->salasVisitadas->contains('id', $target->sala_id);
        }

        return false;
    }

    /**
     * Crear usuario:
     * EXCLUSIVO de ADMIN_DGTIC (pasa directo vía before; los demás retornan false).
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Actualizar usuario:
     * Los Magistrados solo pueden intervenir sobre el personal de su jurisdicción.
     */
    public function update(User $user, User $target): bool
    {
        return $this->view($user, $target);
    }

    /**
     * Eliminar usuario:
     * EXCLUSIVO de ADMIN_DGTIC y no permite auto-eliminación.
     */
    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        return $user->hasRole('ADMIN_DGTIC');
    }
}
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
     * Puerta de entrada a la pantalla / listado general:
     * ADMIN_DGTIC entra por before().
     * RECURSOS_HUMANOS entra por esta regla.
     * Magistrados y cualquier otro usuario reciben 403 Forbidden.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('RECURSOS_HUMANOS');
    }

    /**
     * Consulta de un usuario específico:
     * Valida la jurisdicción territorial (se activa cuando entren los Magistrados).
     */
    public function view(User $user, User $target): bool
    {
        if ($user->hasRole('RECURSOS_HUMANOS')) {
            return true;
        }

        if ($user->hasRole('MAGISTRADO_PRESIDENTE')) {
            return ! is_null($user->sala_id) && $user->sala_id === $target->sala_id;
        }

        if ($user->hasRole('MAGISTRADO_PONENTE')) {
            return ! is_null($user->area_id) && $user->area_id === $target->area_id;
        }

        if ($user->hasRole('MAGISTRADO_VISITADOR')) {
            return ! is_null($target->sala_id) && $user->salasVisitadas->contains('id', $target->sala_id);
        }

        return false;
    }

    /**
     * Crear usuario:
     * Exclusivo de ADMIN_DGTIC (pasa directo vía before).
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Actualizar usuario:
     * Solo personal dentro de la jurisdicción (para cuando Magistrados cambien contraseña).
     */
    public function update(User $user, User $target): bool
    {
        return $this->view($user, $target);
    }

    /**
     * Eliminar usuario:
     * Exclusivo de ADMIN_DGTIC y no permite auto-eliminación.
     */
    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        return $user->hasRole('ADMIN_DGTIC');
    }
}

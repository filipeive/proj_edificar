<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'secretaria', 'tesouraria']);
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $model->cell && $model->cell->supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $model->cell && $model->cell->supervision_id === $user->supervisedSupervisions()->first()?->id;
        }

        if ($user->isLider()) {
            return $model->cell_id === $user->cell_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'secretaria', 'tesouraria']);
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $model->cell && $model->cell->supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $model->cell && $model->cell->supervision_id === $user->supervisedSupervisions()->first()?->id;
        }

        if ($user->isLider()) {
            return $model->cell_id === $user->cell_id && $model->role === 'membro';
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}

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
        // Admins, secretaria, and pastors can view anyone
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Pastor de Zona can view users in their zone
        if ($user->isPastorZona()) {
            if (!$model->cell || !$model->cell->supervision) {
                return false;
            }
            return $model->cell->supervision->zone_id === $user->getZoneId();
        }

        // Supervisor can view users in their supervised supervisions
        if ($user->isSupervisor()) {
            if (!$model->cell) {
                return false;
            }
            $supervisionIds = $user->supervisedSupervisions()->pluck('id');
            return $supervisionIds->contains($model->cell->supervision_id);
        }

        // Cell leader can view members of their cell
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
        // Admins and secretaria can update anyone
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }

        // Users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Pastor de Zona can update users in their zone
        if ($user->isPastorZona()) {
            if (!$model->cell || !$model->cell->supervision) {
                return false;
            }
            return $model->cell->supervision->zone_id === $user->getZoneId();
        }

        // Supervisor can update users in their supervised supervisions
        if ($user->isSupervisor()) {
            if (!$model->cell) {
                return false;
            }
            $supervisionIds = $user->supervisedSupervisions()->pluck('id');
            return $supervisionIds->contains($model->cell->supervision_id);
        }

        // Cell leader can update members (not leaders) of their cell
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

<?php

namespace App\Policies;

use App\Models\Supervision;
use App\Models\User;

class SupervisionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria'], true);
    }

    public function view(User $user, Supervision $supervision): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $supervision->supervisor_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isPastorZona();
    }

    public function update(User $user, Supervision $supervision): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $supervision->supervisor_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Supervision $supervision): bool
    {
        return $user->isAdmin();
    }
}

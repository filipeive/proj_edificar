<?php

namespace App\Policies;

use App\Models\Zone;
use App\Models\User;

class ZonePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor_senior', 'pastor', 'pastor_zona', 'secretaria', 'tesouraria'], true);
    }

    public function view(User $user, Zone $zone): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $zone->pastor_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Zone $zone): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $zone->pastor_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Zone $zone): bool
    {
        return $user->isAdmin();
    }
}

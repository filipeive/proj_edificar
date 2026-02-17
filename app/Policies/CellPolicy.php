<?php

namespace App\Policies;

use App\Models\Cell;
use App\Models\User;

class CellPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria']);
    }

    public function view(User $user, Cell $cell): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $cell->supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $cell->supervision_id === $user->supervisedSupervisions()->first()?->id;
        }

        if ($user->isLider() || $user->isTimoteo()) {
            return $cell->leader_id === $user->id || $user->cell_id === $cell->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastorZona() || $user->isSupervisor();
    }

    public function update(User $user, Cell $cell): bool
    {
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $cell->supervision->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $cell->supervision_id === $user->supervisedSupervisions()->first()?->id;
        }

        if ($user->isLider() || $user->isTimoteo()) {
            return $cell->leader_id === $user->id || $user->cell_id === $cell->id;
        }

        return false;
    }

    public function delete(User $user, Cell $cell): bool
    {
        return $user->isAdmin() || $user->isSecretaria();
    }
}

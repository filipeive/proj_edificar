<?php

namespace App\Policies;

use App\Models\Cell;
use App\Models\User;

class CellPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || in_array($user->role, ['pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'sub_supervisor', 'secretaria', 'tesouraria', 'administracao', 'lider_celula', 'timoteo'], true);
    }

    public function view(User $user, Cell $cell): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorSenior() || $user->isAdministracao()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $user->getManagedZoneIds()->contains($cell->supervision->zone_id);
        }

        if ($user->isSupervisor()) {
            return $user->getManagedSupervisionIds()->contains($cell->supervision_id);
        }

        if ($user->isSubSupervisor()) {
            return $user->getManagedSupervisionIds()->contains($cell->supervision_id);
        }

        if ($user->isLider() || $user->isTimoteo()) {
            return $cell->leader_id === $user->id || $user->cell_id === $cell->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->isSecretaria()
            || $user->isPastorSenior()
            || $user->isPastor()
            || $user->isPastorZona()
            || $user->isSupervisor()
            || $user->isAdministracao();
    }

    public function update(User $user, Cell $cell): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorSenior() || $user->isAdministracao()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $user->getManagedZoneIds()->contains($cell->supervision->zone_id);
        }

        if ($user->isSupervisor()) {
            return $user->getManagedSupervisionIds()->contains($cell->supervision_id);
        }

        if ($user->isSubSupervisor()) {
            return $user->getManagedSupervisionIds()->contains($cell->supervision_id);
        }

        if ($user->isLider() || $user->isTimoteo()) {
            return $cell->leader_id === $user->id || $user->cell_id === $cell->id;
        }

        return false;
    }

    public function delete(User $user, Cell $cell): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastorSenior();
    }
}

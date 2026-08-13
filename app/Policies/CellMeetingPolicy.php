<?php

namespace App\Policies;

use App\Models\CellMeeting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CellMeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'timoteo'], true);
    }

    public function view(User $user, CellMeeting $cellMeeting): bool
    {
        if ($user->isAdmin() || $user->role === 'pastor') {
            return true;
        }

        if ($user->role === 'pastor_zona') {
            return $cellMeeting->cell->supervision->zone->pastor_id === $user->id;
        }

        if ($user->role === 'supervisor') {
            return $cellMeeting->cell->supervision->supervisor_id === $user->id;
        }

        if ($user->role === 'lider_celula' || $user->role === 'timoteo') {
            return $user->getManagedCellIds()->contains($cellMeeting->cell_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'timoteo'], true);
    }

    public function update(User $user, CellMeeting $cellMeeting): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $cellMeeting->leader_id === $user->id
            || (($user->role === 'lider_celula' || $user->role === 'timoteo') && $user->getManagedCellIds()->contains($cellMeeting->cell_id));
    }

    public function delete(User $user, CellMeeting $cellMeeting): bool
    {
        return $user->isAdmin()
            || (
                ($cellMeeting->leader_id === $user->id
                    || (($user->role === 'lider_celula' || $user->role === 'timoteo') && $user->getManagedCellIds()->contains($cellMeeting->cell_id)))
                && $cellMeeting->created_at->diffInHours(now()) < 24
            );
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'timoteo'], true);
    }
}

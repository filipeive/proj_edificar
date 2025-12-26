<?php

namespace App\Policies;

use App\Models\CellMeeting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CellMeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula']);
    }

    public function view(User $user, CellMeeting $cellMeeting): bool
    {
        if (in_array($user->role, ['admin', 'pastor'])) {
            return true;
        }

        if ($user->role === 'pastor_zona') {
            return $cellMeeting->cell->supervision->zone->pastor_id === $user->id;
        }

        if ($user->role === 'supervisor') {
            return $cellMeeting->cell->supervision->supervisor_id === $user->id;
        }

        if ($user->role === 'lider_celula') {
            return $cellMeeting->cell->leader_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula']);
    }

    public function update(User $user, CellMeeting $cellMeeting): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $cellMeeting->leader_id === $user->id;
    }

    public function delete(User $user, CellMeeting $cellMeeting): bool
    {
        return $user->role === 'admin' || ($cellMeeting->leader_id === $user->id && $cellMeeting->created_at->diffInHours(now()) < 24);
    }
}

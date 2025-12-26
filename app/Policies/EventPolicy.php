<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor']);
    }

    public function view(User $user, Event $event): bool
    {
        if (in_array($user->role, ['admin', 'pastor'])) {
            return true;
        }

        if ($user->role === 'pastor_zona') {
            return $event->zone_id === $user->getZoneId();
        }

        if ($user->role === 'supervisor') {
            return $event->zone_id === $user->getZoneId();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor']);
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return in_array($user->role, ['pastor', 'pastor_zona', 'supervisor']) && $event->zone_id === $user->getZoneId();
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }
}

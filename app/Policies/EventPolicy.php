<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria']);
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor()) {
            return true;
        }

        if ($user->isPastorZona()) {
            return $event->zone_id === $user->getZoneId();
        }

        if ($user->isSupervisor()) {
            return $event->zone_id === $user->getZoneId();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria']);
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }

        if ($user->isPastorZona() || $user->isSupervisor() || $user->isPastor()) {
            return $event->zone_id === $user->getZoneId();
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin() || $user->isSecretaria();
    }
}

<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria', 'lider_celula', 'membro', 'administracao']);
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isAdministracao()) {
            return true;
        }

        // Permitir visualização de eventos globais (Sem zona e sem célula)
        if ($event->zone_id === null && $event->cell_id === null) {
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
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'secretaria', 'tesouraria']);
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }

        if ($user->isPastorZona() || $user->isPastor()) {
            return $event->zone_id === $user->getZoneId();
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin() || $user->isSecretaria();
    }
}

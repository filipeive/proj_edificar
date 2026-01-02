<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria']);
    }

    public function view(User $user, Service $service): bool
    {
        return in_array($user->role, ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria']);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorZona();
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorZona();
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin() || $user->isSecretaria();
    }
}

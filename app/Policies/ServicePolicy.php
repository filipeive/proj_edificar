<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria', 'administracao'], true);
    }

    public function view(User $user, Service $service): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'secretaria', 'tesouraria', 'administracao'], true);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorZona() || $user->isAdministracao();
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorZona() || $user->isAdministracao();
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin() || $user->isSecretaria() || $user->isAdministracao();
    }
}

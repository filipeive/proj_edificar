<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor', 'tesouraria']);
    }

    public function view(User $user, Service $service): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona', 'supervisor', 'tesouraria']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona']);
    }

    public function update(User $user, Service $service): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'pastor_zona']);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->role === 'admin';
    }
}

<?php
namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy
{
    public function view(User $user, Contribution $contribution): bool
    {
        // Pode ver se é o dono ou líder da célula ou admin ou secretaria
        return $user->id === $contribution->user_id
            || ($contribution->cell && $user->id === $contribution->cell->leader_id)
            || $user->isAdmin()
            || $user->isSecretaria()
            || $user->isPastorZona();
    }

    public function update(User $user, Contribution $contribution): bool
    {
        // Só o dono pode editar contribuições pendentes, ou admin/secretaria
        if ($user->isAdmin() || $user->isSecretaria()) {
            return true;
        }
        return $user->id === $contribution->user_id
            && $contribution->status === 'pendente';
    }

    public function verify(User $user): bool
    {
        // Só admin/secretaria pode verificar
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastorZona();
    }

    public function reject(User $user): bool
    {
        // Só admin/secretaria pode rejeitar
        return $user->isAdmin() || $user->isSecretaria() || $user->isPastorZona();
    }
}
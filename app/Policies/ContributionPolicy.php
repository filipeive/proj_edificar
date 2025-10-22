<?php
namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy {
    public function view(User $user, Contribution $contribution): bool {
        // Pode ver se é o dono ou líder da célula ou admin
        return $user->id === $contribution->user_id 
            || $user->id === $contribution->cell->leader_id 
            || $user->role === 'admin' 
            || $user->role === 'pastor_zona';
    }

    public function update(User $user, Contribution $contribution): bool {
        // Só o dono pode editar contribuições pendentes
        return $user->id === $contribution->user_id 
            && $contribution->status === 'pendente';
    }

    public function verify(User $user): bool {
        // Só admin pode verificar
        return $user->role === 'admin' || $user->role === 'pastor_zona';
    }

    public function reject(User $user): bool {
        // Só admin pode rejeitar
        return $user->role === 'admin' || $user->role === 'pastor_zona';
    }
}
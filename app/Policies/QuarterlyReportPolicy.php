<?php

namespace App\Policies;

use App\Models\QuarterlyReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuarterlyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor', 'pastor_zona', 'supervisor'], true);
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, QuarterlyReport $report): bool
    {
        if ($user->isAdmin() || $user->role === 'pastor') {
            return true;
        }

        if ($user->role === 'pastor_zona') {
            return $report->zone->pastor_id === $user->id;
        }

        if ($user->role === 'supervisor') {
            return $report->zone_id === $user->getZoneId(); // Assuming a helper to get zone ID
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || in_array($user->role, ['pastor', 'pastor_zona', 'supervisor'], true);
    }

    public function update(User $user, QuarterlyReport $report): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->role === 'pastor_zona') {
            return $report->zone->pastor_id === $user->id;
        }

        return $report->status === 'draft' && $report->supervisor_id === $user->id;
    }

    public function delete(User $user, QuarterlyReport $report): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, QuarterlyReport $report): bool
    {
        return $user->isAdmin() || $user->role === 'pastor';
    }
}

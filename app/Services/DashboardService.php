<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cell;
use App\Models\Supervision;
use App\Models\Zone;
use App\Models\Event;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get consolidated metrics for a user based on their role.
     */
    public function getMetricsForUser(User $user): array
    {
        $role = $user->role;

        if (in_array($role, ['super_admin', 'admin', 'pastor_senior'])) {
            return $this->getAdminMetrics();
        }

        if ($role === 'pastor_zone' || $role === 'pastor_zona') {
            return $this->getPastorMetrics($user);
        }

        if ($role === 'supervisor') {
            return $this->getSupervisorMetrics($user);
        }

        if ($role === 'lider_celula') {
            return $this->getLiderMetrics($user);
        }

        return $this->getMemberMetrics($user);
    }

    private function getAdminMetrics(): array
    {
        return [
            'total_members' => User::where('is_active', true)->count(),
            'total_cells' => Cell::count(),
            'total_supervisions' => Supervision::count(),
            'total_zones' => Zone::count(),
            'recent_services' => Service::orderBy('date', 'desc')->limit(5)->get(),
        ];
    }

    private function getPastorMetrics(User $user): array
    {
        $zoneIds = $user->getManagedZoneIds();
        
        $zones = Zone::whereIn('id', $zoneIds)->get()->map(function ($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'supervisions_count' => $zone->supervisions()->count(),
                'cells_count' => $zone->getTotalCells(),
                'members_count' => User::whereIn('cell_id', Cell::whereIn('supervision_id', $zone->supervisions()->pluck('id'))->pluck('id'))->count(),
            ];
        });

        return [
            'zones' => $zones,
            'recent_services' => Service::orderBy('date', 'desc')->limit(5)->get(),
        ];
    }

    private function getSupervisorMetrics(User $user): array
    {
        $supervision = $user->cell ? $user->cell->supervision : null;

        if (!$supervision) {
            return [
                'cells' => collect(),
                'total' => 0,
                'supervisionName' => 'Sem Supervisão Atribuída',
                'upcoming_events' => collect(),
                'recent_services' => Service::orderBy('date', 'desc')->limit(5)->get(),
            ];
        }

        $cells = $supervision->cells->map(function ($cell) {
            return [
                'id' => $cell->id,
                'name' => $cell->name,
                'leader' => $cell->leader ? $cell->leader->name : 'N/A',
                'members' => $cell->getMembersCount(),
                'contributed' => $cell->getMembersContributedThisMonth(),
                'total' => $cell->getTotalContributedThisMonth(),
            ];
        });

        return [
            'cells' => $cells,
            'total' => $supervision->getTotalContributedThisMonth(),
            'supervisionName' => $supervision->name,
            'upcoming_events' => Event::where('zone_id', $supervision->zone_id)
                ->where('date', '>=', now())
                ->orderBy('date', 'asc')
                ->limit(5)
                ->get(),
            'recent_services' => Service::orderBy('date', 'desc')->limit(5)->get(),
        ];
    }

    private function getLiderMetrics(User $user): array
    {
        $cell = $user->cell;

        if (!$cell || $cell->leader_id !== $user->id) {
            return [
                'members' => collect(),
                'total' => 0,
                'cellName' => 'Nenhuma Célula Atribuída',
                'upcoming_events' => collect(),
                'recent_meetings' => collect(),
            ];
        }

        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $members = $cell->members()
            ->where('is_active', true)
            ->get()
            ->map(function ($member) use ($monthStart, $monthEnd) {
                $contributions = $member->contributions()
                    ->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada')
                    ->sum('amount');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'total' => $contributions,
                    'status' => $contributions > 0 ? 'Contribuiu' : 'Faltoso',
                ];
            });

        return [
            'members' => $members,
            'total' => $cell->getTotalContributedThisMonth(),
            'cellName' => $cell->name,
            'upcoming_events' => Event::where('zone_id', $cell->supervision->zone_id)
                ->where('date', '>=', now())
                ->orderBy('date', 'asc')
                ->limit(5)
                ->get(),
            'recent_meetings' => $cell->meetings()
                ->orderBy('meeting_date', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    private function getMemberMetrics(User $user): array
    {
        $cell = $user->cell;
        $upcomingEvents = collect();

        if ($cell && $cell->supervision) {
            $upcomingEvents = Event::where('zone_id', $cell->supervision->zone_id)
                ->where('date', '>=', now())
                ->orderBy('date', 'asc')
                ->limit(5)
                ->get();
        }

        return [
            'cell_name' => $cell ? $cell->name : 'Nenhuma Célula Atribuída',
            'personal_contributions_total' => $user->contributions()->where('status', 'verificada')->sum('amount'),
            'upcoming_events' => $upcomingEvents,
            'commitments_count' => $user->coupleEnrollments()->count() + $user->courseEnrollments()->count(),
        ];
    }
}

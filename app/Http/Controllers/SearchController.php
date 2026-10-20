<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contribution;
use App\Models\Cell;
use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = Auth::user();
        $isGlobalViewer = $user->isAdmin() || $user->isPastorSenior() || $user->isPastor() || $user->isSecretaria() || $user->isAdministracao();

        $results = [
            'Membros' => [],
            'Células' => [],
            'Supervisões' => [],
            'Zonas' => [],
            'Contribuições' => [],
        ];

        // 1. PESQUISAR MEMBROS
        $membersQuery = User::query()
            ->select('id', 'name', 'email', 'role')
            ->where('name', 'LIKE', '%' . $query . '%')
            ->where('is_active', true)
            ->limit(5);

        if ($isGlobalViewer) {
            // Vê todos da congregação
        } elseif ($user->isPastorZona()) {
            $zoneIds = $user->getManagedZoneIds();
            $supervisions = Supervision::whereIn('zone_id', $zoneIds)->pluck('id');
            $cells = Cell::whereIn('supervision_id', $supervisions)->pluck('id');
            $membersQuery->whereIn('cell_id', $cells);
        } elseif ($user->isSupervisor() || $user->isSubSupervisor()) {
            $supervisionIds = $user->getManagedSupervisionIds();
            $cells = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            $membersQuery->whereIn('cell_id', $cells);
        } elseif ($user->isLider() || $user->isTimoteo()) {
            $membersQuery->where('cell_id', $user->cell_id);
        } else {
            $membersQuery->where('id', $user->id);
        }

        $results['Membros'] = $membersQuery->get()->toArray();

        // 2. PESQUISAR CÉLULAS
        $cellsQuery = Cell::query()
            ->with(['supervision.zone:id,name', 'leader:id,name'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhereHas('leader', fn ($lq) => $lq->where('name', 'LIKE', '%' . $query . '%'))
                    ->orWhereHas('supervision', fn ($sq) => $sq->where('name', 'LIKE', '%' . $query . '%'))
                    ->orWhereHas('supervision.zone', fn ($zq) => $zq->where('name', 'LIKE', '%' . $query . '%'));
            })
            ->limit(5);

        if ($isGlobalViewer) {
            // Vê todas as células da congregação
        } elseif ($user->isPastorZona()) {
            $zoneIds = $user->getManagedZoneIds();
            $cellsQuery->whereHas('supervision', fn ($sq) => $sq->whereIn('zone_id', $zoneIds));
        } elseif ($user->isSupervisor() || $user->isSubSupervisor()) {
            $cellsQuery->whereIn('supervision_id', $user->getManagedSupervisionIds());
        } elseif ($user->isLider() || $user->isTimoteo()) {
            $cellsQuery->where(function ($cq) use ($user) {
                $cq->where('leader_id', $user->id)
                    ->orWhere('id', $user->cell_id);
            });
        } else {
            $cellsQuery->where('id', $user->cell_id ?? 0);
        }

        $results['Células'] = $cellsQuery->get()->map(fn (Cell $cell) => [
            'id' => $cell->id,
            'name' => $cell->name,
            'leader_name' => $cell->leader?->name,
            'supervision_name' => $cell->supervision?->name,
            'zone_name' => $cell->supervision?->zone?->name,
        ])->toArray();

        // 3. PESQUISAR SUPERVISÕES
        if ($isGlobalViewer || $user->isPastorZona() || $user->isSupervisor() || $user->isSubSupervisor()) {
            $supervisionsQuery = Supervision::query()
                ->with(['zone:id,name', 'supervisor:id,name'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhereHas('supervisor', fn ($sq) => $sq->where('name', 'LIKE', '%' . $query . '%'))
                        ->orWhereHas('zone', fn ($zq) => $zq->where('name', 'LIKE', '%' . $query . '%'));
                })
                ->limit(5);

            if (! $isGlobalViewer) {
                if ($user->isPastorZona()) {
                    $supervisionsQuery->whereIn('zone_id', $user->getManagedZoneIds());
                } else {
                    $supervisionsQuery->whereIn('id', $user->getManagedSupervisionIds());
                }
            }

            $results['Supervisões'] = $supervisionsQuery->get()->map(fn (Supervision $sup) => [
                'id' => $sup->id,
                'name' => $sup->name,
                'supervisor_name' => $sup->supervisor?->name,
                'zone_name' => $sup->zone?->name,
            ])->toArray();
        }

        // 4. PESQUISAR ZONAS
        if ($isGlobalViewer || $user->isPastorZona()) {
            $zonesQuery = Zone::query()
                ->with('pastor:id,name')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhereHas('pastor', fn ($pq) => $pq->where('name', 'LIKE', '%' . $query . '%'));
                })
                ->limit(5);

            if (! $isGlobalViewer && $user->isPastorZona()) {
                $zonesQuery->whereIn('id', $user->getManagedZoneIds());
            }

            $results['Zonas'] = $zonesQuery->get()->map(fn (Zone $zone) => [
                'id' => $zone->id,
                'name' => $zone->name,
                'pastor_name' => $zone->pastor?->name,
            ])->toArray();
        }

        // 5. PESQUISAR CONTRIBUIÇÕES
        $contributionsQuery = Contribution::query()
            ->select('id', 'amount', 'contribution_date', 'status', 'user_id')
            ->with('user:id,name')
            ->limit(5);

        $contributionsQuery = $contributionsQuery->where(function ($q) use ($query) {
            $q->whereRaw('CAST(amount AS CHAR) LIKE ?', ['%' . $query . '%'])
              ->orWhereRaw('DATE_FORMAT(contribution_date, "%d/%m/%Y") LIKE ?', ['%' . $query . '%']);
        });

        if ($isGlobalViewer) {
            // Vê tudo
        } elseif ($user->isPastorZona()) {
            $supervisions = Supervision::whereIn('zone_id', $user->getManagedZoneIds())->pluck('id');
            $cells = Cell::whereIn('supervision_id', $supervisions)->pluck('id');
            $contributionsQuery->whereIn('user_id', User::whereIn('cell_id', $cells)->pluck('id'));
        } elseif ($user->isSupervisor() || $user->isSubSupervisor()) {
            $cells = Cell::whereIn('supervision_id', $user->getManagedSupervisionIds())->pluck('id');
            $contributionsQuery->whereIn('user_id', User::whereIn('cell_id', $cells)->pluck('id'));
        } elseif ($user->isLider() || $user->isTimoteo()) {
            $contributionsQuery->whereIn('user_id', User::where('cell_id', $user->cell_id)->pluck('id'));
        } else {
            $contributionsQuery->where('user_id', $user->id);
        }

        $results['Contribuições'] = $contributionsQuery->get()->toArray();

        return response()->json(['results' => $results]);
    }
}

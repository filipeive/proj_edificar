<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Contribution;
use App\Models\Cell;
use App\Models\Supervision;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportsController extends BaseApiController
{
    /**
     * Get contribution report based on entity filters.
     */
    public function contributions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cell_id' => 'nullable|exists:cells,id',
            'supervision_id' => 'nullable|exists:supervisions,id',
            'zone_id' => 'nullable|exists:zones,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth()->addDays(19);

        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->addMonth()->startOfMonth()->addDays(4);

        $query = Contribution::query()
            ->with(['user', 'cell', 'offeringType'])
            ->whereBetween('contribution_date', [$startDate, $endDate]);

        // Filter by specific Cell, Supervision, or Zone
        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        } elseif ($request->filled('supervision_id')) {
            $query->whereHas('cell', function ($q) use ($request) {
                $q->where('supervision_id', $request->supervision_id);
            });
        } elseif ($request->filled('zone_id')) {
            $query->whereHas('cell.supervision', function ($q) use ($request) {
                $q->where('zone_id', $request->zone_id);
            });
        }

        $contributions = $query->orderBy('contribution_date', 'desc')->get();

        $totalAmount = $contributions->sum('amount');
        $verifiedAmount = $contributions->where('status', 'verificada')->sum('amount');
        $pendingAmount = $contributions->where('status', 'pendente')->sum('amount');

        return $this->sendResponse([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'metrics' => [
                'total_amount' => (float) $totalAmount,
                'verified_amount' => (float) $verifiedAmount,
                'pending_amount' => (float) $pendingAmount,
                'contributions_count' => $contributions->count(),
            ],
            'contributions' => $contributions,
        ], 'Relatório de contribuições gerado com sucesso.');
    }
}

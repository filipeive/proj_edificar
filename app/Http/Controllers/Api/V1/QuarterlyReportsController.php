<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\QuarterlyReportResource;
use App\Models\QuarterlyReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuarterlyReportsController extends BaseApiController
{
    /**
     * Display a listing of quarterly reports.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $query = QuarterlyReport::query()->with('zone', 'supervision', 'supervisor');

        // Scope to user managed zones or supervisions
        if ($currentUser->isPastorZona()) {
            $query->whereIn('zone_id', $currentUser->getManagedZoneIds());
        } elseif ($currentUser->isSupervisor()) {
            $query->whereIn('supervision_id', $currentUser->getManagedSupervisionIds());
        }

        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            QuarterlyReportResource::collection($reports),
            'Relatórios trimestrais recuperados.',
            [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        );
    }

    /**
     * Store a newly created quarterly report.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'supervisor_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'required|integer|min:0',
            'cells_count' => 'required|integer|min:0',
            'timoteos_count' => 'required|integer|min:0',
            'members_count' => 'required|integer|min:0',
            'participants_count' => 'required|integer|min:0',
            'pastors_count' => 'required|integer|min:0',
            'supervisors_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'saved_count' => 'required|integer|min:0',
            'planned_baptism_count' => 'required|integer|min:0',
            'baptized_count' => 'required|integer|min:0',
            'cell_multiplications_count' => 'required|integer|min:0',
            'disciplined_leaders_count' => 'required|integer|min:0',
            'closed_cells_count' => 'required|integer|min:0',
            'discipleship_score' => 'required|integer|min:1|max:10',
            'pastoral_score' => 'required|integer|min:1|max:10',
            'cell_participation_score' => 'required|integer|min:1|max:10',
            'service_participation_score' => 'required|integer|min:1|max:10',
            'communion_in_cells_score' => 'required|integer|min:1|max:10',
            'relationship_building_score' => 'required|integer|min:1|max:10',
            'prayer_intercession_score' => 'required|integer|min:1|max:10',
            'status' => 'required|in:draft,submitted',
            'evangelism_strategy' => 'nullable|string',
            'consolidation_growth' => 'nullable|string',
            'visitation_routine' => 'nullable|string',
            'leader_support' => 'nullable|string',
            'tadium_participation' => 'nullable|string',
            'ministerial_observations' => 'nullable|string',
        ]);

        if ($validated['status'] === 'submitted') {
            $validated['submitted_at'] = now();
        }

        $report = QuarterlyReport::create($validated);

        return $this->sendResponse(new QuarterlyReportResource($report), 'Relatório trimestral criado com sucesso.', [], 201);
    }

    /**
     * Display the specified quarterly report.
     */
    public function show(QuarterlyReport $quarterlyReport): JsonResponse
    {
        $quarterlyReport->load('zone', 'supervision', 'supervisor');
        return $this->sendResponse(new QuarterlyReportResource($quarterlyReport), 'Dados do relatório trimestral carregados.');
    }

    /**
     * Update the specified quarterly report.
     */
    public function update(Request $request, QuarterlyReport $quarterlyReport): JsonResponse
    {
        if ($quarterlyReport->status === 'submitted') {
            return $this->sendError('Relatórios já submetidos não podem ser editados.', [], 403);
        }

        $validated = $request->validate([
            'leaders_count' => 'sometimes|required|integer|min:0',
            'cells_count' => 'sometimes|required|integer|min:0',
            'timoteos_count' => 'sometimes|required|integer|min:0',
            'members_count' => 'sometimes|required|integer|min:0',
            'participants_count' => 'sometimes|required|integer|min:0',
            'pastors_count' => 'sometimes|required|integer|min:0',
            'supervisors_count' => 'sometimes|required|integer|min:0',
            'visitors_count' => 'sometimes|required|integer|min:0',
            'saved_count' => 'sometimes|required|integer|min:0',
            'planned_baptism_count' => 'sometimes|required|integer|min:0',
            'baptized_count' => 'sometimes|required|integer|min:0',
            'cell_multiplications_count' => 'sometimes|required|integer|min:0',
            'disciplined_leaders_count' => 'sometimes|required|integer|min:0',
            'closed_cells_count' => 'sometimes|required|integer|min:0',
            'discipleship_score' => 'sometimes|required|integer|min:1|max:10',
            'pastoral_score' => 'sometimes|required|integer|min:1|max:10',
            'cell_participation_score' => 'sometimes|required|integer|min:1|max:10',
            'service_participation_score' => 'sometimes|required|integer|min:1|max:10',
            'communion_in_cells_score' => 'sometimes|required|integer|min:1|max:10',
            'relationship_building_score' => 'sometimes|required|integer|min:1|max:10',
            'prayer_intercession_score' => 'sometimes|required|integer|min:1|max:10',
            'status' => 'sometimes|required|in:draft,submitted',
            'evangelism_strategy' => 'nullable|string',
            'consolidation_growth' => 'nullable|string',
            'visitation_routine' => 'nullable|string',
            'leader_support' => 'nullable|string',
            'tadium_participation' => 'nullable|string',
            'ministerial_observations' => 'nullable|string',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'submitted') {
            $validated['submitted_at'] = now();
        }

        $quarterlyReport->update($validated);

        return $this->sendResponse(new QuarterlyReportResource($quarterlyReport), 'Relatório trimestral atualizado com sucesso.');
    }

    /**
     * Remove the specified quarterly report.
     */
    public function destroy(QuarterlyReport $quarterlyReport): JsonResponse
    {
        $quarterlyReport->delete();

        return $this->sendResponse(null, 'Relatório trimestral excluído com sucesso.');
    }
}

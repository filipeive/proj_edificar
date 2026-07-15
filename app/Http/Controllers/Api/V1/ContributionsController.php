<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\ContributionResource;
use App\Models\Contribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContributionsController extends BaseApiController
{
    /**
     * Display a listing of contributions.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $query = Contribution::query()->with('user', 'cell', 'package');

        // Scoping
        if ($currentUser->role === 'membro') {
            // Member only sees their own contributions
            $query->where('user_id', $currentUser->id);
        } elseif ($currentUser->isPastorZona()) {
            $query->whereIn('zone_id', $currentUser->getManagedZoneIds());
        } elseif ($currentUser->isSupervisor()) {
            $query->whereIn('supervision_id', $currentUser->getManagedSupervisionIds());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        }

        $contributions = $query->orderBy('contribution_date', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            ContributionResource::collection($contributions),
            'Lista de contribuições carregada.',
            [
                'current_page' => $contributions->currentPage(),
                'last_page' => $contributions->lastPage(),
                'per_page' => $contributions->perPage(),
                'total' => $contributions->total(),
            ]
        );
    }

    /**
     * Store a newly created contribution.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date',
            'package_id' => 'nullable|exists:commitment_packages,id',
            'notes' => 'nullable|string',
            'proof_path' => 'nullable|string',
            'proof_message' => 'nullable|string',
        ]);

        $currentUser = $request->user();
        $validated['user_id'] = $currentUser->id;
        $validated['status'] = 'pendente';

        // Auto assign cell details from user profile
        if ($currentUser->cell) {
            $validated['cell_id'] = $currentUser->cell_id;
            $validated['supervision_id'] = $currentUser->cell->supervision_id;
            $validated['zone_id'] = $currentUser->cell->supervision->zone_id ?? null;
        }

        $contribution = Contribution::create($validated);

        return $this->sendResponse(new ContributionResource($contribution), 'Contribuição registada e pendente de verificação.', [], 201);
    }

    /**
     * Display the specified contribution.
     */
    public function show(Contribution $contribution): JsonResponse
    {
        $contribution->load('user', 'cell', 'package');
        return $this->sendResponse(new ContributionResource($contribution), 'Dados da contribuição carregados.');
    }

    /**
     * Update the specified contribution.
     */
    public function update(Request $request, Contribution $contribution): JsonResponse
    {
        if ($contribution->status !== 'pendente') {
            return $this->sendError('Apenas contribuições pendentes podem ser editadas.', [], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'contribution_date' => 'sometimes|required|date',
            'package_id' => 'nullable|exists:commitment_packages,id',
            'notes' => 'nullable|string',
            'proof_path' => 'nullable|string',
            'proof_message' => 'nullable|string',
        ]);

        $contribution->update($validated);

        return $this->sendResponse(new ContributionResource($contribution), 'Contribuição atualizada com sucesso.');
    }

    /**
     * Remove the specified contribution.
     */
    public function destroy(Contribution $contribution): JsonResponse
    {
        if ($contribution->status !== 'pendente') {
            return $this->sendError('Apenas contribuições pendentes podem ser removidas.', [], 403);
        }

        $contribution->delete();

        return $this->sendResponse(null, 'Contribuição removida com sucesso.');
    }

    /**
     * Verify / approve a pending contribution.
     */
    public function verify(Request $request, Contribution $contribution): JsonResponse
    {
        if ($contribution->status !== 'pendente') {
            return $this->sendError('Esta contribuição não está pendente.', [], 400);
        }

        $contribution->update([
            'status' => 'verificada',
            'verified_by_id' => $request->user()->id,
        ]);

        return $this->sendResponse(new ContributionResource($contribution), 'Contribuição verificada com sucesso.');
    }

    /**
     * Reject a pending contribution.
     */
    public function reject(Request $request, Contribution $contribution): JsonResponse
    {
        if ($contribution->status !== 'pendente') {
            return $this->sendError('Esta contribuição não está pendente.', [], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $contribution->update([
            'status' => 'rejeitada',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by_id' => $request->user()->id,
        ]);

        return $this->sendResponse(new ContributionResource($contribution), 'Contribuição rejeitada com sucesso.');
    }

    /**
     * Cancel a verified contribution.
     */
    public function cancel(Request $request, Contribution $contribution): JsonResponse
    {
        if ($contribution->status !== 'verificada') {
            return $this->sendError('Apenas contribuições verificadas podem ser canceladas.', [], 400);
        }

        $contribution->update([
            'status' => 'cancelada',
            'verified_by_id' => $request->user()->id,
        ]);

        return $this->sendResponse(new ContributionResource($contribution), 'Contribuição cancelada com sucesso.');
    }
}

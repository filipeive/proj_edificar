<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\RequisitionResource;
use App\Models\Requisition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequisitionsController extends BaseApiController
{
    /**
     * Display a listing of requisitions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Requisition::query()->with('user', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            RequisitionResource::collection($requisitions),
            'Lista de requisições de fundos carregada.',
            [
                'current_page' => $requisitions->currentPage(),
                'last_page' => $requisitions->lastPage(),
                'per_page' => $requisitions->perPage(),
                'total' => $requisitions->total(),
            ]
        );
    }

    /**
     * Store a newly created requisition.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'scope' => 'required|in:comissao_obra,regular',
            'proof_file' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = Requisition::STATUS_PENDING;

        $requisition = Requisition::create($validated);

        return $this->sendResponse(new RequisitionResource($requisition), 'Requisição enviada com sucesso.', [], 201);
    }

    /**
     * Display the specified requisition.
     */
    public function show(Requisition $requisition): JsonResponse
    {
        $requisition->load('user', 'approver');
        return $this->sendResponse(new RequisitionResource($requisition), 'Detalhes da requisição carregados.');
    }

    /**
     * Update the specified requisition.
     */
    public function update(Request $request, Requisition $requisition): JsonResponse
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return $this->sendError('Apenas requisições pendentes podem ser editadas.', [], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|max:255',
            'scope' => 'sometimes|required|in:comissao_obra,regular',
            'proof_file' => 'nullable|string',
        ]);

        $requisition->update($validated);

        return $this->sendResponse(new RequisitionResource($requisition), 'Requisição atualizada com sucesso.');
    }

    /**
     * Remove the specified requisition.
     */
    public function destroy(Requisition $requisition): JsonResponse
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return $this->sendError('Apenas requisições pendentes podem ser removidas.', [], 403);
        }

        $requisition->delete();

        return $this->sendResponse(null, 'Requisição removida com sucesso.');
    }

    /**
     * Approve a pending requisition.
     */
    public function approve(Request $request, Requisition $requisition): JsonResponse
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return $this->sendError('Esta requisição não está pendente.', [], 400);
        }

        $requisition->update([
            'status' => Requisition::STATUS_APPROVED,
            'approver_id' => $request->user()->id,
        ]);

        return $this->sendResponse(new RequisitionResource($requisition), 'Requisição aprovada com sucesso.');
    }

    /**
     * Reject a pending requisition.
     */
    public function reject(Request $request, Requisition $requisition): JsonResponse
    {
        if ($requisition->status !== Requisition::STATUS_PENDING) {
            return $this->sendError('Esta requisição não está pendente.', [], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $requisition->update([
            'status' => Requisition::STATUS_REJECTED,
            'approver_id' => $request->user()->id,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return $this->sendResponse(new RequisitionResource($requisition), 'Requisição rejeitada com sucesso.');
    }
}

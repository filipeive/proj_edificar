<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Cells\ReassignMemberAction;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\CellResource;
use App\Models\Cell;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CellsController extends BaseApiController
{
    /**
     * Display a listing of cells.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $query = Cell::query()->with('supervision.zone', 'leader');

        // Apply Scoping Based on Role
        if ($currentUser->isPastorZona()) {
            $query->whereHas('supervision', function ($q) use ($currentUser) {
                $q->whereIn('zone_id', $currentUser->getManagedZoneIds());
            });
        } elseif ($currentUser->isSupervisor()) {
            $query->whereIn('supervision_id', $currentUser->getManagedSupervisionIds());
        }

        // Apply search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('leader', function ($ql) use ($searchTerm) {
                        $ql->where('name', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        $cells = $query->orderBy('name', 'asc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            CellResource::collection($cells),
            'Lista de células recuperada.',
            [
                'current_page' => $cells->currentPage(),
                'last_page' => $cells->lastPage(),
                'per_page' => $cells->perPage(),
                'total' => $cells->total(),
            ]
        );
    }

    /**
     * Store a newly created cell.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:membros,lideres,supervisores,pastores_zona,pastores',
            'supervision_id' => 'required|exists:supervisions,id',
            'leader_id' => 'required|exists:users,id',
        ]);

        $cell = Cell::create($validated);
        
        // Recount members for consistency
        $cell->update(['member_count' => $cell->getMembersCount()]);

        return $this->sendResponse(new CellResource($cell), 'Célula criada com sucesso.', [], 201);
    }

    /**
     * Display the specified cell.
     */
    public function show(Cell $cell): JsonResponse
    {
        $cell->load('supervision.zone', 'leader');
        return $this->sendResponse(new CellResource($cell), 'Dados da célula carregados.');
    }

    /**
     * Update the specified cell.
     */
    public function update(Request $request, Cell $cell): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:membros,lideres,supervisores,pastores_zona,pastores',
            'supervision_id' => 'sometimes|required|exists:supervisions,id',
            'leader_id' => 'sometimes|required|exists:users,id',
        ]);

        $cell->update($validated);
        
        // Recount members for consistency
        $cell->update(['member_count' => $cell->getMembersCount()]);

        return $this->sendResponse(new CellResource($cell), 'Célula atualizada com sucesso.');
    }

    /**
     * Remove the specified cell.
     */
    public function destroy(Cell $cell): JsonResponse
    {
        $cell->delete();

        return $this->sendResponse(null, 'Célula removida com sucesso.');
    }

    /**
     * Transfer a member to another cell.
     */
    public function transferMember(Request $request, ReassignMemberAction $action): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'cell_id' => 'required|exists:cells,id',
        ]);

        $member = User::find($validated['member_id']);
        $action->execute($member, (int) $validated['cell_id']);

        return $this->sendResponse(null, 'Membro transferido de célula com sucesso.');
    }
}

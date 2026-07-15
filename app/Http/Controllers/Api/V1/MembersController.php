<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MembersController extends BaseApiController
{
    /**
     * Display a listing of members with filters, search, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $query = User::query()->with('cell');

        // Apply Scoping Based on Role
        if ($currentUser->isPastorZona()) {
            $query->whereHas('cell.supervision', function ($q) use ($currentUser) {
                $q->whereIn('zone_id', $currentUser->getManagedZoneIds());
            });
        } elseif ($currentUser->isSupervisor()) {
            $query->whereHas('cell', function ($q) use ($currentUser) {
                $q->whereIn('supervision_id', $currentUser->getManagedSupervisionIds());
            });
        }

        // Apply Filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        $query->orderBy('name', 'asc');
        $members = $query->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            UserResource::collection($members),
            'Lista de membros recuperada.',
            [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ]
        );
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,super_admin,comissao_obra,responsavel_pacote,tesouraria,pastor,pastor_senior,administracao',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $member = User::create($validated);

        return $this->sendResponse(new UserResource($member), 'Membro criado com sucesso.', [], 201);
    }

    /**
     * Display the specified member.
     */
    public function show(User $member): JsonResponse
    {
        $member->load('cell');
        return $this->sendResponse(new UserResource($member), 'Dados do membro carregados.');
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, User $member): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($member->id),
            ],
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string',
            'role' => 'sometimes|required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,super_admin,comissao_obra,responsavel_pacote,tesouraria,pastor,pastor_senior,administracao',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $member->update($validated);

        return $this->sendResponse(new UserResource($member), 'Membro atualizado com sucesso.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(User $member): JsonResponse
    {
        $member->delete();

        return $this->sendResponse(null, 'Membro removido com sucesso.');
    }
}

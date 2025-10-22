<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\User;
use App\Models\Supervision;
use App\Models\Zone;
use App\Models\CommitmentPackage;
use App\Notifications\MemberCreatedNotification;
use App\Notifications\MemberAddedToCellNotification;
use App\Notifications\UserPromotedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController
{
    use AuthorizesRequests;

    // ==================== ADMIN ROUTES ====================
    
    public function index(): View
    {
        $users = User::with('cell')->paginate(10);
        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin'];
        return view('admin.users.create', ['cells' => $cells, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,admin',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        // Notificar novo usuário
        $user->notify(new MemberCreatedNotification($user, $request->password));

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', ['user' => $user->load('cell', 'commitments')]);
    }

    public function edit(User $user): View
    {
        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin'];
        return view('admin.users.edit', [
            'user' => $user,
            'cells' => $cells,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,admin',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $oldRole = $user->role;
        $user->update($validated);

        // Notificar se mudou o role
        if ($oldRole !== $validated['role']) {
            $user->notify(new UserPromotedNotification($user, $oldRole, $validated['role']));
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Não pode deletar admin!');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Utilizador deletado com sucesso!');
    }

    // ==================== MEMBERS CONTEXT ROUTES ====================

    /**
     * Lista membros com filtro hierárquico
     */
    public function members(Request $request): View
    {
        $user = auth()->user();
        $membersQuery = User::where('role', 'membro')
            ->with(['cell.supervision.zone', 'commitments']);

        // Aplicar filtro hierárquico
        $membersQuery = $this->applyHierarchyFilter($membersQuery, $user);

        // Filtros opcionais
        if ($request->filled('cell_id')) {
            $membersQuery->where('cell_id', $request->cell_id);
        }

        if ($request->filled('search')) {
            $membersQuery->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        $members = $membersQuery->paginate(15);

        // Buscar células disponíveis para filtro
        $availableCells = $this->getAvailableCells($user);

        return view('members.index', [
            'members' => $members,
            'availableCells' => $availableCells,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Formulário para criar membro (contextual)
     */
    public function createFromContext(Request $request): View
    {
        $user = auth()->user();
        $availableCells = $this->getAvailableCells($user);
        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        // Se for líder de célula, pré-selecionar sua célula
        $selectedCell = null;
        if ($user->role === 'lider_celula') {
            $selectedCell = $user->cell;
        }

        return view('members.create', [
            'availableCells' => $availableCells,
            'packages' => $packages,
            'userRole' => $user->role,
            'selectedCell' => $selectedCell,
        ]);
    }

    /**
     * Salvar membro (contextual)
     */
    public function storeFromContext(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'cell_id' => 'required|exists:cells,id',
            'package_id' => 'nullable|exists:commitment_packages,id',
            'committed_amount' => 'nullable|numeric|min:0',
            'password' => 'required|min:6|confirmed',
        ]);

        // Validar permissão para criar nesta célula
        $cell = Cell::findOrFail($validated['cell_id']);
        $this->validateCellPermission($user, $cell);

        $plainPassword = $validated['password'];

        // Criar membro
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($plainPassword),
            'cell_id' => $validated['cell_id'],
            'role' => 'membro',
            'is_active' => true,
        ]);

        // Criar compromisso se especificado
        if ($validated['package_id']) {
            $package = CommitmentPackage::find($validated['package_id']);
            \App\Models\UserCommitment::create([
                'user_id' => $newUser->id,
                'package_id' => $package->id,
                'start_date' => now(),
            ]);
        }

        // Notificações
        $newUser->notify(new MemberCreatedNotification($newUser, $plainPassword));
        
        if ($cell->leader_id && $cell->leader_id !== $user->id) {
            $cell->leader->notify(new MemberAddedToCellNotification($newUser));
        }

        return redirect()->route('members.index')
            ->with('success', 'Membro criado com sucesso!');
    }

    /**
     * Ver detalhes do membro (contextual)
     */
    public function showFromContext(User $member): View
    {
        $user = auth()->user();
        
        // Validar se pode ver este membro
        $this->validateMemberAccess($user, $member);

        $member->load(['cell.supervision.zone', 'commitments.package', 'contributions']);

        return view('members.show', [
            'member' => $member,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Editar membro (contextual)
     */
    public function editFromContext(User $member): View
    {
        $user = auth()->user();
        
        // Validar se pode editar este membro
        $this->validateMemberAccess($user, $member);

        $availableCells = $this->getAvailableCells($user);
        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        return view('members.edit', [
            'member' => $member,
            'availableCells' => $availableCells,
            'packages' => $packages,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Atualizar membro (contextual)
     */
    public function updateFromContext(Request $request, User $member)
    {
        $user = auth()->user();
        
        // Validar se pode editar
        $this->validateMemberAccess($user, $member);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$member->id}",
            'phone' => 'nullable|string',
            'cell_id' => 'required|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        // Validar permissão para mover para esta célula
        $cell = Cell::findOrFail($validated['cell_id']);
        $this->validateCellPermission($user, $cell);

        $member->update($validated);

        return redirect()->route('members.show', $member)
            ->with('success', 'Membro atualizado com sucesso!');
    }

    /**
     * Deletar membro (contextual)
     */
    public function destroyFromContext(User $member)
    {
        $user = auth()->user();
        
        // Validar se pode deletar
        $this->validateMemberAccess($user, $member);

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Membro removido com sucesso!');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Aplicar filtro hierárquico baseado no role
     */
    private function applyHierarchyFilter($query, $user)
    {
        switch ($user->role) {
            case 'lider_celula':
                // Vê apenas membros da sua célula
                $query->where('cell_id', $user->cell_id);
                break;

            case 'supervisor':
                // Vê membros de todas as células da sua supervisão
                $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
                $query->whereIn('cell_id', $cellIds);
                break;

            case 'pastor_zona':
                // Vê membros de todas as supervisões da sua zona
                if ($user->cell && $user->cell->supervision && $user->cell->supervision->zone) {
                    $supervisionIds = Supervision::where('zone_id', $user->cell->supervision->zone_id)->pluck('id');
                    $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                    $query->whereIn('cell_id', $cellIds);
                }
                break;

            case 'admin':
                // Admin vê todos
                break;

            default:
                // Outros roles não podem ver membros
                $query->where('id', 0);
                break;
        }

        return $query;
    }

    /**
     * Buscar células disponíveis baseado no role
     */
    private function getAvailableCells($user)
    {
        $cellsQuery = Cell::with('supervision.zone');

        switch ($user->role) {
            case 'lider_celula':
                // Apenas sua célula
                $cellsQuery->where('id', $user->cell_id);
                break;

            case 'supervisor':
                // Células da sua supervisão
                $cellsQuery->where('supervision_id', $user->cell->supervision_id);
                break;

            case 'pastor_zona':
                // Células de todas as supervisões da zona
                if ($user->cell && $user->cell->supervision && $user->cell->supervision->zone) {
                    $supervisionIds = Supervision::where('zone_id', $user->cell->supervision->zone_id)->pluck('id');
                    $cellsQuery->whereIn('supervision_id', $supervisionIds);
                }
                break;

            case 'admin':
                // Todas as células
                break;
        }

        return $cellsQuery->orderBy('name')->get();
    }

    /**
     * Validar se usuário pode acessar/editar este membro
     */
    private function validateMemberAccess($user, $member)
    {
        if ($user->role === 'admin') return;

        if ($user->role === 'lider_celula') {
            if ($member->cell_id !== $user->cell_id) {
                abort(403, 'Você só pode gerenciar membros da sua célula');
            }
        }

        if ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            if (!$cellIds->contains($member->cell_id)) {
                abort(403, 'Você só pode gerenciar membros da sua supervisão');
            }
        }

        if ($user->role === 'pastor_zona') {
            $supervisionIds = Supervision::where('zone_id', $user->cell->supervision->zone_id)->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            if (!$cellIds->contains($member->cell_id)) {
                abort(403, 'Você só pode gerenciar membros da sua zona');
            }
        }
    }

    /**
     * Validar se usuário pode criar membro nesta célula
     */
    private function validateCellPermission($user, $cell)
    {
        if ($user->role === 'admin') return;

        if ($user->role === 'lider_celula') {
            if ($cell->id !== $user->cell_id) {
                abort(403, 'Você só pode criar membros na sua célula');
            }
        }

        if ($user->role === 'supervisor') {
            if ($cell->supervision_id !== $user->cell->supervision_id) {
                abort(403, 'Você só pode criar membros nas células da sua supervisão');
            }
        }

        if ($user->role === 'pastor_zona') {
            $supervisionIds = Supervision::where('zone_id', $user->cell->supervision->zone_id)->pluck('id');
            if (!$supervisionIds->contains($cell->supervision_id)) {
                abort(403, 'Você só pode criar membros na sua zona');
            }
        }
    }
}
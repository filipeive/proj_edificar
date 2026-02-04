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

    public function index(Request $request): View
    {
        // Bloquear acesso para secretaria e outros não-admins
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $query = User::with('cell');

        // Filtro por role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        // Filtro por célula
        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        }

        // Busca por nome ou email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Ordenação
        $query->orderBy('name', 'asc');

        $users = $query->paginate(15);

        // Buscar todas as células para o filtro
        $cells = Cell::orderBy('name')->get();

        // Estatísticas para os cards
        $totalUsers = User::count();
        $totalMembers = User::where('role', 'membro')->count();
        $totalLeaders = User::where('role', 'lider_celula')->count();
        $totalActive = User::where('is_active', true)->count();

        return view('admin.users.index', [
            'users' => $users,
            'cells' => $cells,
            'totalUsers' => $totalUsers,
            'totalMembers' => $totalMembers,
            'totalLeaders' => $totalLeaders,
            'totalActive' => $totalActive,
        ]);
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
            'phone' => 'nullable|moz_phone',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,comissao_obra,responsavel_pacote,tesouraria,pastor_senior',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $plainPassword = $validated['password'];
        $validated['password'] = bcrypt($plainPassword);
        $user = User::create($validated);

        // Notificar novo usuário
        if ($user->wantsNotification('member_created')) {
            $user->notify(new MemberCreatedNotification($user, $plainPassword));
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('admin.users.show', ['user' => $user->load('cell', 'commitments')]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
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
            'phone' => 'nullable|moz_phone',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,comissao_obra,responsavel_pacote,tesouraria,pastor_senior',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $oldRole = $user->role;
        $user->update($validated);

        // Notificar se mudou o role
        if ($oldRole !== $validated['role']) {
            if ($user->wantsNotification('user_promoted')) {
                $user->notify(new UserPromotedNotification($user, $oldRole, $validated['role']));
            }
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

    /**
     * Alternar status de ativação do utilizador
     */
    public function toggleStatus(User $user)
    {
        if ($user->role === 'admin' && $user->id === auth()->id()) {
            return back()->with('error', 'Você não pode desativar sua própria conta de admin!');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        return back()->with('success', "Utilizador {$status} com sucesso!");
    }

    /**
     * Redefinir senha do utilizador (Admin)
     */
    public function resetPassword(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Não pode redefinir senha de admin!');
        }

        $user->update([
            'password' => Hash::make('mudar123')
        ]);

        return back()->with('success', "Senha de {$user->name} redefinida para: mudar123");
    }

    /**
     * Bulk delete users
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $validated['user_ids'];

        // Prevent deleting admins
        $deletedCount = User::whereIn('id', $userIds)
            ->where('role', '!=', 'admin')
            ->delete();

        return redirect()->route('users.index')
            ->with('success', "{$deletedCount} utilizador(es) deletado(s) com sucesso!");
    }

    // ==================== MEMBERS CONTEXT ROUTES ====================

    /**
     * Lista membros com filtro hierárquico
     */
    public function members(Request $request): View
    {
        $user = auth()->user();
        $membersQuery = User::whereIn('role', ['membro', 'lider_celula'])
            ->with(['cell.supervision.zone', 'commitments']);

        // Aplicar filtro hierárquico
        $membersQuery = $this->applyHierarchyFilter($membersQuery, $user);

        // Filtros opcionais
        if ($request->filled('cell_id')) {
            $membersQuery->where('cell_id', $request->cell_id);
        }

        if ($request->filled('search')) {
            $membersQuery->where(function ($q) use ($request) {
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

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        $availableCells = $this->getAvailableCells($user);
        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        // Se for líder de célula, pré-selecionar sua célula
        $selectedCell = null;
        if ($user->role === 'lider_celula') {
            $selectedCell = $user->cell;
        }

        // Definir papéis permitidos
        $allowedRoles = ['membro'];
        if ($user->isAdmin() || $user->isPastorZona() || $user->isSupervisor()) {
            $allowedRoles[] = 'lider_celula';
        }

        return view('members.create', [
            'availableCells' => $availableCells,
            'packages' => $packages,
            'userRole' => $user->role,
            'selectedCell' => $selectedCell,
            'allowedRoles' => $allowedRoles,
        ]);
    }

    /**
     * Salvar membro (contextual)
     */
    public function storeFromContext(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|moz_phone',
            'cell_id' => 'required|exists:cells,id',
            'role' => 'required|in:membro,lider_celula',
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
            'role' => $validated['role'], // Usar o role validado
            'is_active' => true,
        ]);

        // Criar compromisso se especificado
        if ($validated['package_id']) {
            $package = CommitmentPackage::find($validated['package_id']);
            \App\Models\UserCommitment::create([
                'user_id' => $newUser->id,
                'package_id' => $package->id,
                'cell_id' => $newUser->cell_id,
                'committed_amount' => $validated['committed_amount'] ?? $package->min_amount,
                'start_date' => now(),
            ]);
        }

        // Notificações
        if ($newUser->wantsNotification('member_created')) {
            $newUser->notify(new MemberCreatedNotification($newUser, $plainPassword));
        }

        if ($cell->leader_id && $cell->leader_id !== $user->id) {
            if ($cell->leader->wantsNotification('member_added_to_cell')) {
                $cell->leader->notify(new MemberAddedToCellNotification($newUser));
            }
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

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        // Validar se pode editar este membro
        $this->validateMemberAccess($user, $member);

        $availableCells = $this->getAvailableCells($user);
        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        // Definir papéis permitidos
        $allowedRoles = ['membro'];
        if ($user->isAdmin() || $user->isPastorZona() || $user->isSupervisor()) {
            $allowedRoles[] = 'lider_celula';
        }

        return view('members.edit', [
            'member' => $member,
            'availableCells' => $availableCells,
            'packages' => $packages,
            'userRole' => $user->role,
            'allowedRoles' => $allowedRoles,
        ]);
    }

    /**
     * Atualizar membro (contextual)
     */
    public function updateFromContext(Request $request, User $member)
    {
        $user = auth()->user();

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        // Validar se pode editar
        $this->validateMemberAccess($user, $member);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$member->id}",
            'phone' => 'nullable|moz_phone',
            'cell_id' => 'required|exists:cells,id',
            'role' => 'required|in:membro,lider_celula',
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

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        // Validar se pode deletar
        $this->validateMemberAccess($user, $member);

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Membro removido com sucesso!');
    }

    /**
     * Deletar múltiplos membros (contextual)
     */
    public function bulkDestroyFromContext(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'secretaria') {
            abort(403, 'Acesso de leitura apenas.');
        }

        $validated = $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:users,id'
        ]);

        $count = 0;
        foreach ($validated['selected_ids'] as $id) {
            $member = User::find($id);
            if ($member) {
                // Check permissions for each member
                // If validation fails, it usually throws 403. 
                // We might want to skip instead of aborting the whole process, 
                // but strictly speaking, the UI shouldn't allow selecting them.
                // Let's wrap in try/catch or just checking strict access would be safer but slower.
                // For simplicity and security, we'll verify access.
                try {
                    $this->validateMemberAccess($user, $member);
                    $member->delete();
                    $count++;
                } catch (\Exception $e) {
                    // Skip unauthorized
                    continue;
                }
            }
        }

        return redirect()->route('members.index')
            ->with('success', "{$count} membro(s) removido(s) com sucesso!");
    }

    // ==================== HELPER METHODS ====================

    /**
     * Aplicar filtro hierárquico baseado no role
     */
    private function applyHierarchyFilter($query, $user)
    {
        switch ($user->role) {
            case 'lider_celula':
                // Vê apenas membros e líderes (se houver mais de um, o que é raro) da sua célula
                $query->where('cell_id', $user->cell_id);
                break;

            case 'supervisor':
                // Vê membros de todas as células da sua supervisão
                $cellIds = Cell::whereIn('supervision_id', $user->getManagedSupervisionIds())->pluck('id');
                $query->whereIn('cell_id', $cellIds);
                break;


            case 'pastor_zona':
                // Vê membros e líderes de todas as supervisões da sua zona
                $zoneId = $user->getZoneId();
                if ($zoneId) {
                    $supervisionIds = Supervision::where('zone_id', $zoneId)->pluck('id');
                    $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                    $query->whereIn('cell_id', $cellIds);
                }
                break;

            case 'admin':
            case 'pastor_senior':
            case 'secretaria':
            case 'tesouraria':
                // Admin e roles equivalentes vêem todos
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
                $cellsQuery->whereIn('supervision_id', $user->getManagedSupervisionIds());
                break;

            case 'pastor_zona':
                // Células de todas as supervisões da zona
                $zoneId = $user->getZoneId();
                if ($zoneId) {
                    $supervisionIds = Supervision::where('zone_id', $zoneId)->pluck('id');
                    $cellsQuery->whereIn('supervision_id', $supervisionIds);
                }
                break;

            case 'admin':
            case 'pastor_senior':
            case 'secretaria':
            case 'tesouraria':
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
        if ($user->isAdmin())
            return;

        if ($user->role === 'lider_celula') {
            if ($member->cell_id !== $user->cell_id) {
                abort(403, 'Você só pode gerenciar membros da sua célula');
            }
        }

        if ($user->role === 'supervisor') {
            $cellIds = Cell::whereIn('supervision_id', $user->getManagedSupervisionIds())->pluck('id');
            if (!$cellIds->contains($member->cell_id)) {
                abort(403, 'Você só pode gerenciar membros da sua supervisão');
            }
        }

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            if (!$zoneId) {
                abort(403, 'Zona não encontrada para este pastor.');
            }
            $supervisionIds = Supervision::where('zone_id', $zoneId)->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            if (!$cellIds->contains($member->cell_id)) {
                abort(403, 'Você só pode gerenciar membros da sua zona');
            }
        }
    }

    public function reassignCell(Request $request, User $user)
    {
        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
        ]);

        $user->update(['cell_id' => $validated['cell_id']]);

        return back()->with('success', 'Membro transferido com sucesso!');
    }

    public function removeFromCell(User $user)
    {
        // Apenas remove da célula, não deleta do sistema
        $user->update(['cell_id' => null]);

        return back()->with('success', 'Membro removido da célula com sucesso!');
    }

    public function updateObservations(Request $request, User $user)
    {
        $validated = $request->validate([
            'observations' => 'nullable|string',
        ]);

        $user->update(['observations' => $validated['observations']]);

        return back()->with('success', 'Observações atualizadas com sucesso!');
    }

    /**
     * Validar se usuário pode criar membro nesta célula
     */
    private function validateCellPermission($user, $cell)
    {
        if ($user->isAdmin())
            return;

        if ($user->role === 'lider_celula') {
            if ($cell->id !== $user->cell_id) {
                abort(403, 'Você só pode criar membros na sua célula');
            }
        }

        if ($user->role === 'supervisor') {
            if (!$user->getManagedSupervisionIds()->contains($cell->supervision_id)) {
                abort(403, 'Você só pode criar membros nas células da sua supervisão');
            }
        }

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            if (!$zoneId) {
                abort(403, 'Zona não encontrada para este pastor.');
            }
            $supervisionIds = Supervision::where('zone_id', $zoneId)->pluck('id');
            if (!$supervisionIds->contains($cell->supervision_id)) {
                abort(403, 'Você só pode criar membros na sua zona');
            }
        }
    }
}

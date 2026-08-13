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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        // Bloquear acesso para não-admins e não-pastor_senior
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastorSenior()) {
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
        $totalAdministracao = User::where('role', 'administracao')->count();
        $totalActive = User::where('is_active', true)->count();

        return view('admin.users.index', [
            'users' => $users,
            'cells' => $cells,
            'totalUsers' => $totalUsers,
            'totalMembers' => $totalMembers,
            'totalLeaders' => $totalLeaders,
            'totalAdministracao' => $totalAdministracao,
            'totalActive' => $totalActive,
        ]);
    }

    public function create(): View
    {
        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'administracao', 'admin', 'super_admin'];
        return view('admin.users.create', [
            'cells' => $cells,
            'roles' => $roles,
            'canManageAdminRoles' => auth()->user()->isSuperAdmin(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|moz_phone',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,super_admin,comissao_obra,responsavel_pacote,tesouraria,pastor,pastor_senior,administracao',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $this->validateRoleAssignment($validated['role']);

        $plainPassword = $validated['password'];
        $validated['password'] = bcrypt($plainPassword);
        $user = User::create($validated);

        // Log activity
        auth()->user()->logActivity('create', "Criou o utilizador {$user->name}", $user);

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
        $user->load('cell', 'commitments');

        $relatedCells = collect();

        if ($user->isLider()) {
            $relatedCells = $user->ledCells()->with('supervision')->get();
        } elseif ($user->isTimoteo()) {
            $relatedCells = $user->timoteoCells()->with('supervision')->get();
        } elseif ($user->isSupervisor() || $user->isSubSupervisor()) {
            $relatedCells = Cell::whereIn('supervision_id', $user->supervisedSupervisions()->pluck('id'))
                ->orWhereIn('supervision_id', $user->subSupervisedSupervisions()->pluck('id'))
                ->with('supervision')
                ->get();
        } elseif ($user->isPastorZona() || $user->isSubPastorZona()) {
            $zones = Zone::where('pastor_id', $user->id)->get();
            $supervisionIds = $zones->flatMap(fn($z) => $z->supervisions()->pluck('id'));
            $relatedCells = Cell::whereIn('supervision_id', $supervisionIds)->with('supervision')->get();
        } elseif ($user->isPastor() || $user->isSubPastor()) {
            $zones = Zone::where('pastor_id', $user->id)->get();
            $supervisionIds = $zones->flatMap(fn($z) => $z->supervisions()->pluck('id'));
            $relatedCells = Cell::whereIn('supervision_id', $supervisionIds)->with('supervision')->get();
        } elseif ($user->isAdmin() || $user->isSuperAdmin() || $user->isPastorSenior()) {
            $relatedCells = Cell::with('supervision')->get();
        } elseif ($user->cell) {
            $relatedCells = Cell::where('id', $user->cell_id)->with('supervision')->get();
        }

        return view('admin.users.show', [
            'user' => $user,
            'relatedCells' => $relatedCells,
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        if (!$this->canManagePrivilegedUser($user)) {
            abort(403, 'Apenas super admin pode gerir contas admin/super admin.');
        }

        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'administracao', 'admin', 'super_admin'];
        return view('admin.users.edit', [
            'user' => $user,
            'cells' => $cells,
            'roles' => $roles,
            'canManageAdminRoles' => auth()->user()->isSuperAdmin(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (!$this->canManagePrivilegedUser($user)) {
            return back()->with('error', 'Apenas super admin pode gerir contas admin/super admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|moz_phone',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,secretaria,admin,super_admin,comissao_obra,responsavel_pacote,tesouraria,pastor,pastor_senior,administracao',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
            'menu_permissions' => 'nullable|array',
        ]);

        $this->validateRoleAssignment($validated['role']);

        $oldRole = $user->role;
        $user->update($validated);

        // Log activity
        auth()->user()->logActivity('update', "Atualizou o utilizador {$user->name}", $user);

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
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Conta super admin não pode ser deletada.');
        }

        if (!$this->canManagePrivilegedUser($user)) {
            return back()->with('error', 'Apenas super admin pode deletar contas admin.');
        }

        // Log activity before deletion
        auth()->user()->logActivity('delete', "Eliminou o utilizador {$user->name} ({$user->email})");

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Utilizador deletado com sucesso!');
    }

    /**
     * Ver histórico de atividades do utilizador
     */
    public function activity(Request $request, User $user): View
    {
        $search = trim((string) $request->query('q', ''));

        $activitiesQuery = $user->activities();
        if ($search !== '') {
            $activitiesQuery->where(function ($query) use ($search) {
                $query->where('action', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('model_type', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%');

                if (is_numeric($search)) {
                    $query->orWhere('model_id', (int) $search);
                }
            });
        }

        $activities = $activitiesQuery->paginate(20)->withQueryString();

        return view('admin.users.activity', [
            'user' => $user,
            'activities' => $activities,
            'search' => $search,
        ]);
    }

    /**
     * Alternar status de ativação do utilizador
     */
    public function toggleStatus(User $user)
    {
        if ($user->isAdmin() && $user->id === auth()->id()) {
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
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Senha de super admin não pode ser redefinida por esta rota.');
        }

        if (!$this->canManagePrivilegedUser($user)) {
            return back()->with('error', 'Apenas super admin pode redefinir senha de admin.');
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

        $query = User::whereIn('id', $userIds)->where('role', '!=', 'super_admin');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('role', '!=', 'admin');
        }

        $deletedCount = $query->delete();

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
        $membersQuery = User::whereIn('role', ['membro', 'lider_celula', 'timoteo'])
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

        // Se for líder de uma única célula, pré-selecionar; se gere várias, manter seletor.
        $selectedCell = null;
        if ($user->role === 'lider_celula') {
            $managedCellIds = $user->getManagedCellIds();
            if ($request->filled('cell_id') && $managedCellIds->contains((int) $request->cell_id)) {
                $selectedCell = Cell::find($request->cell_id);
            } elseif ($managedCellIds->count() === 1) {
                $selectedCell = Cell::find($managedCellIds->first());
            }
        }

        // Pré-preenchimento opcional a partir de um visitante
        $prefill = [];
        if ($request->filled('visitor_id')) {
            $visitor = \App\Models\Visitor::find($request->visitor_id);
            if ($visitor) {
                $prefill = [
                    'name' => $visitor->name,
                    'phone' => $visitor->phone,
                    'cell_id' => $visitor->cell_id,
                ];
                if (!$selectedCell && $visitor->cell_id) {
                    $selectedCell = Cell::find($visitor->cell_id);
                }
            }
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
            'prefill' => $prefill,
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

        if ($validated['role'] === 'lider_celula') {
            if ($response = $this->validateLeaderForCellType($request, $user->find($validated['cell_id'])?->leader ?? $user, $cell->type, 'O líder selecionado não é compatível com o tipo de célula selecionado.')) {
                return $response;
            }
        } else {
            if ($response = $this->validateMemberForCellType($request, $user, $cell->type, 'Este membro não é compatível com o tipo de célula selecionado.')) {
                return $response;
            }
        }

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

        // Integrar visitante se aplicável
        if ($request->filled('visitor_id')) {
            $visitor = \App\Models\Visitor::find($request->visitor_id);
            if ($visitor) {
                $visitor->update(['contact_status' => 'integrado']);
            }
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

        if ($user->id === $member->id) {
            abort(403, 'Não é permitido editar o seu próprio perfil a partir da área de membros. Utilize as configurações de perfil.');
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

        if ($user->id === $member->id) {
            abort(403, 'Não é permitido editar o seu próprio perfil a partir da área de membros. Utilize as configurações de perfil.');
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

        if ($validated['role'] === 'lider_celula') {
            if ($response = $this->validateLeaderForCellType($request, $member, $cell->type, 'O líder selecionado não é compatível com o tipo de célula selecionado.')) {
                return $response;
            }
        } else {
            if ($response = $this->validateMemberForCellType($request, $member, $cell->type, 'Este membro não é compatível com o tipo de célula selecionado.')) {
                return $response;
            }
        }

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

        if ($user->id === $member->id) {
            abort(403, 'Não é permitido remover ou alterar o seu próprio perfil a partir da área de membros.');
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
                // Vê membros das células que lidera diretamente.
                $query->whereIn('cell_id', $user->getManagedCellIds());
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
            case 'super_admin':
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
                // Apenas células sob sua gestão direta
                $cellsQuery->whereIn('id', $user->getManagedCellIds());
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
            case 'super_admin':
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
            if (!$user->getManagedCellIds()->contains($member->cell_id)) {
                abort(403, 'Você só pode gerenciar membros das células que lidera');
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

    public function reassignCell(Request $request, User $user, \App\Actions\Cells\ReassignMemberAction $action)
    {
        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
        ]);

        $cell = Cell::findOrFail($validated['cell_id']);
        $this->validateCellPermission(auth()->user(), $cell);
        if ($response = $this->validateMemberForCellType($request, $user, $cell->type, "O membro {$user->name} não é compatível com o tipo de célula selecionado.")) {
            return $response;
        }

        $action->execute($user, (int) $validated['cell_id']);

        return back()->with('success', 'Membro transferido com sucesso!');
    }

    public function removeFromCell(User $user, \App\Actions\Cells\ReassignMemberAction $action)
    {
        // Apenas remove da célula, não deleta do sistema
        $action->execute($user, null);

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

      public function assignMemberToCell(Request $request, User $member)
      {
          $user = auth()->user();
          if ($user->id === $member->id) {
              abort(403, 'Não é permitido alterar a sua própria célula a partir da área de membros.');
          }
          $validated = $request->validate([
              'cell_id' => 'required|exists:cells,id',
          ]);

          $cell = Cell::findOrFail($validated['cell_id']);
          $this->validateCellPermission($user, $cell);
          if ($response = $this->validateMemberForCellType($request, $member, $cell->type, "O membro {$member->name} não é compatível com o tipo de célula selecionado.")) {
            return $response;
        }

          $member->update(['cell_id' => $validated['cell_id']]);

          return back()->with('success', "Membro {$member->name} foi associado à célula com sucesso!");
      }

    /**
     * Validar se usuário pode criar membro nesta célula
     */
    private function validateCellPermission($user, $cell)
    {
        if ($user->isAdmin())
            return;

        if ($user->role === 'lider_celula') {
            if (!$user->getManagedCellIds()->contains($cell->id)) {
                abort(403, 'Você só pode criar membros nas células que lidera');
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

    private function validateRoleAssignment(string $targetRole): void
    {
        if (in_array($targetRole, ['admin', 'super_admin'], true) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Apenas super admin pode atribuir papéis admin/super admin.');
        }
    }

    private function canManagePrivilegedUser(User $target): bool
    {
        if (!in_array($target->role, ['admin', 'super_admin'], true)) {
            return true;
        }

        return auth()->user()->isSuperAdmin();
    }

    private function validateMemberForCellType(Request $request, User $member, string $cellType, string $message): RedirectResponse|JsonResponse|null
    {
        $role = $member->role;

        $valid = match ($cellType) {
            \App\Models\Cell::TYPE_MEMBROS => in_array($role, ['membro', 'timoteo', 'lider_celula']),
            \App\Models\Cell::TYPE_LIDERES => $role === 'lider_celula',
            \App\Models\Cell::TYPE_SUPERVISORES => in_array($role, ['supervisor', 'sub_supervisor']),
            \App\Models\Cell::TYPE_PASTORES_ZONA => in_array($role, ['pastor_zona', 'pastor']),
            \App\Models\Cell::TYPE_PASTORES => in_array($role, ['pastor', 'pastor_senior']),
            default => true,
        };

        if ($valid) {
            return null;
        }

        // Pedidos AJAX/JSON recebem uma resposta JSON 422 normalizada; formulários
        // normais são redirecionados com erro flash (toast) + mensagem no campo.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['role' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['role' => $message])
            ->with('error', $message);
    }

    private function validateLeaderForCellType(Request $request, User $leader, string $cellType, string $message): RedirectResponse|JsonResponse|null
    {
        $role = $leader->role;

        // Regras alinhadas com CellController (células de líderes podem ser lideradas
        // por supervisores, sub-supervisores, pastores de zona, pastores e pastor sénior).
        $valid = match ($cellType) {
            \App\Models\Cell::TYPE_MEMBROS => true,
            \App\Models\Cell::TYPE_LIDERES => in_array($role, ['supervisor', 'sub_supervisor', 'pastor_zona', 'pastor', 'pastor_senior']),
            \App\Models\Cell::TYPE_SUPERVISORES => in_array($role, ['pastor_zona', 'pastor', 'pastor_senior']),
            \App\Models\Cell::TYPE_PASTORES_ZONA => in_array($role, ['pastor_senior']),
            \App\Models\Cell::TYPE_PASTORES => in_array($role, ['pastor_senior']),
            default => true,
        };

        if ($valid) {
            return null;
        }

        // Pedidos AJAX/JSON recebem uma resposta JSON 422 normalizada; formulários
        // normais são redirecionados com erro flash (toast) + mensagem no campo.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['role' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['role' => $message])
            ->with('error', $message);
    }
}

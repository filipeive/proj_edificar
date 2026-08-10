<?php

namespace App\Http\Controllers\Contribution;

use App\Models\Cell;
use App\Models\Contribution;
use App\Models\User;
use App\Models\UserCommitment;
use App\Models\CommitmentPackage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Notification;

// Modelos de Notificação
use App\Notifications\ContributionCreatedNotification;
use App\Notifications\ContributionPendingValidationNotification;
use App\Notifications\ContributionVerifiedNotification;
use App\Notifications\ContributionVerifiedForManagerNotification;
use App\Notifications\ContributionRejectedNotification;
use App\Notifications\ContributionRejectedForManagerNotification;

class ContributionController
{
    use AuthorizesRequests;  // ADICIONAR ISTO!

    // app/Http/Controllers/Contribution/ContributionController.php

    public function index(Request $request): View
    {
        $user = auth()->user();
        $isMine = $request->query('mine');
        $scope = $request->query('scope');
        $isMyCellScope = $scope === 'my_cell';
        $cellScopeUnavailable = false;
        $defaultPackageId = null;
        $managedPackages = collect();

        $contributions = Contribution::query()
            ->with('user', 'cell');

        // Lógica para "Minhas Contribuições" vs. Visualização Hierárquica
        if ($isMine) {
            $contributions->where('user_id', $user->id);
        } elseif ($isMyCellScope) {
            $managedCellIds = $user->isLider() ? $user->getManagedCellIds() : collect([$user->cell_id])->filter();
            if ($managedCellIds->isNotEmpty()) {
                $contributions->whereIn('cell_id', $managedCellIds);
            } else {
                $cellScopeUnavailable = true;
                $contributions->where('id', 0);
            }
        } else {
            switch ($user->role) {
                case 'membro':
                    $contributions->where('user_id', $user->id);
                    break;
                case 'lider_celula':
                    $contributions->whereIn('cell_id', $user->getManagedCellIds());
                    break;
                case 'supervisor':
                    // Protege quando o utilizador não tem célula atribuída
                    if (!$user->cell || !$user->cell->supervision_id) {
                        // Nenhuma célula sob supervisão => resultado vazio
                        $contributions->where('id', 0);
                        break;
                    }
                    $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
                    $contributions->whereIn('cell_id', $cellIds);
                    break;
                case 'pastor_zona':
                    if ($user->cell && $user->cell->supervision && $user->cell->supervision->zone) {
                        $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
                        $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                        $contributions->whereIn('cell_id', $cellIds);
                    } else {
                        $contributions->where('id', 0);
                    }
                    break;
                case 'super_admin':
                case 'admin':
                case 'comissao_obra':
                    break;
                case 'responsavel_pacote':
                    $managedPackages = $user->managedPackages()->where('is_active', true)->orderBy('order')->get();
                    $packageIds = $managedPackages->pluck('id');
                    $defaultPackageId = $request->query('package_id') ?: $packageIds->first();
                    if ($defaultPackageId) {
                        $request->merge(['package_id' => $defaultPackageId]);
                    }
                    $contributions->whereIn('package_id', $packageIds);
                    break;
                default:
                    $contributions->where('user_id', $user->id);
                    break;
            }
        }

        // Filtros Adicionais
        if ($request->filled('search')) {
            $search = $request->query('search');
            $contributions->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $contributions->where('status', $request->query('status'));
        }

        if ($request->filled('package_id')) {
            $contributions->where('package_id', $request->query('package_id'));
        }

        if ($request->filled('date_from')) {
            $contributions->where('contribution_date', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $contributions->where('contribution_date', '<=', $request->query('date_to'));
        }

        $contributions = $contributions
            ->orderBy('contribution_date', 'desc')
            ->paginate(15);

        $pageTitle = $this->getPageTitle($user->role, $isMine, $scope);

        $packages = $user->role === 'responsavel_pacote'
            ? $managedPackages
            : CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        return view('contributions.index', [
            'contributions' => $contributions,
            'pageTitle' => $pageTitle,
            'showUserColumn' => (!$isMine || $isMyCellScope) && $user->role !== 'membro',
            'packages' => $packages,
            'filters' => $request->all(),
            'cellScopeUnavailable' => $cellScopeUnavailable,
        ]);
    }

    private function getPageTitle($role, $isMine, $scope = null)
    {
        if ($isMine) {
            return 'Minhas Contribuições';
        }
        if ($scope === 'my_cell') {
            return 'Minha Célula (Financeiro)';
        }

        return match ($role) {
            'super_admin', 'admin', 'comissao_obra' => 'Todas as Contribuições',
            'pastor_zona' => 'Contribuições da Zona',
            'supervisor' => 'Contribuições da Supervisão',
            'lider_celula' => 'Contribuições da Célula',
            'responsavel_pacote' => 'Contribuições dos Meus Pacotes',
            default => 'Histórico de Contribuições',
        };
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $members = collect();

        // 1. Lógica para filtrar membros que podem receber a contribuição
        if ($user->role === 'membro') {
            $members = collect([$user]);
        } elseif ($user->role === 'lider_celula') {
            $members = $user->cell ? $user->cell->members()->where('is_active', true)->get() : collect();
        } elseif ($user->role === 'supervisor') {
            if ($user->cell && $user->cell->supervision_id) {
                $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
                $members = User::whereIn('cell_id', $cellIds)->where('is_active', true)->get();
            } else {
                $members = collect();
            }
        } elseif ($user->role === 'pastor_zona') {
            if ($user->cell && $user->cell->supervision && $user->cell->supervision->zone) {
                $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
                $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                $members = User::whereIn('cell_id', $cellIds)->where('is_active', true)->get();
            } else {
                $members = collect();
            }
        } elseif ($user->role === 'responsavel_pacote') {
            $packageIds = $user->managedPackages->pluck('id');
            $members = User::whereHas('commitments', function ($query) use ($packageIds) {
                $query->whereIn('package_id', $packageIds)
                    ->where('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>', now());
                    });
            })
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } elseif ($user->isAdmin() || $user->role === 'comissao_obra') {
            $members = User::where('is_active', true)
                ->whereIn('role', ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'pastor_senior', 'responsavel_pacote'])
                ->orderBy('name')
                ->get();
        } else {
            $members = collect();
        }

        // 2. Lógica para Pacotes de Compromisso (usada na view para info/seleção)
        $targetUserId = $request->query('user_id', $user->id);

        $activeCommitment = UserCommitment::with('package')
            ->where('user_id', $targetUserId)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->latest('start_date')->first();

        if ($activeCommitment) {
            $currentPackage = $activeCommitment->package;
            $currentPackage->committed_amount = $activeCommitment->committed_amount ?? $currentPackage->min_amount;
        } else {
            $currentPackage = (object) [
                'id' => null,
                'name' => 'Nenhum',
                'min_amount' => 0,
                'max_amount' => 0,
                'committed_amount' => 0,
            ];
        }

        if ($user->role === 'responsavel_pacote') {
            $packages = $user->managedPackages()->where('is_active', true)->orderBy('order')->get();

            // Se o usuário tiver um pacote atual que não está na lista gerenciada, adicioná-lo para que ele possa selecioná-lo
            if (!empty($currentPackage->id) && !$packages->contains('id', $currentPackage->id)) {
                $packages->push($currentPackage);
            }
        } else {
            $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();
        }

        // 3. Variável de Controle para alternar membro na view
        $canRegisterForOthers = $user->isAdmin() || in_array($user->role, ['lider_celula', 'supervisor', 'pastor_zona', 'comissao_obra', 'responsavel_pacote'], true);

        // IDs dos pacotes gerenciados para filtro via JS
        $managedPackageIds = ($user->role === 'responsavel_pacote') ? $user->managedPackages->pluck('id')->toArray() : [];

        return view('contributions.create', [
            'members' => $members,
            'currentUser' => $user,
            'currentPackage' => $currentPackage,
            'packages' => $packages,
            'managedPackageIds' => $managedPackageIds,
            'canRegisterForOthers' => $canRegisterForOthers,
            'preselectedUserId' => $request->query('user_id'),
            'preselectedPackageId' => $request->query('package_id'),
        ]);
    }

    public function show(Contribution $contribution): View
    {
        $contribution->load(['user.cell.supervision.zone', 'registeredBy', 'verifiedBy']);
        $user = auth()->user();

        // Lógica de Permissão (Mantida)
        if ($user->role === 'membro' && $contribution->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver esta contribuição');
        }
        if ($user->role === 'lider_celula' && $contribution->cell_id !== $user->cell_id) {
            if (!$user->getManagedCellIds()->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver esta contribuição');
            }
        }

        if ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            if (!$cellIds->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver esta contribuição');
            }
        }

        if ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            if (!$cellIds->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver esta contribuição');
            }
        }

        $canManage = $user->isAdmin() || in_array($user->role, ['pastor_zona', 'comissao_obra'], true);
        $canDelete = ($user->isAdmin() || $user->role === 'comissao_obra')
            && in_array($contribution->status, ['pendente', 'cancelada', 'rejeitada'], true);

        return view('contributions.show', [
            'contribution' => $contribution,
            'canManage' => $canManage,
            'canDelete' => $canDelete,
        ]);
    }

    public function edit(Contribution $contribution): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->id() !== $contribution->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Você não tem permissão para editar esta contribuição');
        }
        if ($contribution->status !== 'pendente') {
            return back()->with('error', 'Só pode editar contribuições pendentes!');
        }
        return view('contributions.edit', ['contribution' => $contribution]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date|before_or_equal:today',
            'package_id' => 'nullable|exists:commitment_packages,id',
            'proof_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_message' => 'nullable|string|max:2000',
        ]);

        $targetUser = User::find($validated['user_id']);
        $this->validateContributionPermission($user, $targetUser);

        $cell = $targetUser->cell;
        if (!$cell || !$cell->supervision) {
            // Garante que a hierarquia básica (célula e supervisão) existe
            return back()->with('error', 'Utilizador não está atribuído a uma hierarquia completa (célula/supervisão)!');
        }

        $proofPath = null;
        if ($request->hasFile('proof_path')) {
            $proofPath = $request->file('proof_path')->store('contributions', 'public');
        }

        $contribution = Contribution::create([
            'user_id' => $validated['user_id'],
            'cell_id' => $cell->id,
            'supervision_id' => $cell->supervision_id,
            'zone_id' => $cell->supervision->zone_id, // Assume que supervisão tem zone_id
            'amount' => $validated['amount'],
            'contribution_date' => $validated['contribution_date'],
            'package_id' => $validated['package_id'] ?? null,
            'proof_path' => $proofPath,
            'proof_message' => $validated['proof_message'] ?? null,
            'status' => 'pendente',
        ]);

        // ----------------------------------------------------
        // DISPARO DE NOTIFICAÇÕES: Contribuição Criada
        // ----------------------------------------------------

        // 1. Notificar Líder da Célula (para verificação imediata)
        if ($cell->leader_id) {
            $leader = User::find($cell->leader_id);
            // Evitar notificar o líder se ele mesmo fez a contribuição para outro membro
            if ($leader && $leader->id !== $user->id && $leader->wantsNotification('contribution_created')) {
                $leader->notify(new ContributionCreatedNotification($contribution));
            }
        }

        // 2. Notificar Admins e Comissão da Obra (batch por pacote)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $this->notifyPendingBatch($admin, $contribution);
        }

        $commissionMembers = User::where('role', 'comissao_obra')->get();
        foreach ($commissionMembers as $member) {
            $this->notifyPendingBatch($member, $contribution);
        }

        // 3. Notificar o usuário final (se ele mesmo não registrou)
        if ($targetUser->id !== $user->id && $targetUser->wantsNotification('contribution_created')) {
            $targetUser->notify(new ContributionCreatedNotification($contribution));
        }

        // 3b. Notificar quem registou (quando diferente do membro)
        if ($user->id !== $targetUser->id && $user->wantsNotification('contribution_created')) {
            $user->notify(new ContributionCreatedNotification($contribution));
        }

        // 4. Notificar pastor da zona (pendente para validar)
        if ($contribution->zone_id) {
            $zonePastorIds = \App\Models\Zone::where('id', $contribution->zone_id)->pluck('pastor_id');
            $zonePastors = User::whereIn('id', $zonePastorIds)->get();
            foreach ($zonePastors as $pastor) {
                if ($pastor->wantsNotification('contribution_pending_validation')) {
                    $pastor->notify(new ContributionPendingValidationNotification($contribution));
                }
            }
        }

        // 5. Notificar supervisor da supervisão
        if ($cell->supervision && $cell->supervision->supervisor_id) {
            $supervisor = User::find($cell->supervision->supervisor_id);
            if ($supervisor && $supervisor->id !== $user->id && $supervisor->wantsNotification('contribution_created')) {
                $supervisor->notify(new ContributionCreatedNotification($contribution));
            }
        }
        // ----------------------------------------------------

        $memberName = $targetUser->name === auth()->user()->name ? 'Sua' : 'A contribuição de ' . $targetUser->name;
        return redirect()->route('contributions.index')
            ->with('success', "$memberName foi registada com sucesso! Aguarda verificação.");
    }

    public function update(Request $request, Contribution $contribution)
    {
        if (auth()->id() !== $contribution->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Você não tem permissão para atualizar esta contribuição');
        }
        if ($contribution->status !== 'pendente') {
            return back()->with('error', 'Só pode editar contribuições pendentes!');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date|before_or_equal:today',
            'proof_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_message' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('proof_path')) {
            if ($contribution->proof_path) {
                \Storage::disk('public')->delete($contribution->proof_path);
            }
            $validated['proof_path'] = $request->file('proof_path')->store('contributions', 'public');
        }

        $contribution->update($validated);

        return redirect()->route('contributions.index')
            ->with('success', 'Contribuição atualizada com sucesso!');
    }

    public function verify(Contribution $contribution)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $user->role !== 'pastor_zona' && $user->role !== 'comissao_obra') {
            abort(403, 'Apenas admin, comissão de obra e pastor_zona pode verificar contribuições');
        }

        $contribution->update([
            'status' => 'verificada',
            'verified_by_id' => auth()->id(),
            'notes' => 'Verificado',
        ]);

        // ----------------------------------------------------
        // DISPARO DE NOTIFICAÇÃO: Contribuição Verificada (Para o Doador)
        if ($contribution->user->wantsNotification('contribution_verified')) {
            $contribution->user->notify(new ContributionVerifiedNotification($contribution));
        }
        // ----------------------------------------------------
        if ($contribution->package && $contribution->package->responsible_id) {
            $responsavel = User::find($contribution->package->responsible_id);
            if ($responsavel && $responsavel->id !== auth()->id() && $responsavel->wantsNotification('contribution_verified_manager')) {
                $responsavel->notify(new ContributionVerifiedForManagerNotification($contribution));
            }
        }

        return back()->with('success', 'Contribuição verificada com sucesso!');
    }

    public function reject(Request $request, Contribution $contribution)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $user->role !== 'pastor_zona' && $user->role !== 'comissao_obra') {
            abort(403, 'Apenas admin, comissão de obra ou pastor_zona pode rejeitar contribuições');
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $reason = $validated['notes'];

        $contribution->update([
            'status' => 'rejeitada',
            'verified_by_id' => auth()->id(),
            'notes' => $reason,
        ]);

        // ----------------------------------------------------
        // DISPARO DE NOTIFICAÇÃO: Contribuição Rejeitada (Para o Doador)
        if ($contribution->user->wantsNotification('contribution_rejected')) {
            $contribution->user->notify(new ContributionRejectedNotification($contribution, $reason));
        }
        // ----------------------------------------------------
        if ($contribution->package && $contribution->package->responsible_id) {
            $responsavel = User::find($contribution->package->responsible_id);
            if ($responsavel && $responsavel->id !== auth()->id() && $responsavel->wantsNotification('contribution_rejected_manager')) {
                $responsavel->notify(new ContributionRejectedForManagerNotification($contribution, $reason));
            }
        }

        return back()->with('success', 'Contribuição rejeitada!');
    }

    public function cancel(Request $request, Contribution $contribution)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403, 'Apenas o administrador pode cancelar contribuições.');
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $reason = "CANCELADA: " . $validated['notes'];

        $contribution->update([
            'status' => 'cancelada',
            'verified_by_id' => auth()->id(),
            'notes' => $reason,
        ]);

        return back()->with('success', 'Contribuição cancelada com sucesso!');
    }

    public function destroy(Request $request, Contribution $contribution)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $user->role !== 'comissao_obra') {
            abort(403, 'Apenas admin ou comissão de obra pode eliminar contribuições.');
        }

        $deletableStatuses = ['pendente', 'cancelada', 'rejeitada'];
        if (!in_array($contribution->status, $deletableStatuses, true)) {
            return back()->with('error', 'Apenas contribuições pendentes, canceladas ou rejeitadas podem ser eliminadas.');
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $details = [
            "Contribuição #{$contribution->id}",
            "Membro: {$contribution->user->name}",
            "Valor: " . number_format($contribution->amount, 2, ',', '.') . " MT",
            "Data: " . $contribution->contribution_date->format('d/m/Y'),
            "Status: {$contribution->status}",
        ];

        if ($contribution->cell?->name) {
            $details[] = "Célula: {$contribution->cell->name}";
        }

        if ($contribution->package?->name) {
            $details[] = "Pacote: {$contribution->package->name}";
        }

        $description = 'Eliminou contribuição | ' . implode(' | ', $details) . ' | Motivo: ' . $validated['notes'];
        $user->logActivity('delete', $description, $contribution);

        if ($contribution->proof_path) {
            \Storage::disk('public')->delete($contribution->proof_path);
        }

        $contribution->delete();
        //volta para o index
        return redirect()->route('contributions.index')->with('success', 'Contribuição eliminada com sucesso!');
    }
    public function downloadReceipt(Contribution $contribution)
    {
        $user = auth()->user();

        // Lógica de Permissão (Mesma do show)
        if ($user->role === 'membro' && $contribution->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver este comprovativo');
        }
        if ($user->role === 'lider_celula' && $contribution->cell_id !== $user->cell_id) {
            if (!$user->getManagedCellIds()->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver este comprovativo');
            }
        }

        if ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            if (!$cellIds->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver este comprovativo');
            }
        }

        if ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            if (!$cellIds->contains($contribution->cell_id)) {
                abort(403, 'Você não tem permissão para ver este comprovativo');
            }
        }

        if (!$contribution->proof_path || !\Storage::disk('public')->exists($contribution->proof_path)) {
            abort(404, 'Comprovativo não encontrado.');
        }

        return \Storage::disk('public')->response($contribution->proof_path);
    }
    public function adminShow(Contribution $contribution): View
    {
        $contribution->load(['user.cell.supervision.zone', 'registeredBy', 'verifiedBy']);

        // Apenas admin, comissão de obra e pastor_zona podem ver esta view de administração
        $user = auth()->user();
        if (!$user->isAdmin() && !in_array($user->role, ['comissao_obra', 'pastor_zona'], true)) {
            abort(403, 'Acesso negado.');
        }

        $canDelete = ($user->isAdmin() || $user->role === 'comissao_obra')
            && in_array($contribution->status, ['pendente', 'cancelada', 'rejeitada'], true);

        return view('contributions.show', [
            'contribution' => $contribution,
            'canManage' => true,
            'canDelete' => $canDelete,
        ]);
    }

    public function pendingAdmin(): View
    {
        $contributions = Contribution::where('status', 'pendente')
            ->with('user', 'cell')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.contributions.pending', ['contributions' => $contributions]);
    }

    public function notifyCommission(Request $request, CommitmentPackage $package)
    {
        $user = auth()->user();
        if ($user->role !== 'responsavel_pacote' && !$user->isAdmin()) {
            abort(403);
        }

        $pendingCount = $package->contributions()->where('status', 'pendente')->count();

        if ($pendingCount === 0) {
            return back()->with('info', 'Não existem contribuições pendentes para notificar.');
        }

        $commissionMembers = User::where('role', 'comissao_obra')->get();
        $smsService = app(\App\Services\Sms\SmsService::class);
        $message = "PROJETO EDIFICAR: Olá, o responsável do pacote {$package->name} acabou de registar contribuições. Por favor, aceda ao sistema para validar {$pendingCount} registos pendentes.";

        foreach ($commissionMembers as $member) {
            if ($member->wantsNotification('pending_contributions')) {
                $member->notify(new \App\Notifications\PendingContributionsNotification($pendingCount, $package->name, $package->id));
            }

            // SMS
            if ($member->phone) {
                $smsService->send($member->phone, $message);
            }
        }

        return back()->with('success', 'A Comissão da Obra foi notificada com sucesso!');
    }

    private function notifyPendingBatch(User $recipient, Contribution $contribution): void
    {
        if (!$recipient->wantsNotification('pending_contributions')) {
            return;
        }

        $packageId = $contribution->package_id;
        $packageName = $contribution->package?->name;

        $pendingQuery = Contribution::where('status', 'pendente');
        if ($packageId) {
            $pendingQuery->where('package_id', $packageId);
        } else {
            $pendingQuery->whereNull('package_id');
        }

        $pendingCount = $pendingQuery->count();
        $notification = new \App\Notifications\PendingContributionsNotification($pendingCount, $packageName, $packageId);

        $existing = $recipient->unreadNotifications()
            ->where('type', \App\Notifications\PendingContributionsNotification::class)
            ->when($packageId, function ($q) use ($packageId) {
                $q->where('data->package_id', $packageId);
            }, function ($q) {
                $q->whereNull('data->package_id');
            })
            ->first();

        if ($existing) {
            $existing->update(['data' => $notification->toDatabase($recipient)]);
            return;
        }

        $recipient->notify($notification);
    }

    private function validateContributionPermission($user, $targetUser)
    {
        // Membro só pode registar para si mesmo
        if ($user->role === 'membro') {
            if ($user->id !== $targetUser->id) {
                abort(403, 'Você só pode registar contribuições suas');
            }
            return;
        }

        // Líder pode registar para membros da sua célula
        if ($user->role === 'lider_celula') {
            if (!$user->getManagedCellIds()->contains($targetUser->cell_id)) {
                abort(403, 'Você só pode registar para membros das células que lidera');
            }
            return;
        }

        // Supervisor pode registar para membros das suas células
        if ($user->role === 'supervisor') {
            if (!$user->cell || !$user->cell->supervision_id) {
                abort(403, 'Sua conta não está atribuída a uma célula/supervisão válida.');
            }
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            if (!$cellIds->contains($targetUser->cell_id)) {
                abort(403, 'Você só pode registar para membros da sua supervisão');
            }
            return;
        }

        // Pastor de zona pode registar para qualquer membro da zona
        if ($user->role === 'pastor_zona') {
            if (!$user->cell || !$user->cell->supervision || !$user->cell->supervision->zone) {
                abort(403, 'Sua conta não está atribuída a uma zona/supervisão válida.');
            }
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            if (!$cellIds->contains($targetUser->cell_id)) {
                abort(403, 'Você só pode registar para membros da sua zona');
            }
            return;
        }

        // Admin, Comissão de Obra e Responsável de Pacote pode registar para qualquer membro
        if ($user->isAdmin() || $user->role === 'comissao_obra' || $user->role === 'responsavel_pacote') {
            return;
        }

        abort(403, 'Você não tem permissão para registar contribuições');
    }
}

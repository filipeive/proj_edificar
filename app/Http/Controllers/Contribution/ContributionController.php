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
use App\Notifications\ContributionVerifiedNotification;
use App\Notifications\ContributionRejectedNotification;

class ContributionController
{
    use AuthorizesRequests;  // ADICIONAR ISTO!

    // app/Http/Controllers/Contribution/ContributionController.php

    public function index(Request $request): View
    {
        $user = auth()->user();
        $isMine = $request->query('mine');

        $contributions = Contribution::query()
            ->with('user', 'cell');

        // Lógica para "Minhas Contribuições" vs. Visualização Hierárquica
        if ($isMine) {
            $contributions->where('user_id', $user->id);
        } else {
            switch ($user->role) {
                case 'membro':
                    $contributions->where('user_id', $user->id);
                    break;
                case 'lider_celula':
                    $contributions->where('cell_id', $user->cell_id);
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
                case 'admin':
                case 'comissao_obra':
                    break;
                case 'responsavel_pacote':
                    $packageIds = $user->managedPackages->pluck('id');
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

        $pageTitle = $this->getPageTitle($user->role, $isMine);

        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        return view('contributions.index', [
            'contributions' => $contributions,
            'pageTitle' => $pageTitle,
            'showUserColumn' => !$isMine && $user->role !== 'membro',
            'packages' => $packages,
            'filters' => $request->all(),
        ]);
    }

    private function getPageTitle($role, $isMine)
    {
        if ($isMine) {
            return 'Minhas Contribuições';
        }

        return match ($role) {
            'admin', 'comissao_obra' => 'Todas as Contribuições',
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
        } elseif ($user->role === 'admin' || $user->role === 'comissao_obra') {
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
            $currentPackage = (object) ['name' => 'Nenhum', 'min_amount' => 0, 'max_amount' => 0, 'committed_amount' => 0];
        }

        if ($user->role === 'responsavel_pacote') {
            $packages = $user->managedPackages()->where('is_active', true)->orderBy('order')->get();

            // Se o usuário tiver um pacote atual que não está na lista gerenciada, adicioná-lo para que ele possa selecioná-lo
            if (isset($currentPackage) && $currentPackage->id && !$packages->contains('id', $currentPackage->id)) {
                $packages->push($currentPackage);
            }
        } else {
            $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();
        }

        // 3. Variável de Controle para alternar membro na view
        $canRegisterForOthers = in_array($user->role, ['lider_celula', 'supervisor', 'pastor_zona', 'admin', 'comissao_obra', 'responsavel_pacote']);

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
            abort(403, 'Você não tem permissão para ver esta contribuição');
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

        $canManage = $user->role === 'admin' || $user->role === 'pastor_zona';

        return view('contributions.show', [
            'contribution' => $contribution,
            'canManage' => $canManage,
        ]);
    }

    public function edit(Contribution $contribution): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->id() !== $contribution->user_id && auth()->user()->role !== 'admin') {
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
            'status' => 'pendente',
        ]);

        // ----------------------------------------------------
        // DISPARO DE NOTIFICAÇÕES: Contribuição Criada
        // ----------------------------------------------------

        // 1. Notificar Líder da Célula (para verificação imediata)
        if ($cell->leader_id) {
            $leader = User::find($cell->leader_id);
            // Evitar notificar o líder se ele mesmo fez a contribuição para outro membro
            if ($leader && $leader->id !== $user->id) {
                $leader->notify(new ContributionCreatedNotification($contribution));
            }
        }

        // 2. Notificar o Admin (sempre, para verificação final)
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new ContributionCreatedNotification($contribution));
        }

        // 3. Notificar o usuário final (se ele mesmo não registrou)
        if ($targetUser->id !== $user->id) {
            $targetUser->notify(new ContributionCreatedNotification($contribution));
        }

        // 4. Notificar a Comissão da Obra (se registrado por um Responsável de Pacote)
        if ($user->role === 'responsavel_pacote') {
            $commissionMembers = User::where('role', 'comissao_obra')->get();
            foreach ($commissionMembers as $member) {
                $member->notify(new ContributionCreatedNotification($contribution));
            }
        }
        // ----------------------------------------------------

        $memberName = $targetUser->name === auth()->user()->name ? 'Sua' : 'A contribuição de ' . $targetUser->name;
        return redirect()->route('contributions.index')
            ->with('success', "$memberName foi registada com sucesso! Aguarda verificação.");
    }

    public function update(Request $request, Contribution $contribution)
    {
        if (auth()->id() !== $contribution->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Você não tem permissão para atualizar esta contribuição');
        }
        if ($contribution->status !== 'pendente') {
            return back()->with('error', 'Só pode editar contribuições pendentes!');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date|before_or_equal:today',
            'proof_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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

        if ($user->role !== 'admin' && $user->role !== 'pastor_zona' && $user->role !== 'comissao_obra') {
            abort(403, 'Apenas admin, comissão de obra e pastor_zona pode verificar contribuições');
        }

        $contribution->update([
            'status' => 'verificada',
            'verified_by_id' => auth()->id(),
            'notes' => 'Verificado',
        ]);

        // ----------------------------------------------------
        // DISPARO DE NOTIFICAÇÃO: Contribuição Verificada (Para o Doador)
        $contribution->user->notify(new ContributionVerifiedNotification($contribution));
        // ----------------------------------------------------

        return back()->with('success', 'Contribuição verificada com sucesso!');
    }

    public function reject(Request $request, Contribution $contribution)
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $user->role !== 'pastor_zona' && $user->role !== 'comissao_obra') {
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
        $contribution->user->notify(new ContributionRejectedNotification($contribution, $reason));
        // ----------------------------------------------------

        return back()->with('success', 'Contribuição rejeitada!');
    }
    public function downloadReceipt(Contribution $contribution)
    {
        $user = auth()->user();

        // Lógica de Permissão (Mesma do show)
        if ($user->role === 'membro' && $contribution->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver este comprovativo');
        }
        if ($user->role === 'lider_celula' && $contribution->cell_id !== $user->cell_id) {
            abort(403, 'Você não tem permissão para ver este comprovativo');
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

        // Apenas admins podem ver esta view de administração
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        return view('admin.contributions.details', [
            'contribution' => $contribution,
            'canManage' => true,
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
        if ($user->role !== 'responsavel_pacote' && $user->role !== 'admin') {
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
            // Notificação Interna
            $member->notify(new \App\Notifications\PendingContributionsNotification($pendingCount, $package->name));

            // SMS
            if ($member->phone) {
                $smsService->send($member->phone, $message);
            }
        }

        return back()->with('success', 'A Comissão da Obra foi notificada com sucesso!');
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
            if ($user->cell_id === null || $targetUser->cell_id !== $user->cell_id) {
                abort(403, 'Você só pode registar para membros da sua célula');
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
        if ($user->role === 'admin' || $user->role === 'comissao_obra' || $user->role === 'responsavel_pacote') {
            return;
        }

        abort(403, 'Você não tem permissão para registar contribuições');
    }
}

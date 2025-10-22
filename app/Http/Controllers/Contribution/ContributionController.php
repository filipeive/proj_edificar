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
                    break;
                default:
                    $contributions->where('user_id', $user->id);
                    break;
            }
        }

        $contributions = $contributions
            ->orderBy('contribution_date', 'desc')
            ->paginate(10);

        $pageTitle = $this->getPageTitle($user->role, $isMine);

        return view('contributions.index', [
            'contributions' => $contributions,
            'pageTitle' => $pageTitle,
            'showUserColumn' => !$isMine && $user->role !== 'membro',
        ]);
    }

    private function getPageTitle($role, $isMine)
    {
        if ($isMine) {
            return 'Minhas Contribuições';
        }

        return match ($role) {
            'admin' => 'Todas as Contribuições',
            'pastor_zona' => 'Contribuições da Zona',
            'supervisor' => 'Contribuições da Supervisão',
            'lider_celula' => 'Contribuições da Célula',
            default => 'Histórico de Contribuições',
        };
    }

    public function create(): View
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
        } elseif ($user->role === 'admin') {
            $members = User::where('is_active', true)->where('role', 'membro')->get();
        } else {
            $members = collect();
        }


        // 2. Lógica para Pacotes de Compromisso (usada na view para info/seleção)
        $activeCommitment = UserCommitment::with('package')
            ->where('user_id', $user->id)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->latest('start_date')->first();

        if ($activeCommitment) {
            $currentPackage = $activeCommitment->package;
            $currentPackage->committed_amount = $activeCommitment->committed_amount ?? $currentPackage->min_amount;
        } else {
            $currentPackage = (object)['name' => 'Nenhum', 'min_amount' => 0, 'max_amount' => 0, 'committed_amount' => 0];
        }

        $packages = CommitmentPackage::where('is_active', true)->orderBy('order')->get();

        // 3. Variável de Controle para alternar membro na view
        $canRegisterForOthers = in_array($user->role, ['lider_celula', 'supervisor', 'pastor_zona', 'admin']);

        return view('contributions.create', [
            'members' => $members,
            'currentUser' => $user,
            'currentPackage' => $currentPackage,
            'packages' => $packages,
            'canRegisterForOthers' => $canRegisterForOthers,
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

    public function edit(Contribution $contribution): View
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
            'proof_path' => $proofPath,
            'status' => 'pendente',
            'registered_by_id' => auth()->id(),
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

        if ($user->role !== 'admin' && $user->role !== 'pastor_zona') {
            abort(403, 'Apenas admin e pastor_zona pode verificar contribuições');
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

        if ($user->role !== 'admin' && $user->role !== 'pastor_zona') {
            abort(403, 'Apenas admin ou pastor_zona pode rejeitar contribuições');
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

        // Admin pode registar para qualquer membro
        if ($user->role === 'admin') {
            return;
        }

        abort(403, 'Você não tem permissão para registar contribuições');
    }
}

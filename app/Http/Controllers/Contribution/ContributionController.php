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

class ContributionController {
    use AuthorizesRequests;  // ADICIONAR ISTO!

    // app/Http/Controllers/Contribution/ContributionController.php

public function index(Request $request): View {
    $user = auth()->user();
    $isMine = $request->query('mine');
    
    $contributions = Contribution::query()
        ->with('user', 'cell');

    // Lógica para "Minhas Contribuições"
    if ($isMine) {
        $contributions->where('user_id', $user->id);
    } 
    // Lógica para visualização hierárquica (se não for "Minhas Contribuições")
    else {
        switch ($user->role) {
            case 'membro':
                // Membros só veem as suas por padrão, mas se clicarem na área hierárquica (o que não deve acontecer no menu)
                // vamos manter a lógica de segurança. No menu só oferecemos a rota 'mine'.
                $contributions->where('user_id', $user->id);
                break;

            case 'lider_celula':
                // Líder vê as da sua célula
                $contributions->where('cell_id', $user->cell_id);
                break;

            case 'supervisor':
                // Supervisor vê as das suas supervisões
                $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
                $contributions->whereIn('cell_id', $cellIds);
                break;

            case 'pastor_zona':
                // Pastor de Zona vê as da sua zona
                // ASSUMIMOS que o usuário tem célula para definir a zona/supervisão
                if ($user->cell && $user->cell->supervision && $user->cell->supervision->zone) {
                    $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
                    $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                    $contributions->whereIn('cell_id', $cellIds);
                } else {
                    // Prevenção se a hierarquia não estiver definida (deve ser tratada noutra parte, mas segurança aqui)
                    $contributions->where('id', 0); 
                }
                break;
            
            case 'admin':
                // Admin vê todas por padrão (não precisa de filtro)
                break;

            default:
                // Segurança extra
                $contributions->where('user_id', $user->id);
                break;
        }
    }
    
    $contributions = $contributions
        ->orderBy('contribution_date', 'desc')
        ->paginate(10);

    // Ajuste o título da página para refletir o que está sendo exibido
    $pageTitle = $this->getPageTitle($user->role, $isMine);

    return view('contributions.index', [
        'contributions' => $contributions,
        'pageTitle' => $pageTitle, // Passa o novo título
        'showUserColumn' => !$isMine && $user->role !== 'membro', // Determina se deve mostrar a coluna do usuário
    ]);
}

// Adicione esta função auxiliar (helper) dentro da classe ContributionController
private function getPageTitle($role, $isMine) {
    if ($isMine) {
        return 'Minhas Contribuições';
    }

    return match($role) {
        'admin' => 'Todas as Contribuições',
        'pastor_zona' => 'Contribuições da Zona',
        'supervisor' => 'Contribuições da Supervisão',
        'lider_celula' => 'Contribuições da Célula',
        default => 'Histórico de Contribuições',
    };
}
    /* public function create(): View {
        $user = auth()->user();
        $members = collect();

        // Se for membro, pode só registar para si
        if ($user->role === 'membro') {
            $members = collect([$user]);
        }
        // Se for líder, pode registar para membros da sua célula
        elseif ($user->role === 'lider_celula') {
            $members = $user->cell->members()
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->get();
        }
        // Se for supervisor, pode registar para qualquer membro das suas células
        elseif ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            $members = User::whereIn('cell_id', $cellIds)
                ->where('is_active', true)
                ->get();
        }
        // Se for pastor, pode registar para qualquer membro da sua zona
        elseif ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            $members = User::whereIn('cell_id', $cellIds)
                ->where('is_active', true)
                ->get();
        }
        // Se for admin, pode registar para qualquer membro
        elseif ($user->role === 'admin') {
            $members = User::where('is_active', true)
                ->where('role', 'membro')
                ->get();
        }

        return view('contributions.create', ['members' => $members, 'currentUser' => $user]);
    } */
   
    public function create(): View {
        $user = auth()->user();
        $members = collect();

        // 1. Lógica para filtrar membros que podem receber a contribuição
        
        // Se for membro, vê apenas a si mesmo na lista
        if ($user->role === 'membro') {
            // Membros não terão a opção de toggle, mas precisam de si mesmos na lista para o 'updateSelectedMemberInfo' no JS
            $members = collect([$user]); 
        }
        // Se for líder, pode registar para membros da sua célula
        elseif ($user->role === 'lider_celula') {
            // Incluir o próprio líder na lista de seleção (caso ele use o toggle)
            $members = $user->cell->members()
                ->where('is_active', true)
                ->get();
        }
        // Se for supervisor, pastor, ou admin, a lógica permanece inalterada
        elseif ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            $members = User::whereIn('cell_id', $cellIds)
                ->where('is_active', true)
                ->get();
        }
        elseif ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
            $members = User::whereIn('cell_id', $cellIds)
                ->where('is_active', true)
                ->get();
        }
        elseif ($user->role === 'admin') {
            $members = User::where('is_active', true)
                ->where('role', 'membro')
                ->get();
        }
        
        // 2. Lógica para Pacotes de Compromisso
        $activeCommitment = UserCommitment::with('package')
            ->where('user_id', $user->id)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>', now());
            })
            ->latest('start_date')
            ->first();
            
        // Prepara o objeto para a view
        if ($activeCommitment) {
            $currentPackage = $activeCommitment->package;
            // Se você adicionou 'committed_amount' no UserCommitment
            $currentPackage->committed_amount = $activeCommitment->committed_amount ?? $currentPackage->min_amount;
        } else {
            $currentPackage = (object)['name' => 'Nenhum', 'min_amount' => 0, 'max_amount' => 0, 'committed_amount' => 0];
        }

        // Obtem todos os pacotes ativos para a lista de seleção
        $packages = CommitmentPackage::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        // 3. Variável de Controle
        $canRegisterForOthers = in_array($user->role, ['lider_celula', 'supervisor', 'pastor_zona', 'admin']);

        // 4. Retorna a View
        return view('contributions.create', [
            'members' => $members, 
            'currentUser' => $user,
            'currentPackage' => $currentPackage,
            'packages' => $packages,
            'canRegisterForOthers' => $canRegisterForOthers, // Variável de controle
        ]);
    }
    public function store(Request $request) {
        $user = auth()->user();
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date|before_or_equal:today',
            'proof_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Validar que o utilizador tem permissão para registar para este membro
        $targetUser = User::find($validated['user_id']);
        $this->validateContributionPermission($user, $targetUser);

        $cell = $targetUser->cell;
        if (!$cell) {
            return back()->with('error', 'Utilizador não está atribuído a nenhuma célula!');
        }

        $proofPath = null;
        if ($request->hasFile('proof_path')) {
            $proofPath = $request->file('proof_path')
                ->store('contributions', 'public');
        }

        Contribution::create([
            'user_id' => $validated['user_id'],
            'cell_id' => $cell->id,
            'supervision_id' => $cell->supervision_id,
            'zone_id' => $cell->supervision->zone_id,
            'amount' => $validated['amount'],
            'contribution_date' => $validated['contribution_date'],
            'proof_path' => $proofPath,
            'status' => 'pendente',
            'registered_by_id' => auth()->id(),
        ]);

        $memberName = $targetUser->name === auth()->user()->name ? 'Sua' : 'A contribuição de ' . $targetUser->name;
        return redirect()->route('contributions.index')
            ->with('success', "$memberName foi registada com sucesso!");
    }


public function show(Contribution $contribution): View {
    // Carregar as relações necessárias (user, cell, registeredBy, verifiedBy)
    $contribution->load(['user.cell.supervision.zone', 'registeredBy', 'verifiedBy']);

    $user = auth()->user();
    
    // --- Lógica de Permissão (Mantida) ---
    
    // Membro pode ver apenas suas contribuições
    if ($user->role === 'membro' && $contribution->user_id !== $user->id) {
        abort(403, 'Você não tem permissão para ver esta contribuição');
    }
    
    // Líder pode ver apenas contribuições da sua célula
    if ($user->role === 'lider_celula' && $contribution->cell_id !== $user->cell_id) {
        abort(403, 'Você não tem permissão para ver esta contribuição');
    }
    
    // Supervisor pode ver apenas contribuições das suas células
    if ($user->role === 'supervisor') {
        // ASSUMIMOS que $user->cell->supervision_id existe
        $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
        if (!$cellIds->contains($contribution->cell_id)) {
            abort(403, 'Você não tem permissão para ver esta contribuição');
        }
    }
    
    // Pastor pode ver apenas contribuições da sua zona
    if ($user->role === 'pastor_zona') {
        // ASSUMIMOS que a hierarquia existe
        $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
        $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
        if (!$cellIds->contains($contribution->cell_id)) {
            abort(403, 'Você não tem permissão para ver esta contribuição');
        }
    }    
    // Apenas Admins podem realizar as ações finais de verificação/rejeição
    $canManage = $user->role === 'admin'; 

    return view('contributions.show', [
        'contribution' => $contribution,
        'canManage' => $canManage, // Passar esta variável para a view
    ]);}

    public function edit(Contribution $contribution): View {
        // Apenas o dono pode editar
        if (auth()->id() !== $contribution->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Você não tem permissão para editar esta contribuição');
        }

        if ($contribution->status !== 'pendente') {
            return back()->with('error', 'Só pode editar contribuições pendentes!');
        }

        return view('contributions.edit', ['contribution' => $contribution]);
    }

    public function update(Request $request, Contribution $contribution) {
        // Apenas o dono pode atualizar
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
            $validated['proof_path'] = $request->file('proof_path')
                ->store('contributions', 'public');
        }

        $contribution->update($validated);

        return redirect()->route('contributions.index')
            ->with('success', 'Contribuição atualizada com sucesso!');
    }

    public function verify(Contribution $contribution) {
        $user = auth()->user();
        
        // Apenas admin pode verificar
        if ($user->role !== 'admin') {
            abort(403, 'Apenas admin pode verificar contribuições');
        }

        $contribution->update([
            'status' => 'verificada',
            'verified_by_id' => auth()->id(),
            'notes' => 'Verificado',
        ]);

        return back()->with('success', 'Contribuição verificada com sucesso!');
    }

    public function reject(Request $request, Contribution $contribution) {
        $user = auth()->user();
        
        // Apenas admin pode rejeitar
        if ($user->role !== 'admin') {
            abort(403, 'Apenas admin pode rejeitar contribuições');
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $contribution->update([
            'status' => 'rejeitada',
            'verified_by_id' => auth()->id(),
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Contribuição rejeitada!');
    }

    public function pendingAdmin(): View {
        $contributions = Contribution::where('status', 'pendente')
            ->with('user', 'cell')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.contributions.pending', ['contributions' => $contributions]);
    }

    private function validateContributionPermission($user, $targetUser) {
        // Membro só pode registar para si mesmo
        if ($user->role === 'membro') {
            if ($user->id !== $targetUser->id) {
                abort(403, 'Você só pode registar contribuições suas');
            }
            return;
        }

        // Líder pode registar para membros da sua célula
        if ($user->role === 'lider_celula') {
            if ($targetUser->cell_id !== $user->cell_id) {
                abort(403, 'Você só pode registar para membros da sua célula');
            }
            return;
        }

        // Supervisor pode registar para membros das suas células
        if ($user->role === 'supervisor') {
            $cellIds = Cell::where('supervision_id', $user->cell->supervision_id)->pluck('id');
            if (!$cellIds->contains($targetUser->cell_id)) {
                abort(403, 'Você só pode registar para membros da sua supervisão');
            }
            return;
        }

        // Pastor de zona pode registar para qualquer membro da zona
        if ($user->role === 'pastor_zona') {
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
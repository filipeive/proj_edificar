<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\UserCommitment;
use App\Models\Zone; // Importar o modelo Zone
use App\Actions\Cells\ReassignMemberAction;
use App\Services\CellEligibilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CellController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $user = auth()->user();
        $this->authorize('viewAny', Cell::class);

        // --- PREPARAR FILTROS (Dropdowns) ---
        $zonesQuery = Zone::orderBy('name');
        $supervisionsQuery = Supervision::orderBy('name');

        if ($user->isPastorZona()) {
            $zoneIds = $user->getManagedZoneIds();
            $zonesQuery->whereIn('id', $zoneIds);
            $supervisionsQuery->whereIn('zone_id', $zoneIds);
        } elseif ($user->isSupervisor()) {
            $supervisionIds = $user->getManagedSupervisionIds();
            $supervisionsQuery->whereIn('id', $supervisionIds);
            $zonesQuery->whereHas('supervisions', function ($q) use ($supervisionIds) {
                $q->whereIn('id', $supervisionIds);
            });
        } elseif ($user->isLider() || $user->isTimoteo()) {
            $cellIds = $this->managedCellIds($user);
            $supervisionsQuery->whereHas('cells', function ($q) use ($cellIds) {
                $q->whereIn('id', $cellIds);
            });
            $zonesQuery->whereHas('supervisions.cells', function ($q) use ($cellIds) {
                $q->whereIn('id', $cellIds);
            });
        }

        $zones = $zonesQuery->get();
        $supervisions = $supervisionsQuery->get();

        // Iniciar a query
        $cellsQuery = Cell::query()->with('supervision.zone', 'leader', 'members');
        $this->applyVisibilityScope($cellsQuery, $user);

        // --- 1. FILTRO POR ZONA ---
        if ($request->filled('zone')) {
            $cellsQuery->whereHas('supervision', function ($q) use ($request) {
                $q->where('zone_id', $request->input('zone'));
            });
        }

        // --- 2. FILTRO POR SUPERVISÃO ---
        if ($request->filled('supervision')) {
            $cellsQuery->where('supervision_id', $request->input('supervision'));
        }

        // --- 3. FILTRO POR BUSCA (Search) ---
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $cellsQuery->where(function ($query) use ($searchTerm) {
                // Busca por nome da célula
                $query->where('name', 'LIKE', '%'.$searchTerm.'%');

                // Busca por nome do líder (JOIN necessário)
                $query->orWhereHas('leader', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%'.$searchTerm.'%');
                });

                // Busca por nome da zona (JOIN necessário)
                $query->orWhereHas('supervision.zone', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%'.$searchTerm.'%');
                });
            });
        }

        // --- 4. FILTRO POR TIPO DE CÉLULA ---
        if ($request->filled('type')) {
            $cellsQuery->where('type', $request->input('type'));
        }

        // --- 5. ORDENAÇÃO (Sort) ---
        $sort = $request->input('sort', 'name');

        switch ($sort) {
            case 'members':
                $cellsQuery->orderBy('member_count', 'desc');
                break;
            case 'recent':
                $cellsQuery->orderBy('created_at', 'desc');
                break;
            case 'name':
            default:
                $cellsQuery->orderBy('name', 'asc');
                break;
        }

        $cells = $cellsQuery->paginate(15)->withQueryString();

        return view('admin.cells.index', [
            'cells' => $cells,
            'zones' => $zones,
            'supervisions' => $supervisions,
        ]);
    }

    // ... (restante dos métodos create, store, show, edit, update, destroy)
    // Mantenha os demais métodos inalterados, pois a lógica de filtro foi adicionada apenas ao index.

    public function create(): View
    {
        $user = auth()->user();
        $this->authorize('create', Cell::class);

        $query = Supervision::query();

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $supervisions = $query->with('zone')->orderBy('name')->get();
        $leaders = User::where('role', 'lider_celula')->get();

        return view('admin.cells.create', [
            'supervisions' => $supervisions,
            'leaders' => $leaders,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Cell::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:membros,lideres,supervisores,pastores_zona,pastores',
            'supervision_id' => 'required|exists:supervisions,id',
            'leader_id' => 'required|exists:users,id',
        ]);

        $leader = User::findOrFail($validated['leader_id']);
        if ($response = $this->validateLeaderForCellType($request, $leader, $validated['type'], 'O líder selecionado não é compatível com o tipo de célula selecionado.')) {
            return $response;
        }

        $cell = Cell::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'supervision_id' => $validated['supervision_id'],
            'leader_id' => $validated['leader_id'],
        ]);

        // Atribuir líder à célula
        $leader->update(['cell_id' => $cell->id]);

        // Assumindo que member_count está na tabela cells e é atualizado.
        $cell->update(['member_count' => $cell->getMembersCount()]);

        return redirect()->route('cells.show', $cell->id)
            ->with('success', 'Célula criada com sucesso!');
    }

    public function show(Request $request, Cell $cell): View
    {
        $user = auth()->user();
        $this->authorize('view', $cell);

        $availableCellsQuery = Cell::orderBy('name');
        $this->applyVisibilityScope($availableCellsQuery, $user);

        $availableCells = $availableCellsQuery->where('id', '!=', $cell->id)->get();

        $membersQuery = $cell->members()->where('is_active', true);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $membersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('email', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        return view('admin.cells.show', [
            'cell' => $cell->load('supervision.zone', 'leader', 'timoteos'),
            'members' => $membersQuery->paginate(20)->withQueryString(),
            'meetings' => $cell->meetings()
                ->orderBy('meeting_date', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'availableCells' => $availableCells,
            'visitors' => $cell->visitors()->orderBy('visit_date', 'desc')->get(),
        ]);
    }

    public function edit(Cell $cell): View
    {   
        $user = auth()->user();
        $this->authorize('update', $cell);

        $query = Supervision::query();

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $supervisions = $query->with('zone')->orderBy('name')->get();

        $zoneId = $cell->supervision->zone_id ?? null;
        $supervisionId = $cell->supervision_id;

        $leadersQuery = User::query()
            ->where(function ($q) use ($zoneId) {
                $q->where('role', 'lider_celula')
                    ->whereHas('cell.supervision', function ($q2) use ($zoneId) {
                        if ($zoneId) {
                            $q2->where('zone_id', $zoneId);
                        }
                    });
            })
            ->orWhere(function ($q) use ($cell) {
                $q->where('role', 'timoteo')
                    ->where('cell_id', $cell->id);
            })
            ->orWhere(function ($q) use ($supervisionId) {
                $q->where('role', 'supervisor')
                    ->whereHas('supervisedSupervisions', function ($q2) use ($supervisionId) {
                        $q2->where('id', $supervisionId);
                    });
            })
            ->orWhere(function ($q) use ($supervisionId) {
                $q->where('role', 'sub_supervisor')
                    ->whereHas('subSupervisedSupervisions', function ($q2) use ($supervisionId) {
                        $q2->where('id', $supervisionId);
                    });
            })
            ->orWhere(function ($q) use ($zoneId) {
                $q->whereIn('role', ['pastor_zona', 'pastor'])
                    ->whereIn('id', Zone::where('id', $zoneId)->whereNotNull('pastor_id')->pluck('pastor_id'));
            })
            ->orWhere(function ($q) {
                $q->where('role', 'pastor_senior');
            });

        $leaders = $leadersQuery->get();

        $timoteos = User::where('cell_id', $cell->id)
            ->where('id', '!=', $cell->leader_id)
            ->get()
            ->merge($cell->timoteos)
            ->unique('id');

        return view('admin.cells.edit', [
            'cell' => $cell,
            'supervisions' => $supervisions,
            'leaders' => $leaders,
            'timoteos' => $timoteos,
        ]);
    }

    public function update(Request $request, Cell $cell)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:membros,lideres,supervisores,pastores_zona,pastores',
            'supervision_id' => 'required|exists:supervisions,id',
            'leader_id' => 'required|exists:users,id',
            'timoteos' => 'nullable|array',
            'timoteos.*' => 'exists:users,id',
        ]);

        $leader = User::findOrFail($validated['leader_id']);
        if ($response = $this->validateLeaderForCellType($request, $leader, $validated['type'], 'O líder selecionado não é compatível com o tipo de célula selecionado.')) {
            return $response;
        }

        $newTimoteoIds = collect($validated['timoteos'] ?? []);
        foreach ($newTimoteoIds as $userId) {
            $member = User::findOrFail($userId);
            if ($response = $this->validateMemberForCellType($request, $member, $validated['type'], "O membro {$member->name} não é compatível com o tipo de célula selecionado.")) {
                return $response;
            }
        }

        $cell->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'supervision_id' => $validated['supervision_id'],
            'leader_id' => $validated['leader_id'],
        ]);

        $oldLeaderId = $cell->leader_id;

        DB::transaction(function () use ($cell, $newTimoteoIds, $oldLeaderId) {
            // Unset cell_id for users currently assigned but not in the new timoteo list
            User::where('cell_id', $cell->id)
                ->where('role', 'timoteo')
                ->whereNotIn('id', $newTimoteoIds)
                ->update(['cell_id' => null, 'role' => 'membro']);

            // Set cell_id and role for new timoteo list
            if (! $newTimoteoIds->isEmpty()) {
                User::whereIn('id', $newTimoteoIds)
                    ->where('role', '!=', 'lider_celula')
                    ->where('role', '!=', 'supervisor')
                    ->where('role', '!=', 'sub_supervisor')
                    ->where('role', '!=', 'pastor_zona')
                    ->where('role', '!=', 'pastor')
                    ->where('role', '!=', 'pastor_senior')
                    ->update(['cell_id' => $cell->id, 'role' => 'timoteo']);
            }

            // Atualizar líder
            if ($oldLeaderId != $cell->leader_id) {
                // Remover antiga atribuição de líder
                if ($oldLeaderId) {
                    $oldLeader = User::find($oldLeaderId);
                    if ($oldLeader && ! $newTimoteoIds->contains($oldLeader->id)) {
                        $oldLeader->update(['cell_id' => null]);
                        if (in_array($oldLeader->role, ['lider_celula', 'timoteo'])) {
                            $oldLeader->update(['role' => 'membro']);
                        }
                    }
                }
                // Atribuir novo líder
                $newLeader = User::find($cell->leader_id);
                if ($newLeader) {
                    $newLeader->update(['cell_id' => $cell->id]);
                    if (in_array($newLeader->role, ['membro', 'timoteo'])) {
                        $newLeader->update(['role' => 'lider_celula']);
                    }
                }
            }
        });

        $cell->update(['member_count' => $cell->getMembersCount()]);

        // deve redirecionar para o show da celula editada
        return redirect()->route('cells.show', $cell->id)
            ->with('success', 'Célula atualizada com sucesso!');
    }

    public function getEligibleLeaders(Request $request)
    {
        $request->validate([
            'cell_id' => 'nullable|exists:cells,id',
            'supervision_id' => 'nullable|exists:supervisions,id',
            'cell_type' => 'required|in:membros,lideres,supervisores,pastores_zona,pastores',
        ]);

        $cell = $request->filled('cell_id') ? Cell::findOrFail($request->cell_id) : null;
        $cellType = $request->cell_type;

        if ($cell) {
            $zoneId = $cell->supervision->zone_id ?? null;
            $supervisionId = $cell->supervision_id;
        } elseif ($request->filled('supervision_id')) {
            $supervisionId = (int) $request->supervision_id;
            $zoneId = Supervision::find($supervisionId)->zone_id ?? null;
        } else {
            $zoneId = null;
            $supervisionId = null;
        }

        $leadersQuery = User::query()
            ->where(function ($q) use ($cell, $zoneId, $supervisionId) {
                // Líderes de Célula (para células de membros / padrão)
                $q->where('role', 'lider_celula')
                    ->when($zoneId, function ($q2) use ($zoneId) {
                        $q2->whereHas('cell.supervision', function ($q3) use ($zoneId) {
                            $q3->where('zone_id', $zoneId);
                        });
                    }, function ($q2) use ($supervisionId) {
                        $q2->when($supervisionId, function ($q3) use ($supervisionId) {
                            $q3->whereHas('cell.supervision', function ($q4) use ($supervisionId) {
                                $q4->where('supervision_id', $supervisionId);
                            });
                        });
                    });

                // Timóteos da própria célula (apenas em edição de uma célula existente)
                if ($cell) {
                    $q->orWhere(function ($q2) use ($cell) {
                        $q2->where('role', 'timoteo')->where('cell_id', $cell->id);
                    });
                }

                // Supervisores da supervisão
                $q->orWhere(function ($q2) use ($supervisionId) {
                    $q2->where('role', 'supervisor')
                        ->when($supervisionId, function ($q3) use ($supervisionId) {
                            $q3->whereHas('supervisedSupervisions', function ($q4) use ($supervisionId) {
                                $q4->where('id', $supervisionId);
                            });
                        });
                });

                // Sub-supervisores da supervisão
                $q->orWhere(function ($q2) use ($supervisionId) {
                    $q2->where('role', 'sub_supervisor')
                        ->when($supervisionId, function ($q3) use ($supervisionId) {
                            $q3->whereHas('subSupervisedSupervisions', function ($q4) use ($supervisionId) {
                                $q4->where('id', $supervisionId);
                            });
                        });
                });

                // Pastores de Zona / Pastores (da zona, ou todos sem contexto geográfico)
                $q->orWhere(function ($q2) use ($zoneId) {
                    $q2->whereIn('role', ['pastor_zona', 'pastor'])
                        ->when($zoneId, function ($q3) use ($zoneId) {
                            $q3->whereIn('id', Zone::where('id', $zoneId)->whereNotNull('pastor_id')->pluck('pastor_id'));
                        });
                });

                // Pastor Sénior
                $q->orWhere(function ($q2) {
                    $q2->where('role', 'pastor_senior');
                });
            });

        $allowedRoles = match ($cellType) {
            Cell::TYPE_LIDERES => ['supervisor', 'sub_supervisor', 'pastor_zona', 'pastor', 'pastor_senior'],
            Cell::TYPE_SUPERVISORES => ['pastor_zona', 'pastor', 'pastor_senior'],
            Cell::TYPE_PASTORES_ZONA => ['pastor_senior'],
            Cell::TYPE_PASTORES => ['pastor_senior'],
            // Na criação (sem célula existente) mantém-se apenas líderes de célula para o tipo padrão
            default => $cell ? null : ['lider_celula'],
        };

        if ($allowedRoles) {
            $leadersQuery->whereIn('role', $allowedRoles);
        }

        $leaders = $leadersQuery->orderBy('name')->get(['id', 'name', 'email', 'role']);

        return response()->json($leaders);
    }

    /**
     * Lista (JSON) de membros JÁ CADASTRADOS e elegíveis para serem adicionados a
     * esta célula (usado no modal "Adicionar membro existente").
     */
    public function getEligibleMembers(Request $request, Cell $cell, CellEligibilityService $service)
    {
        $this->authorize('update', $cell);

        $query = $service->membrosElegiveisPara($cell);

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $members = $query->orderBy('name')->limit(50)->get(['id', 'name', 'email', 'role', 'cell_id']);

        return response()->json($members->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'role' => $m->role,
                'role_label' => $m->getRoleLabel(),
                'current_cell' => $m->cell?->name,
            ];
        }));
    }

    /**
     * Adiciona um membro já existente a esta célula (movendo-o, no modelo atual em
     * que cada pessoa pertence a uma única célula). Revalida tudo server-side.
     */
    public function addMember(Request $request, Cell $cell)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'member_id' => 'required|exists:users,id',
            'role_in_cell' => 'nullable|in:membro,lider',
        ]);

        $member = User::findOrFail($validated['member_id']);
        $service = app(CellEligibilityService::class);

        $result = $service->podeSerAdicionado($member, $cell);

        if ($result !== true) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $result,
                    'errors' => ['member_id' => [$result]],
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['member_id' => $result])
                ->with('error', $result);
        }

        // Mover a pessoa para esta célula (função atualiza as contagens de membros).
        app(ReassignMemberAction::class)->execute($member, (int) $cell->id);

        // Papel dentro da célula: "líder" => definir como líder desta célula de membros.
        if (($validated['role_in_cell'] ?? 'membro') === 'lider' && $cell->type === Cell::TYPE_MEMBROS) {
            $cell->update(['leader_id' => $member->id]);
        }

        return back()->with('success', "{$member->name} foi adicionado(a) à célula {$cell->name} com sucesso!");
    }

    private function validateMemberForCellType(Request $request, User $member, string $cellType, string $message): RedirectResponse|JsonResponse|null
    {
        $role = $member->role;

        $valid = match ($cellType) {
            Cell::TYPE_MEMBROS => in_array($role, ['membro', 'timoteo', 'lider_celula']),
            Cell::TYPE_LIDERES => in_array($role, ['lider_celula', 'timoteo', 'sub_supervisor']),
            Cell::TYPE_SUPERVISORES => in_array($role, ['supervisor', 'sub_supervisor', 'subpastor_zona']),
            Cell::TYPE_PASTORES_ZONA => in_array($role, ['pastor_zona', 'subpastor_zona', 'pastor']),
            Cell::TYPE_PASTORES => in_array($role, ['pastor', 'subpastor', 'pastor_senior']),
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
                'errors' => ['timoteos' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['timoteos' => $message])
            ->with('error', $message);
    }

    private function validateLeaderForCellType(Request $request, User $leader, string $cellType, string $message): RedirectResponse|JsonResponse|null
    {
        $role = $leader->role;

        \Log::info('Validando líder para célula', [
            'cellType' => $cellType,
            'leaderRole' => $role,
            'leaderId' => $leader->id,
        ]);

        $valid = match ($cellType) {
            Cell::TYPE_MEMBROS => true,
            Cell::TYPE_LIDERES => in_array($role, ['supervisor', 'sub_supervisor', 'pastor_zona', 'pastor', 'pastor_senior']),
            Cell::TYPE_SUPERVISORES => in_array($role, ['pastor_zona', 'pastor', 'pastor_senior']),
            Cell::TYPE_PASTORES_ZONA => in_array($role, ['pastor_senior']),
            Cell::TYPE_PASTORES => in_array($role, ['pastor_senior']),
            default => true,
        };

        if ($valid) {
            return null;
        }

        \Log::warning('Validação de líder falhou', [
            'cellType' => $cellType,
            'leaderRole' => $role,
            'leaderId' => $leader->id,
        ]);

        // Pedidos AJAX/JSON recebem uma resposta JSON 422 normalizada; formulários
        // normais são redirecionados com erro flash (toast) + mensagem no campo.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['leader_id' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['leader_id' => $message])
            ->with('error', $message);
    }

    public function destroy(Cell $cell)
    {
        $this->authorize('delete', $cell);

        if ($cell->members()->exists()) {
            return back()->with('error', 'Não pode deletar célula com membros!');
        }

        if ($cell->contributions()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem contribuições financeiras vinculadas a esta célula.');
        }

        DB::transaction(function () use ($cell) {
            UserCommitment::where('cell_id', $cell->id)->update(['cell_id' => null]);
            $cell->delete();
        });

        return redirect()->route('cells.index')->with('success', 'Célula excluída com sucesso!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        $cells = Cell::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $skippedCount = 0;

        /** @var \App\Models\Cell $cell */
        foreach ($cells as $cell) {
            $this->authorize('delete', $cell);

            if ($cell->members()->exists() || $cell->contributions()->exists()) {
                $skippedCount++;

                continue;
            }

            DB::transaction(function () use ($cell) {
                UserCommitment::where('cell_id', $cell->id)->update(['cell_id' => null]);
                $cell->delete();
            });

            $deletedCount++;
        }

        $message = "{$deletedCount} células excluídas.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} foram puladas por possuírem membros ou contribuições vinculadas.";
        }

        return redirect()->route('cells.index')->with($skippedCount > 0 ? 'warning' : 'success', $message);
    }

    public function downloadPdf(Cell $cell)
    {
        $this->authorize('view', $cell);

        $cell->load(['supervision.zone', 'leader', 'members']);

        $pdf = Pdf::loadView('admin.cells.pdf', compact('cell'));

        return $pdf->download("ficha_celula_{$cell->name}.pdf");
    }

    public function reassignSupervision(Request $request, Cell $cell)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'supervision_id' => 'required|exists:supervisions,id',
        ]);

        $cell->update(['supervision_id' => $validated['supervision_id']]);

        return back()->with('success', 'Célula transferida de supervisão com sucesso!');
    }

    public function assignTimoteo(Request $request, Cell $cell)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Verificar se usuário pertence à célula
        if ($user->cell_id !== $cell->id) {
            return back()->with('error', 'O usuário não pertence a esta célula!');
        }

        // Promover a Timóteo
        $user->update(['role' => 'timoteo']);

        return back()->with('success', "{$user->name} foi promovido(a) a Timóteo com sucesso!");
    }

    public function removeTimoteo(Request $request, Cell $cell)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Verificar se usuário pertence à célula
        if ($user->cell_id !== $cell->id) {
            return back()->with('error', 'O usuário não pertence a esta célula!');
        }

        // Remover função de Timóteo, voltar a membro
        $user->update(['role' => 'membro']);

        return back()->with('success', "{$user->name} deixou de ser Timóteo com sucesso!");
    }

    public function promoteSubSupervisor(Cell $cell, User $user): RedirectResponse
    {
        $this->authorize('update', $cell);

        if ($cell->type !== Cell::TYPE_LIDERES) {
            return back()->with('error', 'Apenas membros de Célula de Líderes podem ser designados como Sub-supervisor!');
        }

        if ($user->cell_id !== $cell->id) {
            return back()->with('error', 'O utilizador não pertence a esta célula!');
        }

        $user->update(['role' => 'sub_supervisor']);

        if ($cell->supervision) {
            $cell->supervision->update(['sub_supervisor_id' => $user->id]);
        }

        return back()->with('success', "{$user->name} foi promovido(a) a Sub-supervisor da supervisão {$cell->supervision?->name} com sucesso!");
    }

    public function promoteSubpastorZona(Cell $cell, User $user): RedirectResponse
    {
        $this->authorize('update', $cell);

        if ($cell->type !== Cell::TYPE_SUPERVISORES) {
            return back()->with('error', 'Apenas membros de Célula de Supervisores podem ser designados como Sub-pastor de Zona!');
        }

        if ($user->cell_id !== $cell->id) {
            return back()->with('error', 'O utilizador não pertence a esta célula!');
        }

        $user->update(['role' => 'subpastor_zona']);

        return back()->with('success', "{$user->name} foi promovido(a) a Sub-pastor de Zona (Auxiliar) com sucesso!");
    }

    private function applyVisibilityScope(Builder $query, User $user): Builder
    {
        if ($user->isAdmin() || $user->isSecretaria() || $user->isPastor() || $user->isPastorSenior() || $user->isAdministracao()) {
            return $query;
        }

        if ($user->isPastorZona()) {
            return $query->whereHas('supervision', function ($q) use ($user) {
                $q->whereIn('zone_id', $user->getManagedZoneIds());
            });
        }

        if ($user->isSupervisor()) {
            return $query->whereIn('supervision_id', $user->getManagedSupervisionIds());
        }

        if ($user->isSubSupervisor()) {
            return $query->whereIn('supervision_id', $user->getManagedSupervisionIds());
        }

        if ($user->isLider() || $user->isTimoteo()) {
            return $query->whereIn('id', $this->managedCellIds($user));
        }

        return $query->whereRaw('1 = 0');
    }

    private function managedCellIds(User $user)
    {
        return $user->getManagedCellIds();
    }
}

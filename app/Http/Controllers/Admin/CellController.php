<?php
namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone; // Importar o modelo Zone
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CellController
{
    public function index(Request $request): View
    {
        $user = auth()->user();

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
        }

        $zones = $zonesQuery->get();
        $supervisions = $supervisionsQuery->get();

        // Iniciar a query
        $cellsQuery = Cell::query()->with('supervision.zone', 'leader', 'members');

        // --- SCOPED ACCESS FOR PASTORS AND SUPERVISORS ---
        if ($user->isPastorZona()) {
            $cellsQuery->whereHas('supervision', function ($q) use ($user) {
                $q->whereIn('zone_id', $user->getManagedZoneIds());
            });
        } elseif ($user->isSupervisor()) {
            $cellsQuery->whereIn('supervision_id', $user->getManagedSupervisionIds());
        }

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
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');

                // Busca por nome do líder (JOIN necessário)
                $query->orWhereHas('leader', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                });

                // Busca por nome da zona (JOIN necessário)
                $query->orWhereHas('supervision.zone', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                });
            });
        }

        // --- 4. ORDENAÇÃO (Sort) ---
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
        $query = Supervision::query();

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $supervisions = $query->orderBy('name')->get();
        $leaders = User::where('role', '!=', 'admin')->get();

        return view('admin.cells.create', [
            'supervisions' => $supervisions,
            'leaders' => $leaders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supervision_id' => 'required|exists:supervisions,id',
            'leader_id' => 'required|exists:users,id',
        ]);

        $cell = Cell::create($validated);

        // Atribuir líder à célula
        User::find($validated['leader_id'])->update(['cell_id' => $cell->id]);

        // Assumindo que member_count está na tabela cells e é atualizado.
        $cell->update(['member_count' => $cell->getMembersCount()]);

        return redirect()->route('cells.index')
            ->with('success', 'Célula criada com sucesso!');
    }

    public function show(Cell $cell): View
    {
        return view(
            'admin.cells.show',
            ['cell' => $cell->load('supervision', 'leader', 'members')]
        );
    }

    public function edit(Cell $cell): View
    {
        $user = auth()->user();
        $query = Supervision::query();

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $supervisions = $query->orderBy('name')->get();
        $leaders = User::where('role', '!=', 'admin')->get();

        return view('admin.cells.edit', [
            'cell' => $cell,
            'supervisions' => $supervisions,
            'leaders' => $leaders,
        ]);
    }

    public function update(Request $request, Cell $cell)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supervision_id' => 'required|exists:supervisions,id',
            'leader_id' => 'required|exists:users,id',
        ]);

        $cell->update($validated);

        // Atualizar líder
        if ($request->leader_id != $cell->leader_id) {
            // Remover antiga atribuição
            if ($cell->leader_id) {
                User::find($cell->leader_id)->update(['cell_id' => null]);
            }
            // Atribuir novo líder
            User::find($request->leader_id)->update(['cell_id' => $cell->id]);
        }

        $cell->update(['member_count' => $cell->getMembersCount()]);

        return redirect()->route('cells.index')
            ->with('success', 'Célula atualizada com sucesso!');
    }

    public function destroy(Cell $cell)
    {
        if ($cell->members()->exists()) {
            return back()->with('error', 'Não pode deletar célula com membros!');
        }
        $cell->delete();
        return redirect()->route('cells.index')->with('success', 'Célula excluída com sucesso!');
    }

    public function downloadPdf(Cell $cell)
    {
        $cell->load(['supervision.zone', 'leader', 'members']);

        $pdf = Pdf::loadView('admin.cells.pdf', compact('cell'));

        return $pdf->download("ficha_celula_{$cell->name}.pdf");
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone; // Importar o modelo Zone
use Illuminate\Http\Request;
use Illuminate\View\View;

class CellController {
    public function index(Request $request): View {
        // Obter todas as zonas para o filtro de dropdown
        $zones = Zone::orderBy('name')->get();
        
        // Iniciar a query
        $cellsQuery = Cell::query()->with('supervision.zone', 'leader', 'members');

        // --- 1. FILTRO POR ZONA ---
        if ($request->filled('zone')) {
            $zoneId = $request->input('zone');
            // Encontra todas as supervisões pertencentes à zona
            $supervisionIds = Supervision::where('zone_id', $zoneId)->pluck('id');
            // Filtra as células que pertencem a essas supervisões
            $cellsQuery->whereIn('supervision_id', $supervisionIds);
        }

        // --- 2. FILTRO POR BUSCA (Search) ---
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
        
        // --- 3. ORDENAÇÃO (Sort) ---
        $sort = $request->input('sort', 'name');
        
        switch ($sort) {
            case 'members':
                // Ordenar pela contagem de membros (requires selectRaw for efficiency)
                // Usaremos um orderBy simples na coluna member_count se ela existir e for atualizada
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
            'zones' => $zones, // Passa as zonas para o dropdown da view
        ]);
    }

    // ... (restante dos métodos create, store, show, edit, update, destroy)
    // Mantenha os demais métodos inalterados, pois a lógica de filtro foi adicionada apenas ao index.
    
    public function create(): View {
        $supervisions = Supervision::all();
        $leaders = User::where('role', '!=', 'admin')->get();
        return view('admin.cells.create', [
            'supervisions' => $supervisions,
            'leaders' => $leaders,
        ]);
    }

    public function store(Request $request) {
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

    public function show(Cell $cell): View {
        return view('admin.cells.show', 
            ['cell' => $cell->load('supervision', 'leader', 'members')]);
    }

    public function edit(Cell $cell): View {
        $supervisions = Supervision::all();
        $leaders = User::where('role', '!=', 'admin')->get();
        return view('admin.cells.edit', [
            'cell' => $cell,
            'supervisions' => $supervisions,
            'leaders' => $leaders,
        ]);
    }

    public function update(Request $request, Cell $cell) {
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

    public function destroy(Cell $cell) {
        if ($cell->members()->exists()) {
            return back()->with('error', 'Não pode deletar célula com membros!');
        }

        $cell->delete();

        return redirect()->route('cells.index')
            ->with('success', 'Célula deletada com sucesso!');
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CellController {
    public function index(): View {
        $cells = Cell::with('supervision', 'leader', 'members')->paginate(15);
        return view('admin.cells.index', ['cells' => $cells]);
    }

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

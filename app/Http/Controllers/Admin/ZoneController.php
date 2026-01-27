<?php
namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController
{
    public function index(Request $request): View
    {
        $query = Zone::with('supervisions', 'pastor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%")
                ->orWhereHas('pastor', fn($q) => $q->where('name', 'LIKE', "%$search%"));
        }

        $zones = $query->paginate(24)->withQueryString();
        return view('admin.zones.index', ['zones' => $zones]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Nenhuma zona selecionada.');
        }

        // Check for supervisions before deleting
        $zonesWithSupervisions = Zone::whereIn('id', $ids)->has('supervisions')->count();
        if ($zonesWithSupervisions > 0) {
            return back()->with('error', 'Algumas zonas selecionadas possuem supervisões e não podem ser excluídas.');
        }

        Zone::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' zonas excluídas com sucesso.');
    }

    public function create(): View
    {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::where('role', 'pastor_zona')->orderBy('name')->get();
        return view('admin.zones.create', ['pastors' => $pastors]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:zones|string|max:255',
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id',
        ]);

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona criada com sucesso!');
    }

    public function show(Zone $zone): View
    {
        $zone->load(['supervisions.cells.leader', 'pastor']);

        $cells = $zone->cells()->with(['leader', 'supervision'])->paginate(24)->withQueryString();
        $members = $zone->members()->paginate(24)->withQueryString();

        return view('admin.zones.show', [
            'zone' => $zone,
            'cells' => $cells,
            'members' => $members
        ]);
    }

    public function edit(Zone $zone): View
    {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::where('role', 'pastor_zona')->orderBy('name')->get();
        return view('admin.zones.edit', ['zone' => $zone, 'pastors' => $pastors]);
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => "required|unique:zones,name,{$zone->id}|string|max:255",
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona atualizada com sucesso!');
    }

    public function destroy(Zone $zone)
    {
        if ($zone->supervisions()->exists()) {
            return back()->with('error', 'Não pode deletar zona com supervisões!');
        }

        $zone->delete();

        return redirect()->route('zones.index')
            ->with('success', 'Zona deletada com sucesso!');
    }
}

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
        $user = auth()->user();
        $query = Zone::with('supervisions', 'pastor');

        // Pastor de Zona can only see zones they are responsible for
        if ($user->role === 'pastor_zona') {
            $query->where('pastor_id', $user->id);
        }

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
        if (Zone::whereIn('id', $ids)->has('supervisions')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem supervisões vinculadas.');
        }

        if (Zone::whereIn('id', $ids)->has('contributions')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem contribuições financeiras vinculadas.');
        }

        if (Zone::whereIn('id', $ids)->has('quarterlyReports')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem relatórios trimestrais vinculados.');
        }

        if (Zone::whereIn('id', $ids)->has('events')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem eventos vinculados.');
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
            'show_in_teaching_services' => 'nullable|boolean',
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

        $availableZones = Zone::orderBy('name')->where('id', '!=', $zone->id)->get();

        return view('admin.zones.show', [
            'zone' => $zone,
            'cells' => $cells,
            'members' => $members,
            'availableZones' => $availableZones
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
            'show_in_teaching_services' => 'nullable|boolean',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona atualizada com sucesso!');
    }

    public function destroy(Zone $zone)
    {
        if ($zone->supervisions()->exists()) {
            return back()->with('error', 'Não pode excluir: Esta zona possui supervisões vinculadas.');
        }

        if ($zone->contributions()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem contribuições financeiras registradas nesta zona.');
        }

        if ($zone->quarterlyReports()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem relatórios trimestrais vinculados a esta zona.');
        }

        if ($zone->events()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem eventos agendados nesta zona.');
        }

        $zone->delete();

        return redirect()->route('zones.index')
            ->with('success', 'Zona deletada com sucesso!');
    }
}

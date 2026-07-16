<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Zone;
use App\Models\Cell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    private function shouldScopeByManagedZones($user): bool
    {
        return ($user->isPastorZona() || $user->isSupervisor() || $user->isLider()) && !$user->isAdmin();
    }

    private function getScopedZoneAndCellIds($user): array
    {
        if ($user->isLider()) {
            $cellIds = Cell::where('leader_id', $user->id)->pluck('id');
            return [collect(), $cellIds];
        }

        $managedZoneIds = $user->getManagedZoneIds();
        $cellIds = Cell::whereHas('supervision', function ($q) use ($managedZoneIds) {
            $q->whereIn('zone_id', $managedZoneIds);
        })->pluck('id');

        return [$managedZoneIds, $cellIds];
    }

    private function applyManagedZoneScope($query, $user): void
    {
        if (!$this->shouldScopeByManagedZones($user)) {
            return;
        }

        [$managedZoneIds, $cellIds] = $this->getScopedZoneAndCellIds($user);
        $query->where(function ($q) use ($managedZoneIds, $cellIds) {
            $q->whereIn('zone_id', $managedZoneIds)
                ->orWhereIn('cell_id', $cellIds);
        });
    }

    private function ensureCanAccessVisitor($user, Visitor $visitor): void
    {
        if (!$this->shouldScopeByManagedZones($user)) {
            return;
        }

        [$managedZoneIds, $cellIds] = $this->getScopedZoneAndCellIds($user);
        $allowed = ($visitor->zone_id && $managedZoneIds->contains($visitor->zone_id))
            || ($visitor->cell_id && $cellIds->contains($visitor->cell_id));

        if (!$allowed) {
            abort(403, 'Você não tem permissão para aceder a este visitante.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Visitor::with(['zone', 'cell', 'creator', 'contactedBy'])
            ->orderBy('visit_date', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('contact_status', $request->status);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('neighborhood', 'like', "%{$search}%");
            });
        }

        // Permissões por papel
        $user = Auth::user();
        $this->applyManagedZoneScope($query, $user);

        $visitors = $query->paginate(20);

        // Estatísticas
        $statsQuery = Visitor::query();
        $this->applyManagedZoneScope($statsQuery, $user);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->pending()->count(),
            'contacted' => (clone $statsQuery)->contacted()->count(),
            'integrated' => (clone $statsQuery)->integrated()->count(),
        ];

        $zones = Zone::orderBy('name')->get();

        return view('visitors.index', compact('visitors', 'stats', 'zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $zonesQuery = Zone::orderBy('name');

        if (($user->isPastorZona() || $user->isSupervisor()) && !$user->isAdmin()) {
            $zonesQuery->whereIn('id', $user->getManagedZoneIds());
        }

        $zones = $zonesQuery->get();
        $cells = collect(); // Initially empty, will load via AJAX
        $services = \App\Models\Service::orderBy('date', 'desc')->take(20)->get();
        return view('visitors.create', compact('zones', 'cells', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|in:masculino,feminino',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|moz_phone',
            'invited_by_someone' => 'boolean',
            'inviter_name' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['contact_status'] = 'pendente';

        Visitor::create($validated);

        return redirect()->route('visitors.index')
            ->with('success', 'Visitante cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);
        $visitor->load(['zone', 'cell', 'creator', 'contactedBy']);
        $cells = $visitor->zone ? $visitor->zone->cells : collect();
        $zonesQuery = Zone::orderBy('name');

        if (($user->isPastorZona() || $user->isSupervisor()) && !$user->isAdmin()) {
            $zonesQuery->whereIn('id', $user->getManagedZoneIds());
        }

        $zones = $zonesQuery->get();

        return view('visitors.show', compact('visitor', 'cells', 'zones'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitor $visitor)
    {
        $user = Auth::user();
        if ($user->isSupervisor() && !$user->isAdmin()) {
            abort(403, 'Supervisores não podem editar visitantes.');
        }

        $zonesQuery = Zone::orderBy('name');

        if (($user->isPastorZona() || $user->isSupervisor()) && !$user->isAdmin()) {
            $managedZoneIds = $user->getManagedZoneIds();
            $zonesQuery->whereIn('id', $managedZoneIds);

            $this->ensureCanAccessVisitor($user, $visitor);
        }

        $zones = $zonesQuery->get();
        $cells = $visitor->zone ? $visitor->zone->cells : collect();
        $services = \App\Models\Service::orderBy('date', 'desc')->take(20)->get();

        return view('visitors.edit', compact('visitor', 'zones', 'cells', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);
        if ($user->isSupervisor() && !$user->isAdmin()) {
            abort(403, 'Supervisores não podem editar visitantes.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|in:masculino,feminino',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|moz_phone',
            'invited_by_someone' => 'boolean',
            'inviter_name' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'contact_status' => 'required|in:pendente,contatado,integrado,sem_interesse',
            'notes' => 'nullable|string',
        ]);

        $visitor->update($validated);

        return redirect()->route('visitors.show', $visitor)
            ->with('success', 'Visitante atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);
        if ($user->isSupervisor() && !$user->isAdmin()) {
            abort(403, 'Supervisores não podem eliminar visitantes.');
        }
        $visitor->delete();

        return redirect()->route('visitors.index')
            ->with('success', 'Visitante removido com sucesso!');
    }

    /**
     * Atribuir visitante a uma zona
     */
    public function assignToZone(Request $request, Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);

        $request->validate([
            'zone_id' => 'required|exists:zones,id',
        ]);

        if ($this->shouldScopeByManagedZones($user) && !$user->getManagedZoneIds()->contains((int) $request->zone_id)) {
            abort(403, 'Você não tem permissão para atribuir esta zona.');
        }

        $visitor->update([
            'zone_id' => $request->zone_id,
        ]);

        return back()->with('success', 'Visitante atribuído à zona com sucesso!');
    }

    /**
     * Atribuir visitante a uma célula
     */
    public function assignToCell(Request $request, Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);

        $request->validate([
            'cell_id' => 'required|exists:cells,id',
        ]);

        if ($this->shouldScopeByManagedZones($user)) {
            [, $managedCellIds] = $this->getScopedZoneAndCellIds($user);
            if (!$managedCellIds->contains((int) $request->cell_id)) {
                abort(403, 'Você não tem permissão para atribuir esta célula.');
            }
        }

        $visitor->update([
            'cell_id' => $request->cell_id,
        ]);

        return back()->with('success', 'Visitante atribuído à célula com sucesso!');
    }

    public function manualNotifySupervisor(Visitor $visitor)
    {
        $user = Auth::user();
        $this->ensureCanAccessVisitor($user, $visitor);

        $visitor->notifyAssignment();

        return back()->with('success', 'Supervisor e Pastor de Zona notificados com sucesso via SMS!');
    }

    /**
     * Marcar visitante como contatado
     */
    public function markAsContacted(Visitor $visitor)
    {
        $this->ensureCanAccessVisitor(Auth::user(), $visitor);
        $visitor->markAsContacted(Auth::user());

        return back()->with('success', 'Visitante marcado como contatado!');
    }

    public function updateFeedback(Request $request, Visitor $visitor)
    {
        $this->ensureCanAccessVisitor(Auth::user(), $visitor);

        $request->validate([
            'contact_status' => 'required|in:pendente,contatado,sem_interesse,integrado',
            'notes' => 'nullable|string',
        ]);

        $visitor->update([
            'contact_status' => $request->contact_status,
            'notes' => $request->notes,
            'contacted_at' => $request->contact_status !== 'pendente' ? now() : null,
            'contacted_by' => $request->contact_status !== 'pendente' ? Auth::id() : null,
        ]);

        return back()->with('success', 'Acompanhamento registrado com sucesso!');
    }

    /**
     * Exportar visitantes para Excel
     */
    public function export(Request $request)
    {
        $query = Visitor::with(['zone', 'cell', 'service', 'creator', 'contactedBy'])
            ->orderBy('visit_date', 'desc');

        // Aplicar mesmos filtros da listagem
        if ($request->filled('status')) {
            $query->where('contact_status', $request->status);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('neighborhood', 'like', "%{$search}%");
            });
        }

        // Permissões por papel
        $user = Auth::user();
        $this->applyManagedZoneScope($query, $user);

        $filename = 'visitantes_' . date('Y-m-d_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VisitorsExport($query),
            $filename
        );
    }

    /**
     * Bulk delete visitors
     */
    public function bulkDestroy(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        if ($request->isMethod('get')) {
            return redirect()->route('visitors.index');
        }

        $validated = $request->validate([
            'visitor_ids' => 'required|array',
            'visitor_ids.*' => 'exists:visitors,id'
        ]);

        $deletedCount = Visitor::whereIn('id', $validated['visitor_ids'])->delete();

        return redirect()->route('visitors.index')
            ->with('success', "{$deletedCount} visitante(s) removido(s) com sucesso!");
    }

    /**
     * Get cells filtered by zone for dynamic selects
     */
    public function getCellsByZone(Request $request)
    {
        $zoneId = $request->zone_id;
        $user = Auth::user();

        if (!$zoneId) {
            return response()->json([]);
        }

        if ($this->shouldScopeByManagedZones($user) && !$user->getManagedZoneIds()->contains((int) $zoneId)) {
            return response()->json([], 403);
        }

        $cells = Cell::whereHas('supervision', function ($q) use ($zoneId) {
            $q->where('zone_id', $zoneId);
        })->orderBy('name')->get(['id', 'name']);

        return response()->json($cells);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Zone;
use App\Models\Cell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
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
        if ($user->isPastorZona() && !$user->isAdmin()) {
            $query->where('zone_id', $user->zone_id);
        }

        $visitors = $query->paginate(20);

        // Estatísticas
        $stats = [
            'total' => Visitor::count(),
            'pending' => Visitor::pending()->count(),
            'contacted' => Visitor::contacted()->count(),
            'integrated' => Visitor::integrated()->count(),
        ];

        $zones = Zone::orderBy('name')->get();

        return view('visitors.index', compact('visitors', 'stats', 'zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::orderBy('name')->get();
        $services = \App\Models\Service::orderBy('date', 'desc')->take(20)->get();
        return view('visitors.create', compact('zones', 'services'));
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
            'phone' => 'nullable|string|max:20',
            'invited_by_someone' => 'boolean',
            'inviter_name' => 'nullable|string|max:255',
            'visit_date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
            'zone_id' => 'nullable|exists:zones,id',
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
        $visitor->load(['zone', 'cell', 'creator', 'contactedBy']);
        $cells = $visitor->zone ? $visitor->zone->cells : collect();

        return view('visitors.show', compact('visitor', 'cells'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitor $visitor)
    {
        $zones = Zone::orderBy('name')->get();
        $cells = $visitor->zone ? $visitor->zone->cells : collect();
        $services = \App\Models\Service::orderBy('date', 'desc')->take(20)->get();

        return view('visitors.edit', compact('visitor', 'zones', 'cells', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|in:masculino,feminino',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
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
        $visitor->delete();

        return redirect()->route('visitors.index')
            ->with('success', 'Visitante removido com sucesso!');
    }

    /**
     * Atribuir visitante a uma zona
     */
    public function assignToZone(Request $request, Visitor $visitor)
    {
        $request->validate([
            'zone_id' => 'required|exists:zones,id',
        ]);

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
        $request->validate([
            'cell_id' => 'required|exists:cells,id',
        ]);

        $visitor->update([
            'cell_id' => $request->cell_id,
        ]);

        return back()->with('success', 'Visitante atribuído à célula com sucesso!');
    }

    /**
     * Marcar visitante como contatado
     */
    public function markAsContacted(Visitor $visitor)
    {
        $visitor->markAsContacted(Auth::user());

        return back()->with('success', 'Visitante marcado como contatado!');
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
        if ($user->isPastorZona() && !$user->isAdmin()) {
            $query->where('zone_id', $user->zone_id);
        }

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
}

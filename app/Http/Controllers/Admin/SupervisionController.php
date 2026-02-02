<?php
namespace App\Http\Controllers\Admin;

use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisionController
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Supervision::with('zone', 'cells', 'supervisor');

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        } elseif ($user->isSupervisor()) {
            $query->where('supervisor_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('zone', function ($qz) use ($search) {
                        $qz->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('supervisor', function ($qs) use ($search) {
                        $qs->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $supervisions = $query->paginate(24)->withQueryString();
        return view('admin.supervisions.index', ['supervisions' => $supervisions]);
    }

    public function create(): View
    {
        $user = auth()->user();
        $zonesQuery = Zone::query();

        if ($user->isPastorZona()) {
            $zonesQuery->where('id', $user->getZoneId());
        }

        $zones = $zonesQuery->orderBy('name')->get();
        $supervisors = \App\Models\User::where('role', 'supervisor')
            ->orWhere(function ($query) {
                $query->where('role', 'admin') // Allow admins for testing/fallback
                    ->orWhere('role', 'pastor_zona');
            })->orderBy('name')->get();

        return view('admin.supervisions.create', [
            'zones' => $zones,
            'supervisors' => $supervisors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Supervision::create($validated);

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão criada com sucesso!');
    }

    public function show(Supervision $supervision): View
    {
        $user = auth()->user();
        $query = Supervision::orderBy('name');

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $availableSupervisions = $query->where('id', '!=', $supervision->id)->get();
        $availableZones = Zone::orderBy('name')->where('id', '!=', $supervision->zone_id)->get();

        return view(
            'admin.supervisions.show',
            [
                'supervision' => $supervision->load('zone'),
                'cells' => $supervision->cells()
                    ->with('leader')
                    ->paginate(20)
                    ->withQueryString(),
                'availableSupervisions' => $availableSupervisions,
                'availableZones' => $availableZones
            ]
        );
    }

    public function edit(Supervision $supervision): View
    {
        $user = auth()->user();
        $zonesQuery = Zone::query();

        if ($user->isPastorZona()) {
            $zonesQuery->where('id', $user->getZoneId());
        }

        $zones = $zonesQuery->orderBy('name')->get();
        $supervisors = \App\Models\User::where('role', 'supervisor')
            ->orWhere(function ($query) {
                $query->where('role', 'admin')
                    ->orWhere('role', 'pastor_zona');
            })->orderBy('name')->get();

        return view('admin.supervisions.edit', [
            'supervision' => $supervision,
            'zones' => $zones,
            'supervisors' => $supervisors
        ]);
    }

    public function update(Request $request, Supervision $supervision)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $supervision->update($validated);

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão atualizada com sucesso!');
    }

    public function destroy(Supervision $supervision)
    {
        if ($supervision->cells()->exists()) {
            return back()->with('error', 'Não pode deletar supervisão com células!');
        }

        $supervision->delete();

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão deletada com sucesso!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        $supervisions = Supervision::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $skippedCount = 0;

        /** @var Supervision $supervision */
        foreach ($supervisions as $supervision) {
            if ($supervision->cells()->exists()) {
                $skippedCount++;
                continue;
            }
            $supervision->delete();
            $deletedCount++;
        }

        $message = "{$deletedCount} supervisões excluídas.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} foram puladas por possuírem células.";
        }

        return back()->with('success', $message);
    }

    public function reassignZone(Request $request, Supervision $supervision)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
        ]);

        $supervision->update($validated);

        return back()->with('success', 'Supervisão transferida com sucesso para a nova zona!');
    }
}

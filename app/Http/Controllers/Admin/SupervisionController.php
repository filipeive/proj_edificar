<?php
namespace App\Http\Controllers\Admin;

use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisionController
{
    public function index(): View
    {
        $user = auth()->user();
        $query = Supervision::with('zone', 'cells');

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        } elseif ($user->isSupervisor()) {
            $query->where('supervisor_id', $user->id);
        }

        $supervisions = $query->get();
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
        return view(
            'admin.supervisions.show',
            ['supervision' => $supervision->load('zone', 'cells')]
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
}
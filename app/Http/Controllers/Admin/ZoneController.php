<?php
namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController {
    public function index(): View {
        $zones = Zone::with('supervisions')->get();
        return view('admin.zones.index', ['zones' => $zones]);
    }

    public function create(): View {
        return view('admin.zones.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:zones|string|max:255',
            'description' => 'nullable|string',
        ]);

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona criada com sucesso!');
    }

    public function show(Zone $zone): View {
        return view('admin.zones.show', ['zone' => $zone->load('supervisions')]);
    }

    public function edit(Zone $zone): View {
        return view('admin.zones.edit', ['zone' => $zone]);
    }

    public function update(Request $request, Zone $zone) {
        $validated = $request->validate([
            'name' => "required|unique:zones,name,{$zone->id}|string|max:255",
            'description' => 'nullable|string',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona atualizada com sucesso!');
    }

    public function destroy(Zone $zone) {
        if ($zone->supervisions()->exists()) {
            return back()->with('error', 'Não pode deletar zona com supervisões!');
        }

        $zone->delete();

        return redirect()->route('zones.index')
            ->with('success', 'Zona deletada com sucesso!');
    }
}
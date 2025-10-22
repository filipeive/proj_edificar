<?php
namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController {
    public function index(): View {
        $zones = Zone::with('supervisions', 'pastor')->get();
        return view('admin.zones.index', ['zones' => $zones]);
    }

    public function create(): View {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::where('role', 'pastor_zona')->orderBy('name')->get(); 
        return view('admin.zones.create', ['pastors' => $pastors]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:zones|string|max:255',
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id', // Validar o Pastor
        ]);

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona criada com sucesso!');
    }

    public function show(Zone $zone): View {
        return view('admin.zones.show', ['zone' => $zone->load('supervisions', 'pastor')]);
    }

    public function edit(Zone $zone): View {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::where('role', 'pastor_zona')->orderBy('name')->get(); 
        return view('admin.zones.edit', ['zone' => $zone, 'pastors' => $pastors]);
    }

    public function update(Request $request, Zone $zone) {
        $validated = $request->validate([
            'name' => "required|unique:zones,name,{$zone->id}|string|max:255",
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id', // Validar o Pastor
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
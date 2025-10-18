<?php
namespace App\Http\Controllers\Admin;

use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisionController {
    public function index(): View {
        $supervisions = Supervision::with('zone', 'cells')->get();
        return view('admin.supervisions.index', ['supervisions' => $supervisions]);
    }

    public function create(): View {
        $zones = Zone::all();
        return view('admin.supervisions.create', ['zones' => $zones]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'description' => 'nullable|string',
        ]);

        Supervision::create($validated);

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão criada com sucesso!');
    }

    public function show(Supervision $supervision): View {
        return view('admin.supervisions.show', 
            ['supervision' => $supervision->load('zone', 'cells')]);
    }

    public function edit(Supervision $supervision): View {
        $zones = Zone::all();
        return view('admin.supervisions.edit', [
            'supervision' => $supervision,
            'zones' => $zones,
        ]);
    }

    public function update(Request $request, Supervision $supervision) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'description' => 'nullable|string',
        ]);

        $supervision->update($validated);

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão atualizada com sucesso!');
    }

    public function destroy(Supervision $supervision) {
        if ($supervision->cells()->exists()) {
            return back()->with('error', 'Não pode deletar supervisão com células!');
        }

        $supervision->delete();

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão deletada com sucesso!');
    }
}
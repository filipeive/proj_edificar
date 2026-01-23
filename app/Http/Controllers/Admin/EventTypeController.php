<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', EventType::class);

        $eventTypes = EventType::orderBy('name')->paginate(20);

        return view('admin.event_types.index', compact('eventTypes'));
    }

    public function create()
    {
        $this->authorize('create', EventType::class);

        return view('admin.event_types.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', EventType::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        EventType::create($validated);

        return redirect()->route('event-types.index')
            ->with('success', 'Tipo de evento criado com sucesso!');
    }

    public function edit(EventType $eventType)
    {
        $this->authorize('update', $eventType);

        return view('admin.event_types.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType)
    {
        $this->authorize('update', $eventType);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name,' . $eventType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $eventType->update($validated);

        return redirect()->route('event-types.index')
            ->with('success', 'Tipo de evento atualizado com sucesso!');
    }

    public function destroy(EventType $eventType)
    {
        $this->authorize('delete', $eventType);

        // Check if there are events using this type
        if ($eventType->events()->count() > 0) {
            return back()->withErrors(['error' => 'Não é possível excluir este tipo pois existem eventos associados.']);
        }

        $eventType->delete();

        return redirect()->route('event-types.index')
            ->with('success', 'Tipo de evento excluído com sucesso!');
    }
}

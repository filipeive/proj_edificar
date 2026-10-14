<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CongregationContext;

class EventTypeController extends Controller
{
    public function index()
    {


        $eventTypes = EventType::orderBy('name')->paginate(20);

        return view('admin.event_types.index', compact('eventTypes'));
    }

    public function create()
    {


        return view('admin.event_types.create');
    }

    public function store(Request $request)
    {

        $request->merge(['code' => Str::slug((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('event_types', 'code')->where('congregation_id', app(CongregationContext::class)->id())],
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


        return view('admin.event_types.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType)
    {

        $request->merge(['code' => Str::slug((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('event_types', 'code')->where('congregation_id', app(CongregationContext::class)->id())->ignore($eventType->id)],
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


        // Check if there are events using this type
        if ($eventType->events()->count() > 0) {
            return back()->withErrors(['error' => 'Não é possível excluir este tipo pois existem eventos associados.']);
        }

        $eventType->delete();

        return redirect()->route('event-types.index')
            ->with('success', 'Tipo de evento excluído com sucesso!');
    }
}

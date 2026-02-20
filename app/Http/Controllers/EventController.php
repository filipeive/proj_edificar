<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class EventController extends Controller
{
    public function feed(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = Event::with(['eventType', 'zone', 'cell'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('date', '<', $start)
                            ->where('end_date', '>', $end);
                    });
            })
            ->get();

        $calendarEvents = $events->map(function ($event) {
            $color = '#3b82f6'; // blue-500 default
            if ($event->eventType->name === 'Culto')
                $color = '#f59e0b'; // amber-500
            if ($event->eventType->name === 'Batismo')
                $color = '#06b6d4'; // cyan-500

            $title = $event->name;
            if ($event->participants_count > 0) {
                $title .= ' (' . $event->participants_count . ')';
            }

            return [
                'id' => $event->id,
                'title' => $title,
                'start' => \Carbon\Carbon::parse($event->date)->format('Y-m-d'),
                'end' => $event->end_date ? \Carbon\Carbon::parse($event->end_date)->addDay()->format('Y-m-d') : null,
                'url' => route('events.show', $event),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'allDay' => true,
                'extendedProps' => [
                    'event_type' => $event->eventType->name,
                    'location' => $event->location,
                    'description' => $event->description,
                    'participants_count' => $event->participants_count,
                    'zone' => $event->zone ? $event->zone->name : null,
                    'cell' => $event->cell ? $event->cell->name : null,
                ]
            ];
        });

        return response()->json($calendarEvents);
    }

    public function downloadPdf(Event $event)
    {
        Gate::authorize('view', $event);
        $event->load(['eventType', 'zone', 'cell']);

        $pdf = Pdf::loadView('events.pdf', compact('event'));
        return $pdf->download('Relatorio_Culto_' . \Carbon\Carbon::parse($event->date)->format('d_m_Y') . '.pdf');
    }

    public function sendEmail(Request $request, Event $event)
    {
        Gate::authorize('view', $event);

        $request->validate([
            'email' => 'required|email'
        ]);

        $event->load(['eventType', 'zone', 'cell']);
        $pdf = Pdf::loadView('events.pdf', compact('event'));
        $pdfContent = $pdf->output();

        \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($request, $event, $pdfContent) {
            $formattedDate = \Carbon\Carbon::parse($event->date)->format('d/m/Y');
            $message->to($request->email)
                ->subject('Relatório de Culto - ' . $formattedDate)
                ->html('Olá,<br><br>Segue em anexo o relatório do culto/evento realizado em ' . $formattedDate . '.<br><br>Atenciosamente,<br>Portal Life Church')
                ->attachData($pdfContent, 'Relatorio_Culto_' . \Carbon\Carbon::parse($event->date)->format('d_m_Y') . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return back()->with('success', 'Relatório enviado com sucesso para ' . $request->email);
    }
    public function index()
    {
        Gate::authorize('viewAny', Event::class);

        $user = auth()->user();
        $query = Event::with(['eventType', 'zone', 'cell']);

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->where(function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId)
                    ->orWhereNull('zone_id');
            });
        } elseif ($user->role === 'supervisor') {
            $supervisionIds = $user->supervisedSupervisions()->pluck('id');
            $zoneId = $user->getZoneId();

            $query->where(function ($q) use ($zoneId, $supervisionIds) {
                // Events in the zone (general events)
                $q->where('zone_id', $zoneId)
                    // OR events from cells supervised by this user
                    ->orWhereHas('cell', function ($sq) use ($supervisionIds) {
                        $sq->whereIn('supervision_id', $supervisionIds);
                    })
                    // OR Global events
                    ->orWhereNull('zone_id');
            });
        } elseif ($user->role === 'lider_celula') {
            $query->where(function ($q) use ($user) {
                $q->whereHas('cell', function ($sq) use ($user) {
                    $sq->where('leader_id', $user->id);
                })
                    ->orWhereNull('zone_id');
            });
        }

        $events = $query->orderBy('date', 'desc')->paginate(15);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        Gate::authorize('create', Event::class);

        $user = auth()->user();
        $eventTypes = EventType::where('is_active', 1)->get();
        \Log::info('EventTypes count: ' . $eventTypes->count());
        $zones = collect();
        $cells = collect();

        if ($user->isAdmin() || $user->role === 'pastor' || $user->role === 'secretaria') {
            $zones = Zone::all();
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zones = Zone::where('pastor_id', $user->id)->get();
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('zone_id', $user->getZoneId());
            })->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = Zone::where('id', $zoneId)->get();
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->get();
        } elseif ($user->role === 'lider_celula') {
            $cells = Cell::where('leader_id', $user->id)->get();
        }

        return view('events.create', compact('eventTypes', 'zones', 'cells'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Event::class);

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|string|max:255',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'participants_count' => 'required|integer|min:0',
            'observations' => 'nullable|string',
        ]);

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Evento registrado com sucesso!');
    }

    public function show(Event $event)
    {
        Gate::authorize('view', $event);

        $event->load(['eventType', 'zone', 'cell']);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        Gate::authorize('update', $event);

        $user = auth()->user();
        $eventTypes = EventType::where('is_active', 1)->get();
        \Log::info('EventTypes count (edit): ' . $eventTypes->count());
        $zones = collect();
        $cells = collect();

        if ($user->isAdmin() || $user->role === 'pastor' || $user->role === 'secretaria') {
            $zones = Zone::all();
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zones = Zone::where('pastor_id', $user->id)->get();
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('zone_id', $user->getZoneId());
            })->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = Zone::where('id', $zoneId)->get();
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->get();
        } elseif ($user->role === 'lider_celula') {
            $cells = Cell::where('leader_id', $user->id)->get();
        }

        return view('events.edit', compact('event', 'eventTypes', 'zones', 'cells'));
    }

    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|string|max:255',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'participants_count' => 'required|integer|min:0',
            'observations' => 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    /**
     * Bulk delete events
     */
    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        $validated = $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id'
        ]);

        $deletedCount = Event::whereIn('id', $validated['event_ids'])->delete();

        return redirect()->route('events.index')
            ->with('success', "{$deletedCount} evento(s) excluído(s) com sucesso!");
    }
}

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
    public function downloadPdf(Event $event)
    {
        Gate::authorize('view', $event);
        $event->load(['eventType', 'zone', 'cell']);

        $pdf = Pdf::loadView('events.pdf', compact('event'));
        return $pdf->download('Relatorio_Culto_' . $event->date->format('d_m_Y') . '.pdf');
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
            $message->to($request->email)
                ->subject('Relatório de Culto - ' . $event->date->format('d/m/Y'))
                ->html('Olá,<br><br>Segue em anexo o relatório do culto/evento realizado em ' . $event->date->format('d/m/Y') . '.<br><br>Atenciosamente,<br>Portal Life Church')
                ->attachData($pdfContent, 'Relatorio_Culto_' . $event->date->format('d_m_Y') . '.pdf', [
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
            $query->where('zone_id', $zoneId);
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $query->where('zone_id', $zoneId);
        } elseif ($user->role === 'lider_celula') {
            $query->whereHas('cell', function ($q) use ($user) {
                $q->where('leader_id', $user->id);
            });
        }

        $events = $query->orderBy('date', 'desc')->paginate(15);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        Gate::authorize('create', Event::class);

        $user = auth()->user();
        $eventTypes = EventType::where('is_active', true)->get();
        $zones = collect();
        $cells = collect();

        if ($user->role === 'admin' || $user->role === 'pastor') {
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
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'date' => 'required|date',
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
        $eventTypes = EventType::where('is_active', true)->get();
        $zones = collect();
        $cells = collect();

        if ($user->role === 'admin' || $user->role === 'pastor') {
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
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'date' => 'required|date',
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
}

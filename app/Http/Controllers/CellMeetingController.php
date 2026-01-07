<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\CellMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CellMeetingController extends Controller
{
    public function downloadPdf(CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);
        $cellMeeting->load(['cell.supervision.zone', 'leader']);

        $pdf = Pdf::loadView('cell_meetings.pdf', compact('cellMeeting'));
        $date = Carbon::parse($cellMeeting->meeting_date);
        return $pdf->download('Relatorio_Celula_' . $date->format('d_m_Y') . '.pdf');
    }

    public function sendEmail(Request $request, CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);

        $request->validate([
            'email' => 'required|email'
        ]);

        $cellMeeting->load(['cell.supervision.zone', 'leader']);
        $pdf = Pdf::loadView('cell_meetings.pdf', compact('cellMeeting'));
        $pdfContent = $pdf->output();

        Mail::send([], [], function ($message) use ($request, $cellMeeting, $pdfContent) {
            $date = Carbon::parse($cellMeeting->meeting_date);
            $type = 'Relatório de Célula';
            if ($cellMeeting->meeting_type === 'leadership')
                $type = 'Acta de Reunião de Liderança';
            if ($cellMeeting->meeting_type === 'supervision')
                $type = 'Acta de Reunião de Supervisão';
            if ($cellMeeting->meeting_type === 'zone')
                $type = 'Acta de Reunião de Zona';

            $message->to($request->email)
                ->subject($type . ' - ' . $date->format('d/m/Y'))
                ->html('Olá,<br><br>Segue em anexo o/a ' . strtolower($type) . ' realizado em ' . $date->format('d/m/Y') . '.<br><br>Atenciosamente,<br>Portal Life Church')
                ->attachData($pdfContent, str_replace(' ', '_', $type) . '_' . $date->format('d_m_Y') . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return back()->with('success', 'Relatório enviado com sucesso para ' . $request->email);
    }
    public function index()
    {
        Gate::authorize('viewAny', CellMeeting::class);

        $user = auth()->user();
        $query = CellMeeting::with(['cell', 'leader']);

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->whereHas('cell.supervision', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        } elseif ($user->role === 'supervisor') {
            $query->whereHas('cell', function ($q) use ($user) {
                $q->whereHas('supervision', function ($sq) use ($user) {
                    $sq->where('supervisor_id', $user->id);
                });
            });
        } elseif ($user->role === 'lider_celula') {
            $query->whereHas('cell', function ($q) use ($user) {
                $q->where('leader_id', $user->id);
            });
        }

        $meetings = $query->orderBy('meeting_date', 'desc')->paginate(15);

        return view('cell_meetings.index', compact('meetings'));
    }

    public function create()
    {
        Gate::authorize('create', CellMeeting::class);

        $user = auth()->user();
        $cells = collect();

        if ($user->role === 'admin' || $user->role === 'pastor') {
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $cells = Cell::whereHas('supervision', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            })->get();
        } elseif ($user->role === 'supervisor') {
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->get();
        } elseif ($user->role === 'lider_celula') {
            $cells = Cell::where('leader_id', $user->id)->get();
        }

        $leaders = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'timoteo'])->get();

        $selectedCellId = request('cell_id');
        $members = collect();
        if ($selectedCellId) {
            $members = User::where('cell_id', $selectedCellId)->where('is_active', true)->orderBy('name')->get();
        } elseif ($user->role === 'lider_celula') {
            $cell = $user->ledCells()->first();
            if ($cell) {
                $members = $cell->members()->where('is_active', true)->orderBy('name')->get();
            }
        }

        return view('cell_meetings.create', compact('cells', 'leaders', 'members'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', CellMeeting::class);

        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
            'meeting_date' => 'required|date',
            'theme' => 'nullable|string|max:255',
            'biblical_text' => 'nullable|string|max:255',
            'leader_id' => 'required|exists:users,id',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'decisions' => 'nullable|string',
            'meeting_type' => 'required|string|in:normal,leadership,supervision,zone,other',
            'minutes' => 'nullable|string',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'observations' => 'nullable|string',
        ]);

        // Check for duplicate meeting on same date for same cell
        $exists = CellMeeting::where('cell_id', $validated['cell_id'])
            ->where('meeting_date', $validated['meeting_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['meeting_date' => 'Já existe um encontro registrado para esta célula nesta data.'])->withInput();
        }

        $meeting = CellMeeting::create($validated);

        if ($request->has('participants')) {
            $meeting->participants()->sync($request->participants);
        }

        // Record attendance for present members
        if ($request->has('present_members')) {
            foreach ($request->present_members as $userId) {
                \App\Models\Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'cell_id' => $meeting->cell_id,
                        'date' => \Carbon\Carbon::parse($meeting->meeting_date)->format('Y-m-d'),
                        'type' => 'cell',
                    ],
                    [
                        'status' => true,
                    ]
                );
            }
        }

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro de célula registrado com sucesso!');
    }

    public function show(CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);

        $cellMeeting->load(['cell.supervision.zone', 'leader']);

        return view('cell_meetings.show', compact('cellMeeting'));
    }

    public function edit(CellMeeting $cellMeeting)
    {
        Gate::authorize('update', $cellMeeting);

        $user = auth()->user();
        $cells = collect();

        if ($user->role === 'admin' || $user->role === 'pastor') {
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $cells = Cell::whereHas('supervision', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            })->get();
        } elseif ($user->role === 'supervisor') {
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->get();
        } elseif ($user->role === 'lider_celula') {
            $cells = Cell::where('leader_id', $user->id)->get();
        }

        $leaders = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor', 'lider_celula', 'timoteo'])->get();

        return view('cell_meetings.edit', compact('cellMeeting', 'cells', 'leaders'));
    }

    public function update(Request $request, CellMeeting $cellMeeting)
    {
        Gate::authorize('update', $cellMeeting);

        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string|in:normal,leadership,supervision,zone,other',
            'minutes' => 'nullable|string',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'theme' => 'nullable|string|max:255',
            'biblical_text' => 'nullable|string|max:255',
            'leader_id' => 'required|exists:users,id',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'decisions' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        // Check for duplicate meeting on same date for same cell (excluding current)
        $exists = CellMeeting::where('cell_id', $validated['cell_id'])
            ->where('meeting_date', $validated['meeting_date'])
            ->where('id', '!=', $cellMeeting->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['meeting_date' => 'Já existe um encontro registrado para esta célula nesta data.'])->withInput();
        }

        $cellMeeting->update($validated);

        if ($request->has('participants')) {
            $cellMeeting->participants()->sync($request->participants);
        }

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro de célula atualizado com sucesso!');
    }

    public function destroy(CellMeeting $cellMeeting)
    {
        Gate::authorize('delete', $cellMeeting);

        $cellMeeting->delete();

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro de célula excluído com sucesso!');
    }
}

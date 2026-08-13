<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Discipleship;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendanceController extends Controller
{
    use AuthorizesRequests;

    public function index(Cell $cell, Request $request)
    {
        $this->authorize('view', $cell);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        [$date, $saturdays, $sundays, $wednesdays] = $this->monthDates($year, $month);

        $members = $cell->members()->where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::where('cell_id', $cell->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy([
                'user_id',
                'type',
                function ($item) {
                    return $item->date->format('Y-m-d');
                }
            ]);

        $visitors = $cell->visitors()->whereYear('visit_date', $year)->whereMonth('visit_date', $month)->get();
        $discipleships = $cell->discipleships()->with('user')->get();
        $conversions = $cell->conversions()->whereYear('date', $year)->whereMonth('date', $month)->get();
        $baptismCandidates = $members->where('baptism_status', 'candidate');
        $baptizedMembers = $members->where('baptism_status', 'baptized');

        return view('admin.cells.attendance', compact(
            'cell',
            'members',
            'attendances',
            'month',
            'year',
            'date',
            'visitors',
            'discipleships',
            'conversions',
            'baptismCandidates',
            'baptizedMembers',
            'saturdays',
            'sundays',
            'wednesdays'
        ));
    }

    public function store(Cell $cell, Request $request)
    {
        $this->authorize('update', $cell);

        $attendanceData = $request->input('attendance', []);
        $reasons = $request->input('reason', []);

        foreach ($attendanceData as $userId => $types) {
            foreach ($types as $type => $dates) {
                foreach ($dates as $dateStr => $status) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'cell_id' => $cell->id,
                            'date' => $dateStr,
                            'type' => $type,
                        ],
                        [
                            'status' => (bool) $status,
                            'reason' => $reasons[$userId] ?? null,
                        ]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Presenças actualizadas com sucesso!');
    }

    public function storeVisitor(Cell $cell, Request $request)
    {
        $this->authorize('update', $cell);

        $request->validate([
            'name' => 'required|string|max:255',
            'visit_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $cell->visitors()->create($data);

        return redirect()->back()->with('success', 'Visita registada com sucesso!');
    }

    public function storeDiscipleship(Cell $cell, Request $request)
    {
        $this->authorize('update', $cell);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'start_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        foreach ($request->user_ids as $userId) {
            $cell->discipleships()->create([
                'user_id' => $userId,
                'mentor_name' => $request->mentor_name,
                'start_date' => $request->start_date,
                'current_lesson' => $request->current_lesson,
                'observations' => $request->observations,
            ]);
        }

        return redirect()->back()->with('success', 'Discipulado(s) registado(s) com sucesso!');
    }

    public function storeConversion(Cell $cell, Request $request)
    {
        $this->authorize('update', $cell);

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $cell->conversions()->create($request->all());

        return redirect()->back()->with('success', 'Registo de salvação/baptismo guardado!');
    }

    public function storeBaptismStatus(Cell $cell, Request $request)
    {
        $this->authorize('update', $cell);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'baptism_status' => 'required|in:not_baptized,candidate,baptized',
            'baptism_decision_date' => 'nullable|date',
            'baptism_date' => 'nullable|date',
            'baptism_notes' => 'nullable|string|max:1000',
        ]);

        $member = User::where('cell_id', $cell->id)->findOrFail($validated['user_id']);

        if ($validated['baptism_status'] === 'baptized' && empty($validated['baptism_date'])) {
            return back()
                ->withErrors(['baptism_date' => 'Informe a data do baptismo para membros já baptizados.'])
                ->withInput();
        }

        $member->update([
            'baptism_status' => $validated['baptism_status'],
            'baptism_decision_date' => $validated['baptism_decision_date'] ?? null,
            'baptism_date' => $validated['baptism_status'] === 'baptized' ? $validated['baptism_date'] : null,
            'baptism_notes' => $validated['baptism_notes'] ?? null,
        ]);

        return back()->with('success', 'Registo de baptismo actualizado com sucesso!');
    }

    public function downloadPdf(Cell $cell, Request $request)
    {
        $this->authorize('view', $cell);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        [$date, $saturdays, $sundays, $wednesdays] = $this->monthDates($year, $month);

        $cell->load(['supervision.zone', 'leader']);
        $members = $cell->members()->where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::where('cell_id', $cell->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy([
                'user_id',
                'type',
                fn ($item) => $item->date->format('Y-m-d'),
            ]);

        $pdf = Pdf::loadView('admin.cells.attendance-pdf', compact(
            'cell',
            'members',
            'attendances',
            'month',
            'year',
            'date',
            'saturdays',
            'sundays',
            'wednesdays'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("ficha_presenca_{$cell->name}_{$year}_{$month}.pdf");
    }

    public function updateDiscipleship(Cell $cell, Discipleship $discipleship, Request $request)
    {
        $this->authorize('update', $cell);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
        ]);

        $discipleship->update($request->all());

        return redirect()->back()->with('success', 'Discipulado actualizado com sucesso!');
    }

    public function destroyDiscipleship(Cell $cell, Discipleship $discipleship)
    {
        $this->authorize('update', $cell);

        $discipleship->delete();

        return redirect()->back()->with('success', 'Discipulado removido com sucesso!');
    }

    /**
     * Permite ao líder da célula (ou pastor/supervisor) registar o feedback de contacto com o visitante.
     */
    public function updateVisitorFeedback(Cell $cell, \App\Models\Visitor $visitor, Request $request)
    {
        $this->authorize('update', $cell);

        $user = auth()->user();
        
        // Garantir que o visitante pertence à célula
        if ($visitor->cell_id !== $cell->id) {
            abort(403, 'Este visitante não pertence à célula indicada.');
        }

        // Se for líder, garantir que lidera esta célula
        if ($user->role === 'lider_celula' && $cell->leader_id !== $user->id) {
            abort(403, 'Você não tem permissão para atualizar feedback nesta célula.');
        }

        $validated = $request->validate([
            'contact_status' => 'required|in:pendente,contatado,integrado,sem_interesse',
            'notes' => 'nullable|string',
        ]);

        $updateData = [
            'contact_status' => $validated['contact_status'],
            'notes' => $validated['notes'],
        ];

        // Se o status for alterado para contatado, registar quem e quando
        if ($validated['contact_status'] === 'contatado' && !$visitor->isContacted()) {
            $updateData['contacted_at'] = now();
            $updateData['contacted_by'] = $user->id;
        }

        $visitor->update($updateData);

        return redirect()->back()->with('success', 'Feedback do visitante atualizado com sucesso!');
    }

    private function monthDates(int $year, int $month): array
    {
        $date = Carbon::createFromDate($year, $month, 1);
        $saturdays = [];
        $sundays = [];
        $wednesdays = [];

        $tempDate = $date->copy()->startOfMonth();
        while ($tempDate->month === $month) {
            if ($tempDate->isSaturday()) {
                $saturdays[] = $tempDate->copy();
            } elseif ($tempDate->isSunday()) {
                $sundays[] = $tempDate->copy();
            } elseif ($tempDate->isWednesday()) {
                $wednesdays[] = $tempDate->copy();
            }
            $tempDate->addDay();
        }

        return [$date, $saturdays, $sundays, $wednesdays];
    }
}

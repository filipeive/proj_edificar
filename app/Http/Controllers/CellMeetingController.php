<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\CellMeeting;
use App\Models\Zone;
use App\Models\Supervision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CellMeetingsExport;

class CellMeetingController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', CellMeeting::class);

        $user = auth()->user();
        $query = CellMeeting::with(['cell', 'leader', 'zone', 'supervision']);

        // Base Hierarchical Filter
        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->where(function ($q) use ($zoneId) {
                $q->whereHas('cell.supervision', function ($sq) use ($zoneId) {
                    $sq->where('zone_id', $zoneId);
                })->orWhere('zone_id', $zoneId);
            });
        } elseif ($user->role === 'supervisor') {
            $supervisionIds = $user->getManagedSupervisionIds();
            $query->where(function ($q) use ($supervisionIds) {
                $q->whereHas('cell', function ($cq) use ($supervisionIds) {
                    $cq->whereIn('supervision_id', $supervisionIds);
                })->orWhereIn('supervision_id', $supervisionIds);
            });
        } elseif ($user->role === 'lider_celula' || $user->role === 'timoteo') {
            $query->whereHas('cell', function ($q) use ($user) {
                $q->where('leader_id', $user->id)
                    ->orWhere('id', $user->cell_id);
            });
        }

        // Additional Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'LIKE', "%{$search}%")
                    ->orWhere('biblical_text', 'LIKE', "%{$search}%")
                    ->orWhereHas('cell', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('leader', function ($lq) use ($search) {
                        $lq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        }

        if ($request->filled('meeting_type')) {
            $query->where('meeting_type', $request->meeting_type);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('meeting_date', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('meeting_date', '<=', $request->date_end);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('supervision_id')) {
            $query->where('supervision_id', $request->supervision_id);
        }

        $meetings = $query->orderBy('meeting_date', 'desc')->paginate(15)->withQueryString();

        // Data for filters
        $cells = collect();
        if ($user->isAdmin() || $user->role === 'pastor') {
            $cells = Cell::orderBy('name')->get();
        } elseif ($user->role === 'pastor_zona') {
            $cells = Cell::whereHas('supervision', function ($q) use ($user) {
                $q->where('zone_id', $user->getZoneId());
            })->orderBy('name')->get();
        } elseif ($user->role === 'supervisor') {
            $cells = Cell::whereIn('supervision_id', $user->getManagedSupervisionIds())->orderBy('name')->get();
        }

        $statsQuery = clone $query;
        $stats = [
            'total_meetings' => $statsQuery->count(),
            'total_attendance' => (int) $statsQuery->sum(\DB::raw('adults_count + children_count + visitors_count')),
            'avg_attendance' => $statsQuery->count() > 0
                ? round($statsQuery->sum(\DB::raw('adults_count + children_count + visitors_count')) / $statsQuery->count(), 1)
                : 0,
            'total_decisions' => $statsQuery->whereNotNull('decisions')->where('decisions', '!=', '')->count(),
        ];

        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::orderBy('name')->get();

        return view('cell_meetings.index', compact('meetings', 'cells', 'stats', 'zones', 'supervisions'));
    }

    public function export(Request $request)
    {
        Gate::authorize('viewAny', CellMeeting::class);

        $user = auth()->user();
        $query = CellMeeting::with(['cell', 'leader', 'zone', 'supervision']);

        // Apply same filters as index
        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->where(function ($q) use ($zoneId) {
                $q->whereHas('cell.supervision', function ($sq) use ($zoneId) {
                    $sq->where('zone_id', $zoneId);
                })->orWhere('zone_id', $zoneId);
            });
        } elseif ($user->role === 'supervisor') {
            $supervisionIds = $user->getManagedSupervisionIds();
            $query->where(function ($q) use ($supervisionIds) {
                $q->whereHas('cell', function ($cq) use ($supervisionIds) {
                    $cq->whereIn('supervision_id', $supervisionIds);
                })->orWhereIn('supervision_id', $supervisionIds);
            });
        } elseif ($user->role === 'lider_celula' || $user->role === 'timoteo') {
            $query->whereHas('cell', function ($q) use ($user) {
                $q->where('leader_id', $user->id)
                    ->orWhere('id', $user->cell_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'LIKE', "%{$search}%")
                    ->orWhere('biblical_text', 'LIKE', "%{$search}%")
                    ->orWhereHas('cell', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('leader', function ($lq) use ($search) {
                        $lq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('cell_id')) {
            $query->where('cell_id', $request->cell_id);
        }

        if ($request->filled('meeting_type')) {
            $query->where('meeting_type', $request->meeting_type);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('meeting_date', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('meeting_date', '<=', $request->date_end);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('supervision_id')) {
            $query->where('supervision_id', $request->supervision_id);
        }

        if ($request->filled('meeting_id')) {
            $query->where('id', $request->meeting_id);
        }

        $meetings = $query->orderBy('meeting_date', 'desc')->get();

        return Excel::download(new CellMeetingsExport($meetings), 'Encontros_Celula.xlsx');
    }

    public function bulkDestroy(Request $request)
    {
        Gate::authorize('deleteAny', CellMeeting::class);

        $validated = $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:cell_meetings,id'
        ]);

        $count = CellMeeting::whereIn('id', $validated['selected_ids'])->delete();

        return redirect()->route('cell-meetings.index')->with('success', "{$count} encontro(s) excluído(s) com sucesso!");
    }

    public function create()
    {
        Gate::authorize('create', CellMeeting::class);

        $user = auth()->user();
        $cells = collect();

        if ($user->isAdmin() || $user->role === 'pastor') {
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $cells = Cell::whereHas('supervision', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            })->get();
        } elseif ($user->role === 'supervisor') {
            $cells = Cell::whereIn('supervision_id', $user->getManagedSupervisionIds())->get();
        } elseif ($user->role === 'lider_celula' || $user->role === 'timoteo') {
            $cells = Cell::where('leader_id', $user->id)
                ->orWhere('id', $user->cell_id)
                ->orderBy('name')->get();
        } else {
            $cells = collect();
        }

        $leadersQuery = User::whereIn('role', ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo']);

        if ($user->isPastorZona()) {
            $zoneId = $user->getZoneId();
            $leadersQuery->where(function ($q) use ($zoneId, $user) {
                $q->where('id', $user->id)
                    ->orWhereHas('cell.supervision', function ($sq) use ($zoneId) {
                        $sq->where('zone_id', $zoneId);
                    })
                    ->orWhereHas('supervisedSupervisions', function ($sq) use ($zoneId) {
                        $sq->where('zone_id', $zoneId);
                    });
            });
        } elseif ($user->isSupervisor()) {
            $supervisionIds = $user->getManagedSupervisionIds();
            $leadersQuery->where(function ($q) use ($user, $supervisionIds) {
                $q->where('id', $user->id)
                    ->orWhereHas('cell', function ($cq) use ($supervisionIds) {
                        $cq->whereIn('supervision_id', $supervisionIds);
                    })
                    ->orWhereHas('supervisedSupervisions', function ($sq) use ($supervisionIds) {
                        $sq->whereIn('id', $supervisionIds);
                    });
            });
        } elseif ($user->isLider() || $user->isTimoteo()) {
            $leadersQuery->where('id', $user->id)
                ->orWhere('cell_id', $user->cell_id);
        }

        $leaders = $leadersQuery->orderBy('name')->get();

        $selectedCellId = request('cell_id');
        $members = collect();
        if ($selectedCellId) {
            $members = User::where('cell_id', $selectedCellId)->where('is_active', true)->orderBy('name')->get();
        } elseif ($user->role === 'lider_celula' || $user->role === 'timoteo') {
            $cell = Cell::where('leader_id', $user->id)->orWhere('id', $user->cell_id)->first();
            if ($cell) {
                $members = $cell->members()->where('is_active', true)->orderBy('name')->get();
            }
        }

        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::orderBy('name')->get();

        $allLeaders = User::whereIn('role', ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo'])->orderBy('name')->get();

        return view('cell_meetings.create', compact('cells', 'leaders', 'members', 'zones', 'supervisions', 'allLeaders'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', CellMeeting::class);

        $validated = $request->validate([
            'cell_id' => 'required_if:meeting_type,normal|nullable|exists:cells,id',
            'zone_id' => 'required_if:meeting_type,zone|nullable|exists:zones,id',
            'supervision_id' => 'required_if:meeting_type,supervision|nullable|exists:supervisions,id',
            'meeting_date' => 'required|date',
            'theme' => 'nullable|string|max:255',
            'biblical_text' => 'nullable|string|max:255',
            'leader_id' => 'required|exists:users,id',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'decisions' => 'nullable|string',
            'meeting_type' => 'required|string|in:normal,leadership,supervision,zone,general,other',
            'minutes' => 'nullable|string',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'observations' => 'nullable|string',
            'present_members' => 'nullable|array',
            'present_members.*' => 'exists:users,id',
            'reasons' => 'nullable|array',
            'reasons.*' => 'nullable|string|max:255',
        ]);

        // Set correct associations based on meeting type
        $validated['cell_id'] = $validated['meeting_type'] === 'normal' ? ($validated['cell_id'] ?? null) : null;
        $validated['zone_id'] = $validated['meeting_type'] === 'zone' ? ($validated['zone_id'] ?? null) : null;
        $validated['supervision_id'] = $validated['meeting_type'] === 'supervision' ? ($validated['supervision_id'] ?? null) : null;

        // Check for duplicate meeting on same date for same cell (only for cell meetings)
        if (!empty($validated['cell_id'])) {
            $exists = CellMeeting::where('cell_id', $validated['cell_id'])
                ->where('meeting_date', $validated['meeting_date'])
                ->exists();

            if ($exists) {
                return back()->withErrors(['meeting_date' => 'Já existe um encontro registrado para esta célula nesta data.'])->withInput();
            }
        }

        $meeting = CellMeeting::create($validated);

        if ($request->has('participants')) {
            $meeting->participants()->sync($request->participants);
        }

        // Record attendance only for cell meetings
        if ($meeting->cell_id) {
            $allCellMembers = User::where('cell_id', $meeting->cell_id)->where('is_active', true)->pluck('id');
            $presentIds = $request->input('present_members', []);
            $reasons = $request->input('reasons', []);

            foreach ($allCellMembers as $userId) {
                $isPresent = in_array($userId, $presentIds);
                \App\Models\Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'cell_id' => $meeting->cell_id,
                        'date' => \Carbon\Carbon::parse($meeting->meeting_date)->format('Y-m-d'),
                        'type' => 'cell',
                    ],
                    [
                        'status' => $isPresent,
                        'reason' => $isPresent ? null : ($reasons[$userId] ?? null),
                    ]
                );
            }
        }

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro registrado com sucesso!');
    }

    public function show(CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);

        $date = $cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('Y-m-d') : null;
        $cellMeeting->load([
            'cell.supervision.zone',
            'leader',
            'attendances' => function ($query) use ($date) {
                $query->where('date', $date)->with('member');
            },
            'visitors' => function ($query) use ($date) {
                $query->where('visit_date', $date);
            }
        ]);

        return view('cell_meetings.show', compact('cellMeeting'));
    }

    public function downloadPdf(CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);

        $date = $cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('Y-m-d') : null;
        $cellMeeting->load([
            'cell.supervision.zone',
            'leader',
            'participants',
            'attendances' => function ($query) use ($date) {
                $query->where('date', $date)->with('member');
            },
            'visitors' => function ($query) use ($date) {
                $query->where('visit_date', $date);
            }
        ]);

        $pdf = Pdf::loadView('cell_meetings.pdf', compact('cellMeeting'));
        $filename = "Encontro_" . ($cellMeeting->cell?->name ?? $cellMeeting->zone?->name ?? $cellMeeting->supervision?->name ?? $cellMeeting->meeting_type_label) . "_" . ($cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('d-m-Y') : 'data-nd');
        return $pdf->download(str_replace(' ', '_', $filename) . ".pdf");
    }

    public function sendEmail(Request $request, CellMeeting $cellMeeting)
    {
        Gate::authorize('view', $cellMeeting);

        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $date = $cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('Y-m-d') : null;
        $cellMeeting->load([
            'cell.supervision.zone',
            'leader',
            'participants',
            'attendances' => function ($query) use ($date) {
                $query->where('date', $date)->with('member');
            },
            'visitors' => function ($query) use ($date) {
                $query->where('visit_date', $date);
            }
        ]);

        try {
            Mail::send('emails.cell_meeting_report', ['cellMeeting' => $cellMeeting], function ($message) use ($validated, $cellMeeting) {
                $subjectName = $cellMeeting->cell?->name ?? $cellMeeting->zone?->name ?? $cellMeeting->supervision?->name ?? $cellMeeting->meeting_type_label;
                $formattedDate = $cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('d/m/Y') : 'N/D';
                $message->to($validated['email'])
                    ->subject("Relatório de Encontro: {$subjectName} - {$formattedDate}");
            });

            return back()->with('success', 'Relatório enviado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }

    public function edit(CellMeeting $cellMeeting)
    {
        Gate::authorize('update', $cellMeeting);

        $user = auth()->user();
        $cells = collect();

        if ($user->isAdmin() || $user->role === 'pastor') {
            $cells = Cell::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $cells = Cell::whereHas('supervision', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            })->get();
        } elseif ($user->role === 'supervisor') {
            $supervisionIds = $user->getManagedSupervisionIds();
            $cells = Cell::whereIn('supervision_id', $supervisionIds)->get();
        } elseif ($user->role === 'lider_celula') {
            $cells = Cell::where('leader_id', $user->id)->get();
        }

        $leadersQuery = User::whereIn('role', ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo']);

        if ($user->isPastorZona()) {
            $zoneId = $user->getZoneId();
            $leadersQuery->where(function ($q) use ($zoneId, $user) {
                $q->where('id', $user->id)
                    ->orWhereHas('cell.supervision', function ($sq) use ($zoneId) {
                        $sq->where('zone_id', $zoneId);
                    })
                    ->orWhereHas('supervisedSupervisions', function ($sq) use ($zoneId) {
                        $sq->where('zone_id', $zoneId);
                    });
            });
        } elseif ($user->isSupervisor()) {
            $supervisionIds = $user->getManagedSupervisionIds();
            $leadersQuery->where(function ($q) use ($user, $supervisionIds) {
                $q->where('id', $user->id)
                    ->orWhereHas('cell', function ($cq) use ($supervisionIds) {
                        $cq->whereIn('supervision_id', $supervisionIds);
                    })
                    ->orWhereHas('supervisedSupervisions', function ($sq) use ($supervisionIds) {
                        $sq->whereIn('id', $supervisionIds);
                    });
            });
        } elseif ($user->isLider()) {
            $leadersQuery->where('id', $user->id)
                ->orWhere('cell_id', $user->cell_id);
        }

        $leaders = $leadersQuery->orderBy('name')->get();

        $members = User::where('cell_id', $cellMeeting->cell_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $attendances = \App\Models\Attendance::where('cell_id', $cellMeeting->cell_id)
            ->where('date', $cellMeeting->meeting_date ? Carbon::parse($cellMeeting->meeting_date)->format('Y-m-d') : null)
            ->where('type', 'cell')
            ->get()
            ->keyBy('user_id');

        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::orderBy('name')->get();

        $allLeaders = User::whereIn('role', ['admin', 'pastor_senior', 'pastor', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo'])->orderBy('name')->get();

        return view('cell_meetings.edit', compact('cellMeeting', 'cells', 'leaders', 'members', 'attendances', 'zones', 'supervisions', 'allLeaders'));
    }

    public function update(Request $request, CellMeeting $cellMeeting)
    {
        Gate::authorize('update', $cellMeeting);

        $validated = $request->validate([
            'cell_id' => 'required_if:meeting_type,normal|nullable|exists:cells,id',
            'zone_id' => 'required_if:meeting_type,zone|nullable|exists:zones,id',
            'supervision_id' => 'required_if:meeting_type,supervision|nullable|exists:supervisions,id',
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string|in:normal,leadership,supervision,zone,general,other',
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
            'present_members' => 'nullable|array',
            'present_members.*' => 'exists:users,id',
            'reasons' => 'nullable|array',
            'reasons.*' => 'nullable|string|max:255',
        ]);

        // Set correct associations based on meeting type
        $validated['cell_id'] = $validated['meeting_type'] === 'normal' ? ($validated['cell_id'] ?? null) : null;
        $validated['zone_id'] = $validated['meeting_type'] === 'zone' ? ($validated['zone_id'] ?? null) : null;
        $validated['supervision_id'] = $validated['meeting_type'] === 'supervision' ? ($validated['supervision_id'] ?? null) : null;

        // Check for duplicate meeting on same date for same cell (excluding current, only for cell meetings)
        if (!empty($validated['cell_id'])) {
            $exists = CellMeeting::where('cell_id', $validated['cell_id'])
                ->where('meeting_date', $validated['meeting_date'])
                ->where('id', '!=', $cellMeeting->id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['meeting_date' => 'Já existe um encontro registrado para esta célula nesta data.'])->withInput();
            }
        }

        $cellMeeting->update($validated);

        if ($request->has('participants')) {
            $cellMeeting->participants()->sync($request->participants);
        }

        // Record attendance only for cell meetings
        if ($cellMeeting->cell_id) {
            $allCellMembers = User::where('cell_id', $cellMeeting->cell_id)->where('is_active', true)->pluck('id');
            $presentIds = $request->input('present_members', []);
            $reasons = $request->input('reasons', []);

            foreach ($allCellMembers as $userId) {
                $isPresent = in_array($userId, $presentIds);
                \App\Models\Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'cell_id' => $cellMeeting->cell_id,
                        'date' => \Carbon\Carbon::parse($cellMeeting->meeting_date)->format('Y-m-d'),
                        'type' => 'cell',
                    ],
                    [
                        'status' => $isPresent,
                        'reason' => $isPresent ? null : ($reasons[$userId] ?? null),
                    ]
                );
            }
        }

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro atualizado com sucesso!');
    }

    public function destroy(CellMeeting $cellMeeting)
    {
        Gate::authorize('delete', $cellMeeting);

        $cellMeeting->delete();

        return redirect()->route('cell-meetings.index')->with('success', 'Encontro de célula excluído com sucesso!');
    }
}

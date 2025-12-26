<?php

namespace App\Http\Controllers;

use App\Models\Cell;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Cell $cell, Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $date = Carbon::createFromDate($year, $month, 1);

        // Find all Saturdays (Cell), Sundays (Service), and Wednesdays in the month
        $saturdays = [];
        $sundays = [];
        $wednesdays = [];

        $tempDate = $date->copy()->startOfMonth();
        while ($tempDate->month == $month) {
            if ($tempDate->isSaturday()) {
                $saturdays[] = $tempDate->copy();
            } elseif ($tempDate->isSunday()) {
                $sundays[] = $tempDate->copy();
            } elseif ($tempDate->isWednesday()) {
                $wednesdays[] = $tempDate->copy();
            }
            $tempDate->addDay();
        }

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
            'saturdays',
            'sundays',
            'wednesdays'
        ));
    }

    public function store(Cell $cell, Request $request)
    {
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
        $request->validate([
            'name' => 'required|string|max:255',
            'visit_date' => 'required|date',
        ]);

        $cell->visitors()->create($request->all());

        return redirect()->back()->with('success', 'Visita registada com sucesso!');
    }

    public function storeDiscipleship(Cell $cell, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
        ]);

        $cell->discipleships()->create($request->all());

        return redirect()->back()->with('success', 'Discipulado registado com sucesso!');
    }

    public function storeConversion(Cell $cell, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $cell->conversions()->create($request->all());

        return redirect()->back()->with('success', 'Registo de salvação/baptismo guardado!');
    }
}

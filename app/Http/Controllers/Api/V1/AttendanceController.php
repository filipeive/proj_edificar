<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Attendance;
use App\Models\Cell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends BaseApiController
{
    /**
     * Display a listing of attendances for a cell.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2100',
        ]);

        $cellId = $validated['cell_id'];
        $month = $validated['month'] ?? now()->month;
        $year = $validated['year'] ?? now()->year;

        $attendances = Attendance::where('cell_id', $cellId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return $this->sendResponse($attendances, 'Registos de presença recuperados.');
    }

    /**
     * Store or update attendance records.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cell_id' => 'required|exists:cells,id',
            'records' => 'required|array',
            'records.*.user_id' => 'required|exists:users,id',
            'records.*.date' => 'required|date',
            'records.*.type' => 'required|in:sabado,domingo,quarta',
            'records.*.status' => 'required|boolean',
            'records.*.reason' => 'nullable|string',
        ]);

        $cellId = $validated['cell_id'];
        $updatedRecords = [];

        foreach ($validated['records'] as $record) {
            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $record['user_id'],
                    'cell_id' => $cellId,
                    'date' => $record['date'],
                    'type' => $record['type'],
                ],
                [
                    'status' => $record['status'],
                    'reason' => $record['reason'] ?? null,
                ]
            );
            $updatedRecords[] = $attendance;
        }

        return $this->sendResponse($updatedRecords, 'Presenças registadas com sucesso.', [], 201);
    }
}

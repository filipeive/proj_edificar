<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoupleEnrollment;
use App\Models\QuarterlyReport;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicFormController extends Controller
{
    public function showPreMaritalForm()
    {
        $course = Course::where('slug', 'like', '%nupcial%')
            ->orWhere('slug', 'like', '%casais%')
            ->orWhere('name', 'like', '%casais%')
            ->first();

        if (!$course) {
            return redirect()->route('welcome')->with('error', 'Curso pré-marital não encontrado.');
        }

        return view('public.courses.pre-marital', compact('course'));
    }

    public function storePreMarital(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'couple_name' => 'required|string|max:255',
            'relationship_type' => 'required|in:namoro,noivos,vivendo_maritalmente,casados',
            'address' => 'required|string|max:255',
            'contacts' => 'required|string|max:255',
            'cell_zone' => 'nullable|string|max:255',
            'years_together_text' => 'nullable|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'has_pastoral_recommendation' => 'required|boolean',
            'observations' => 'nullable|string',
        ]);

        $parts = preg_split('/\\s*(?:e|&|\\/)\\s*/i', $validated['couple_name'], 2);
        $husbandName = trim($parts[0] ?? $validated['couple_name']);
        $wifeName = trim($parts[1] ?? '');
        if ($wifeName === '') {
            $wifeName = 'Não informado';
        }

        $yearsRaw = $validated['years_together_text'] ?? '';
        $years = 0;
        if ($yearsRaw !== '') {
            if (preg_match('/(\\d+)/', $yearsRaw, $match)) {
                $num = (int) $match[1];
                if (preg_match('/mes/i', $yearsRaw)) {
                    $years = 0;
                } else {
                    $years = $num;
                }
            }
        }

        $observations = $validated['observations'] ?? null;
        if ($yearsRaw !== '' && $years === 0) {
            $observations = trim(($observations ? $observations . ' | ' : '') . 'Tempo informado: ' . $yearsRaw);
        }

        CoupleEnrollment::create([
            'course_id' => $validated['course_id'],
            'husband_name' => $husbandName,
            'wife_name' => $wifeName,
            'relationship_type' => $validated['relationship_type'],
            'address' => $validated['address'],
            'contacts' => $validated['contacts'],
            'cell_zone' => $validated['cell_zone'] ?? null,
            'years_together' => $years,
            'leader_name' => $validated['leader_name'] ?? null,
            'has_pastoral_recommendation' => $validated['has_pastoral_recommendation'],
            'observations' => $observations,
            'status' => 'pending',
        ]);

        return redirect()->route('public.forms.pre-marital')
            ->with('success', 'Inscrição enviada com sucesso! Entraremos em contacto em breve.');
    }

    public function showQuarterlyReportForm()
    {
        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::orderBy('name')->get();
        return view('public.reports.quarterly', compact('zones', 'supervisions'));
    }

    public function storeQuarterlyReport(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'year' => 'required|integer|min:2000|max:2100',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'nullable|integer|min:0',
            'cells_count' => 'nullable|integer|min:0',
            'timoteos_count' => 'nullable|integer|min:0',
            'members_count' => 'nullable|integer|min:0',
            'participants_count' => 'nullable|integer|min:0',
            'pastors_count' => 'nullable|integer|min:0',
            'supervisors_count' => 'nullable|integer|min:0',
            'visitors_count' => 'nullable|integer|min:0',
            'saved_count' => 'nullable|integer|min:0',
            'planned_baptism_count' => 'nullable|integer|min:0',
            'baptized_count' => 'nullable|integer|min:0',
            'cell_multiplications_count' => 'nullable|integer|min:0',
            'disciplined_leaders_count' => 'nullable|integer|min:0',
            'closed_cells_count' => 'nullable|integer|min:0',
            'ministerial_observations' => 'nullable|string',
            'events' => 'nullable|array',
            'events.*.event_type_id' => 'required|exists:event_types,id',
            'events.*.count' => 'required|integer|min:0',
            'events.*.description' => 'nullable|string',
        ]);

        $exists = QuarterlyReport::where('supervision_id', $validated['supervision_id'])
            ->where('year', $validated['year'])
            ->where('quarter', $validated['quarter'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['quarter' => 'Já existe um relatório para esta supervisão, ano e trimestre.'])->withInput();
        }

        $supervision = Supervision::find($validated['supervision_id']);
        $zone = Zone::find($validated['zone_id']);
        $supervisorId = $supervision->supervisor_id
            ?? $zone->pastor_id
            ?? optional(User::where('role', 'admin')->first())->id;

        if (!$supervisorId) {
            return back()->withErrors(['supervision_id' => 'Supervisor não encontrado para esta supervisão.'])->withInput();
        }

        $defaults = [
            'leaders_count' => 0,
            'cells_count' => 0,
            'timoteos_count' => 0,
            'members_count' => 0,
            'participants_count' => 0,
            'pastors_count' => 0,
            'supervisors_count' => 0,
            'visitors_count' => 0,
            'saved_count' => 0,
            'planned_baptism_count' => 0,
            'baptized_count' => 0,
            'cell_multiplications_count' => 0,
            'disciplined_leaders_count' => 0,
            'closed_cells_count' => 0,
        ];

        DB::transaction(function () use ($validated, $defaults, $supervisorId, $zone) {
            $data = array_merge($defaults, $validated, [
                'supervisor_id' => $supervisorId,
                'zone_pastor_id' => $zone->pastor_id,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $report = QuarterlyReport::create($data);

            if (!empty($validated['events'])) {
                foreach ($validated['events'] as $eventData) {
                    if ($eventData['count'] > 0) {
                        $report->events()->create($eventData);
                    }
                }
            }
        });

        return redirect()->route('public.reports.quarterly')
            ->with('success', 'Relatório enviado com sucesso! Obrigado.');
    }
}

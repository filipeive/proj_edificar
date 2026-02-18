<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CoupleEnrollment;
use App\Models\QuarterlyReport;
use App\Models\Setting;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicFormController extends Controller
{
    public function showPreMaritalForm()
    {
        $courseId = Setting::get('pre_marital_course_id');

        $course = null;
        if ($courseId) {
            $course = Course::find($courseId);
        }

        // Fallback for backward compatibility if not configured
        if (!$course) {
            $course = Course::where('slug', 'like', '%nupcial%')
                ->orWhere('slug', 'like', '%casais%')
                ->orWhere('name', 'like', '%casais%')
                ->first();
        }

        if (!$course) {
            return redirect()->route('welcome')->with('error', 'Curso de casais não encontrado. Entre em contato com a secretaria.');
        }

        $zones = Zone::orderBy('name')->get();

        // Fetch active classes for this course, grouped by year
        $classes = CourseClass::where('course_id', $course->id)
            ->whereIn('status', ['active', 'open', 'planned'])
            ->orderByDesc('start_date')
            ->get()
            ->groupBy(fn($c) => $c->start_date ? $c->start_date->format('Y') : 'Sem ano');

        // Check if registration is open
        if (!$course->isRegistrationOpen()) {
            return back()->with('error', 'As inscrições para este curso estão encerradas no momento.');
        }

        return view('public.courses.pre-marital', compact('course', 'zones', 'classes'));
    }
    public function storePreMarital(Request $request)
    {
        // Pre-handle zone_id: if 'other', set to null to avoid exists:zones,id validation failure
        $zoneIdRaw = $request->zone_id;
        if ($zoneIdRaw === 'other') {
            $request->merge(['zone_id' => null]);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if (!$course->isRegistrationOpen()) {
            return back()->with('error', 'As inscrições para este curso estão encerradas.')->withInput();
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'couple_name' => 'required|string|max:255',
            'relationship_type' => 'required|in:namoro,noivos,vivendo_maritalmente,casados',
            'address' => 'required|string|max:255',
            'wife_address' => 'nullable|string|max:255',
            'husband_phone' => 'nullable|string|max:30',
            'wife_phone' => 'nullable|string|max:30',
            'cell_zone' => 'nullable|string|max:255',
            'zone_id' => 'nullable|exists:zones,id',
            'years_together_text' => 'nullable|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'has_pastoral_recommendation' => 'required|boolean',
            'is_church_member' => 'required|boolean',
            'observations' => 'nullable|string',
            'course_class_id' => 'nullable|exists:course_classes,id',
        ]);

        // At least one phone is required
        if (empty($validated['husband_phone']) && empty($validated['wife_phone'])) {
            return back()->withErrors(['husband_phone' => 'Preencha pelo menos um contacto (parceiro ou parceira).'])->withInput();
        }

        $parts = preg_split('/\s*(?:e|&|\/)\s*/i', $validated['couple_name'], 2);
        $husbandName = trim($parts[0] ?? $validated['couple_name']);
        $wifeName = trim($parts[1] ?? '');
        if ($wifeName === '') {
            $wifeName = 'Não informado';
        }

        $yearsRaw = $validated['years_together_text'] ?? '';
        $years = 0;
        if ($yearsRaw !== '') {
            if (preg_match('/(\d+)/', $yearsRaw, $match)) {
                $num = (int) $match[1];
                if (preg_match('/mes/i', $yearsRaw)) {
                    $years = 0;
                } else {
                    $years = $num;
                }
            }
        }

        // Build contacts string from phone fields for backward compat
        $contacts = collect([
            $validated['husband_phone'] ? 'Ele: ' . $validated['husband_phone'] : null,
            $validated['wife_phone'] ? 'Ela: ' . $validated['wife_phone'] : null,
        ])->filter()->implode(' | ');

        $observations = $validated['observations'] ?? null;
        if ($yearsRaw !== '' && $years === 0) {
            $observations = trim(($observations ? $observations . ' | ' : '') . 'Tempo informado: ' . $yearsRaw);
        }
        if (!empty($validated['zone_id'])) {
            $zone = Zone::find($validated['zone_id']);
            if ($zone) {
                $observations = trim(($observations ? $observations . ' | ' : '') . 'Zona: ' . $zone->name);
            }
        }

        $enrollmentData = [
            'course_id' => $validated['course_id'],
            'husband_name' => $husbandName,
            'wife_name' => $wifeName,
            'relationship_type' => $validated['relationship_type'],
            'address' => $validated['address'],
            'wife_address' => $validated['wife_address'] ?? null,
            'contacts' => $contacts,
            'husband_phone' => $validated['husband_phone'] ?? null,
            'wife_phone' => $validated['wife_phone'] ?? null,
            'cell_zone' => $validated['cell_zone'] ?? null,
            'years_together' => $years,
            'leader_name' => $validated['leader_name'] ?? null,
            'has_pastoral_recommendation' => $validated['has_pastoral_recommendation'],
            'is_church_member' => $validated['is_church_member'],
            'observations' => $observations,
            'status' => 'pending',
        ];

        if (!empty($validated['course_class_id'])) {
            $enrollmentData['course_class_id'] = $validated['course_class_id'];
        }

        CoupleEnrollment::create($enrollmentData);

        return redirect()->route('public.forms.pre-marital')
            ->with('success', 'Inscrição enviada com sucesso! Entraremos em contacto em breve.');
    }

    public function showQuarterlyReportForm()
    {
        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::orderBy('name')->get();
        $eventTypes = \App\Models\EventType::where('is_active', true)->get();
        return view('public.reports.quarterly', compact('zones', 'supervisions', 'eventTypes'));
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

    public function showMinisterialForm($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $ministerialCourseIds = Setting::get('ministerial_public_courses', []);

        if (!is_array($ministerialCourseIds) || !in_array($course->id, $ministerialCourseIds)) {
            return redirect()->route('welcome')->with('error', 'Inscrições não disponíveis para este curso.');
        }

        if (!$course->isRegistrationOpen()) {
            return back()->with('error', 'As inscrições para este curso estão encerradas no momento.');
        }

        $zones = Zone::orderBy('name')->get();
        // Fetch active classes for this course
        $classes = CourseClass::where('course_id', $course->id)
            ->whereIn('status', ['active', 'open', 'planned'])
            ->orderByDesc('start_date')
            ->get();

        return view('public.courses.ministerial-form', compact('course', 'zones', 'classes'));
    }

    public function storeMinisterialForm(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:30',
            'is_church_member' => 'required|boolean',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_name' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'course_class_id' => 'nullable|exists:course_classes,id',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if (!$course->isRegistrationOpen()) {
            return back()->with('error', 'As inscrições para este curso estão encerradas.')->withInput();
        }

        \App\Models\MinisterialEnrollment::create([
            'course_id' => $validated['course_id'],
            'course_class_id' => $validated['course_class_id'] ?? null,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'is_church_member' => $validated['is_church_member'],
            'zone_id' => $validated['zone_id'] ?? null,
            'cell_name' => $validated['cell_name'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('public.forms.ministerial', $course->slug)
            ->with('success', 'Inscrição enviada com sucesso! Entraremos em contacto em breve.');
    }
}

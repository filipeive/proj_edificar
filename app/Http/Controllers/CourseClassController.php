<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseClassMeeting;
use App\Models\CourseClassAttendance;
use App\Models\CourseEnrollment;
use App\Models\CoupleEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CourseClassReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (($user->isSupervisor() || $user->isSecretaria()) && !$user->hasAnyCourseEnrollment()) {
            abort(403, 'Você não está matriculada em nenhuma turma.');
        }

        $courseId = $request->query('course_id');
        $type = $request->query('type');
        $status = $request->query('status');

        $query = CourseClass::with(['course', 'teacherMale', 'teacherFemale', 'assistantMale', 'assistantFemale'])
            ->withCount(['courseEnrollments', 'coupleEnrollments']);

        if ($user->isPastorZona()) {
            $query->whereHas('courseEnrollments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('male_partner_id', $user->id)
                    ->orWhere('female_partner_id', $user->id);
            });
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Grouping logic for the view: get all but organized
        $classes = $query->latest()->get();
        $groupedClasses = $classes->groupBy('course_id');
        $courses = Course::all();

        return view('course_classes.index', compact('groupedClasses', 'courses', 'courseId', 'type', 'status'));
    }

    public function create(Request $request)
    {
        $this->abortIfPastorZonaCannotManage();

        $courses = Course::all();
        $teachers = User::whereIn('role', ['pastor', 'supervisor', 'membro', 'admin', 'secretaria'])->orderBy('name')->get();
        $selectedCourseId = $request->query('course_id');

        return view('course_classes.create', compact('courses', 'teachers', 'selectedCourseId'));
    }

    public function store(Request $request)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:casais_vivendo,pre_nupcial',
            'teacher_male_id' => 'nullable|exists:users,id',
            'teacher_female_id' => 'nullable|exists:users,id',
            'assistant_male_id' => 'nullable|exists:users,id',
            'assistant_female_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'em_andamento';

        $courseClass = CourseClass::create($validated);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Turma criada com sucesso!');
    }

    public function show(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaNotEnrolled($courseClass);

        $courseClass->load([
            'course',
            'teacherMale',
            'teacherFemale',
            'assistantMale',
            'assistantFemale',
            'meetings.attendances',
            'courseEnrollments.user',
            'courseEnrollments.malePartner',
            'courseEnrollments.femalePartner',
            'coupleEnrollments'
        ]);

        // Merge for the "Students" list
        $allStudents = $courseClass->courseEnrollments
            ->concat($courseClass->coupleEnrollments)
            ->sortByDesc('created_at');

        $availableUsers = User::orderBy('name')->get();
        $publicCoupleEnrollments = CoupleEnrollment::where('course_id', $courseClass->course_id)
            ->whereNull('course_class_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('course_classes.show', compact('courseClass', 'allStudents', 'availableUsers', 'publicCoupleEnrollments'));
    }

    public function edit(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $courses = Course::where('is_active', true)->get();
        $teachers = User::whereIn('role', ['pastor', 'supervisor', 'membro', 'admin', 'secretaria', 'pastor_zona'])->get();

        return view('course_classes.edit', compact('courseClass', 'courses', 'teachers'));
    }

    public function update(Request $request, CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:casais_vivendo,pre_nupcial',
            'teacher_male_id' => 'nullable|exists:users,id',
            'teacher_female_id' => 'nullable|exists:users,id',
            'assistant_male_id' => 'nullable|exists:users,id',
            'assistant_female_id' => 'nullable|exists:users,id',
            'status' => 'required|in:em_andamento,concluida,cancelada',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $courseClass->update($validated);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $courseClass->delete();
        return redirect()->route('course-classes.index')
            ->with('success', 'Turma removida com sucesso!');
    }

    public function storeMeeting(Request $request, CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'meeting_number' => 'required|integer',
            'date' => 'required|date',
            'topic' => 'nullable|string|max:255',
        ]);

        $courseClass->meetings()->create($validated);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Encontro agendado com sucesso!');
    }

    public function attendance(CourseClass $courseClass, CourseClassMeeting $meeting)
    {
        $this->abortIfPastorZonaNotEnrolled($courseClass);

        $courseClass->load(['courseEnrollments.user', 'coupleEnrollments']);
        $meeting->load('attendances');

        return view('course_classes.attendance', compact('courseClass', 'meeting'));
    }

    public function storeAttendance(Request $request, CourseClass $courseClass, CourseClassMeeting $meeting)
    {
        $this->abortIfPastorZonaCannotManage();

        $attendances = $request->input('attendance', []);
        $coupleAttendances = $request->input('attendance_couple', []);

        foreach ($attendances as $enrollmentId => $status) {
            CourseClassAttendance::updateOrCreate(
                [
                    'course_class_meeting_id' => $meeting->id,
                    'enrollable_type' => CourseEnrollment::class,
                    'enrollable_id' => $enrollmentId,
                ],
                ['status' => $status]
            );

            // Update attendance counts in enrollment
            $enrollment = CourseEnrollment::find($enrollmentId);
            if ($enrollment) {
                $enrollment->syncAttendanceCounts();
            }
        }

        foreach ($coupleAttendances as $enrollmentId => $status) {
            CourseClassAttendance::updateOrCreate(
                [
                    'course_class_meeting_id' => $meeting->id,
                    'enrollable_type' => CoupleEnrollment::class,
                    'enrollable_id' => $enrollmentId,
                ],
                ['status' => $status]
            );
        }

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Presenças registradas com sucesso!');
    }

    public function assignCoupleEnrollment(Request $request, CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'couple_enrollment_id' => 'required|exists:couple_enrollments,id',
        ]);

        $enrollment = CoupleEnrollment::findOrFail($validated['couple_enrollment_id']);
        if ($enrollment->course_id !== $courseClass->course_id) {
            return back()->with('error', 'Inscrição não pertence a este curso.');
        }

        $enrollment->update([
            'course_class_id' => $courseClass->id,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Inscrição atribuída à turma com sucesso!');
    }

    public function addEnrollment(Request $request, CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'male_partner_id' => 'nullable|exists:users,id',
            'female_partner_id' => 'nullable|exists:users,id',
            'wedding_date' => 'nullable|date',
            'is_church_member' => 'required|boolean',
        ]);

        if (!$validated['male_partner_id'] && !$validated['female_partner_id']) {
            return back()->with('error', 'Selecione pelo menos um parceiro.');
        }

        $enrollment = CourseEnrollment::create([
            'course_id' => $courseClass->course_id,
            'course_class_id' => $courseClass->id,
            'male_partner_id' => $validated['male_partner_id'],
            'female_partner_id' => $validated['female_partner_id'],
            'wedding_date' => $validated['wedding_date'],
            'is_church_member' => $validated['is_church_member'],
            'status' => 'cursando',
        ]);

        if (!empty($validated['wedding_date'])) {
            \App\Models\Wedding::updateOrCreate(
                [
                    'groom_name' => $enrollment->malePartner->name ?? 'N/A',
                    'bride_name' => $enrollment->femalePartner->name ?? 'N/A',
                ],
                [
                    'date' => $validated['wedding_date'],
                    'status' => 'scheduled'
                ]
            );
        }

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Matrícula realizada com sucesso!');
    }

    public function removeEnrollment(Request $request, CourseClass $courseClass)
    {
        $this->abortIfPastorZonaCannotManage();

        $validated = $request->validate([
            'enrollment_id' => 'required|exists:course_enrollments,id',
        ]);

        CourseEnrollment::where('id', $validated['enrollment_id'])->delete();

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Inscrito removido da turma!');
    }

    public function report(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaNotEnrolled($courseClass);

        $courseClass->load(['course', 'meetings.attendances', 'courseEnrollments.malePartner', 'courseEnrollments.femalePartner']);

        $stats = [
            'total_enrolled' => $courseClass->courseEnrollments->count(),
            'started' => $courseClass->courseEnrollments->where('attendance_count', '>', 0)->count(),
            'completed' => $courseClass->courseEnrollments->where('status', 'aprovado')->count(),
            'failed' => $courseClass->courseEnrollments->where('status', 'reprovado')->count(),
            'active' => $courseClass->courseEnrollments->where('status', 'cursando')->count(),
            'average_attendance' => $courseClass->courseEnrollments->avg('attendance_count') ?? 0,
        ];

        return view('course_classes.report', compact('courseClass', 'stats'));
    }

    public function upcomingWeddings()
    {
        $this->abortIfPastorZonaCannotManage();

        $enrollments = CourseEnrollment::whereNotNull('wedding_date')
            ->where('wedding_date', '>=', now())
            ->orderBy('wedding_date')
            ->with(['malePartner', 'femalePartner', 'courseClass.course'])
            ->get();

        return view('course_classes.upcoming_weddings', compact('enrollments'));
    }

    public function exportReport(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaNotEnrolled($courseClass);

        return Excel::download(new CourseClassReportExport($courseClass), 'relatorio_turma_' . $courseClass->id . '.xlsx');
    }

    public function exportPdf(CourseClass $courseClass)
    {
        $this->abortIfPastorZonaNotEnrolled($courseClass);

        $courseClass->load(['course', 'teacherMale', 'teacherFemale', 'courseEnrollments.malePartner', 'courseEnrollments.femalePartner']);

        $pdf = Pdf::loadView('reports.course_class_pdf', compact('courseClass'));

        return $pdf->download('relatorio_turma_' . str_replace(' ', '_', $courseClass->name) . '.pdf');
    }

    public function exportAll(Request $request)
    {
        $this->abortIfPastorZonaCannotManage();

        $classIds = $request->input('class_ids');
        return Excel::download(new \App\Exports\AllClassesExport($classIds), 'relatorio_geral_turmas.xlsx');
    }

    /**
     * Bulk delete course classes
     */
    public function bulkDestroy(Request $request)
    {
        $this->abortIfPastorZonaCannotManage();

        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        $validated = $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:course_classes,id'
        ]);

        $deletedCount = CourseClass::whereIn('id', $validated['class_ids'])->delete();

        return redirect()->route('course-classes.index')
            ->with('success', "{$deletedCount} turma(s) excluída(s) com sucesso!");
    }

    private function abortIfPastorZonaCannotManage(): void
    {
        if (auth()->user()->isPastorZona()) {
            abort(403, 'Pastor de zona não tem permissão para gerir turmas.');
        }
    }

    private function abortIfPastorZonaNotEnrolled(CourseClass $courseClass): void
    {
        $user = auth()->user();
        if ($user->isPastorZona() && !$user->isEnrolledInClass($courseClass->id)) {
            abort(403, 'Você não está matriculado nesta turma.');
        }
    }

    private function checkCompletionStatus(CourseClass $courseClass)
    {
        $meetingsCount = $courseClass->meetings()->count();
        if ($meetingsCount == 0)
            return;

        $courseEnrollments = $courseClass->courseEnrollments;
        foreach ($courseEnrollments as $enrollment) {
            $absences = $enrollment->attendances()->where('status', 'absent')->count();
            if ($absences > 2) {
                $enrollment->update(['status' => 'failed']);
            }
        }

        $coupleEnrollments = $courseClass->coupleEnrollments;
        foreach ($coupleEnrollments as $enrollment) {
            $absences = $enrollment->attendances()->where('status', 'absent')->count();
            if ($absences > 2) {
                $enrollment->update(['status' => 'failed']);
            }
        }
    }


}

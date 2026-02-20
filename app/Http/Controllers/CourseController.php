<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CoupleEnrollment;
use App\Models\CourseEnrollment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GlobalCourseReportExport;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Courses the user is enrolled in
        $enrolledCourses = Course::whereHas('enrollments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->withCount(['enrollments', 'coupleEnrollments'])->get();

        // 2. Available courses for the user
        $availableCourses = Course::whereDoesntHave('enrollments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('is_active', true)
            ->where('registration_open', true)
            ->where(function ($q) use ($user) {
                // Eligible if no role restriction OR if user role matches the target role
                // Admin/Pastor always see everything
                if ($user->isAdmin() || $user->role === 'pastor') {
                    return;
                }
                $q->whereNull('target_role')
                    ->orWhere('target_role', $user->role);
            })
            ->withCount(['enrollments', 'coupleEnrollments'])
            ->get();

        if ($user->isSupervisor()) {
            $preMaritalCourseId = (int) Setting::get('pre_marital_course_id');
            if ($preMaritalCourseId > 0) {
                $availableCourses = $availableCourses->where('id', '!=', $preMaritalCourseId)->values();
            }
        }

        // 3. For admins/pastors, list all courses separately if needed (optional)
        $allCourses = collect();
        if ($user->isAdmin() || $user->role === 'pastor') {
            $allCourses = Course::withCount(['enrollments', 'coupleEnrollments'])->get();
        }

        return view('courses.index', compact('enrolledCourses', 'availableCourses', 'allCourses'));
    }

    public function create()
    {
        $this->ensureCourseManagementAccess();
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $this->ensureCourseManagementAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'target_role' => 'nullable|string|max:255',
            'registration_deadline' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['registration_open'] = true; // Default to open for new courses

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso!');
    }

    public function show(Request $request, Course $course)
    {
        $user = auth()->user();

        $course->load([
            'classes' => function ($q) {
                $q->withCount(['courseEnrollments', 'coupleEnrollments'])->latest();
            }
        ]);

        if ($user->isSupervisor()) {
            $hasEnrollmentInCourse = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$hasEnrollmentInCourse) {
                abort(403, 'Supervisor só pode ver cursos em que está matriculado.');
            }

            $enrolledClassIds = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->whereNotNull('course_class_id')
                ->pluck('course_class_id')
                ->unique();

            $course->setRelation(
                'classes',
                $course->classes->whereIn('id', $enrolledClassIds)->values()
            );
        }

        // Public Inbox (Pending Couple Enrollments)
        $publicCoupleEnrollments = CoupleEnrollment::where('course_id', $course->id)
            ->whereNull('course_class_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $coupleEnrollments = CoupleEnrollment::with(['courseClass'])
            ->where('course_id', $course->id)
            ->whereNotNull('course_class_id')
            ->when($user->isSupervisor(), function ($q) use ($course) {
                $q->whereIn('course_class_id', $course->classes->pluck('id'));
            })
            ->get();

        $statusLabels = [
            'cursando' => 'Cursando',
            'aprovado' => 'Aprovado',
            'reprovado' => 'Reprovado',
            'desistente' => 'Desistente',
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'default' => 'Desconhecido'
        ];

        $statusStyles = [
            'cursando' => 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
            'aprovado' => 'bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'approved' => 'bg-green-50 text-green-600 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'reprovado' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            'rejected' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
            'desistente' => 'bg-gray-50 text-gray-500 border-gray-100 dark:bg-gray-900/50 dark:text-gray-400 dark:border-gray-700',
            'default' => 'bg-gray-50 text-gray-500 border-gray-100'
        ];

        $stats = [
            'total_students' => $course->enrollments()->count() + CoupleEnrollment::where('course_id', $course->id)->whereNotNull('course_class_id')->count(),
            'active_classes' => $user->isSupervisor()
                ? $course->classes->whereIn('status', ['active', 'em_andamento'])->count()
                : $course->classes()->whereIn('status', ['active', 'em_andamento'])->count(),
            'pending_public' => $publicCoupleEnrollments->count(),
        ];

        $courseEnrollments = CourseEnrollment::with(['user', 'courseClass', 'malePartner', 'femalePartner'])
            ->where('course_id', $course->id)
            ->when($user->isSupervisor(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();

        $allCourses = collect();
        if (auth()->user()->isAdmin() || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior') {
            $allCourses = Course::where('id', '!=', $course->id)->orderBy('name')->get();
        }

        return view('courses.show', compact(
            'course',
            'publicCoupleEnrollments',
            'stats',
            'allCourses',
            'courseEnrollments',
            'coupleEnrollments',
            'statusLabels',
            'statusStyles'
        ));
    }

    public function assignPublicEnrollment(Request $request, Course $course)
    {
        $this->ensureCourseManagementAccess();

        $validated = $request->validate([
            'couple_enrollment_id' => 'required|exists:couple_enrollments,id',
            'course_class_id' => 'required|exists:course_classes,id',
        ]);

        $enrollment = CoupleEnrollment::findOrFail($validated['couple_enrollment_id']);
        if ($enrollment->course_id !== $course->id) {
            return back()->with('error', 'Inscrição não pertence a este curso.');
        }

        $courseClass = CourseClass::findOrFail($validated['course_class_id']);
        if ($courseClass->course_id !== $course->id) {
            return back()->with('error', 'Turma não pertence a este curso.');
        }

        $enrollment->update([
            'course_class_id' => $courseClass->id,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Inscrição atribuída à turma com sucesso!');
    }

    public function edit(Course $course)
    {
        $this->ensureCourseManagementAccess();
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->ensureCourseManagementAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'target_role' => 'nullable|string|max:255',
            'registration_deadline' => 'nullable|date',
            'registration_open' => 'boolean',
        ]);

        if ($course->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course)
    {
        $this->ensureCourseManagementAccess();
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso!');
    }

    public function exportGlobalReport(Request $request)
    {
        $this->ensureCourseManagementAccess();
        $classIds = $request->input('class_ids', []);
        return Excel::download(new GlobalCourseReportExport($classIds), 'relatorio_geral_cursos.xlsx');
    }

    /**
     * Bulk delete courses
     */
    public function bulkDestroy(Request $request)
    {
        $this->ensureCourseManagementAccess();

        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        $deletedCount = Course::whereIn('id', $validated['course_ids'])->delete();

        return redirect()->route('courses.index')
            ->with('success', "{$deletedCount} curso(s) excluído(s) com sucesso!");
    }

    private function ensureCourseManagementAccess(): void
    {
        $user = auth()->user();

        if (!$user || (!($user->isAdmin() || $user->role === 'pastor' || $user->role === 'pastor_senior'))) {
            abort(403, 'Sem permissão para gerir cursos.');
        }
    }
}

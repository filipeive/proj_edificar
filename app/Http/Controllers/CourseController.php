<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CoupleEnrollment;
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
        })->withCount('enrollments')->get();

        // 2. Available courses for the user
        $availableCourses = Course::whereDoesntHave('enrollments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('is_active', true)
            ->where('registration_open', true)
            ->where(function ($q) use ($user) {
                // Eligible if no role restriction OR if user role matches the target role
                // Admin/Pastor always see everything
                if ($user->role === 'admin' || $user->role === 'pastor') {
                    return;
                }
                $q->whereNull('target_role')
                    ->orWhere('target_role', $user->role);
            })
            ->withCount('enrollments')
            ->get();

        // 3. For admins/pastors, list all courses separately if needed (optional)
        $allCourses = collect();
        if ($user->role === 'admin' || $user->role === 'pastor') {
            $allCourses = Course::withCount('enrollments')->get();
        }

        return view('courses.index', compact('enrolledCourses', 'availableCourses', 'allCourses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'target_role' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['registration_open'] = true; // Default to open for new courses

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso!');
    }

    public function show(Request $request, Course $course)
    {
        $search = $request->input('search');

        $query = $course->enrollments()->with(['user', 'malePartner', 'femalePartner']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('malePartner', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('femalePartner', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $enrollments = $query->latest()->get();

        $publicCoupleEnrollments = CoupleEnrollment::where('course_id', $course->id)
            ->whereNull('course_class_id')
            ->orderBy('created_at', 'desc')
            ->get();
        $courseClasses = CourseClass::where('course_id', $course->id)->orderBy('name')->get();

        return view('courses.show', compact('course', 'enrollments', 'search', 'publicCoupleEnrollments', 'courseClasses'));
    }

    public function assignPublicEnrollment(Request $request, Course $course)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'pastor'])) {
            abort(403, 'Sem permissão.');
        }

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
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'target_role' => 'nullable|string|max:255',
        ]);

        if ($course->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso!');
    }

    public function exportGlobalReport(Request $request)
    {
        $classIds = $request->input('class_ids', []);
        return Excel::download(new GlobalCourseReportExport($classIds), 'relatorio_geral_cursos.xlsx');
    }

    /**
     * Bulk delete courses
     */
    public function bulkDestroy(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        $deletedCount = Course::whereIn('id', $validated['course_ids'])->delete();

        return redirect()->route('courses.index')
            ->with('success', "{$deletedCount} curso(s) excluído(s) com sucesso!");
    }
}

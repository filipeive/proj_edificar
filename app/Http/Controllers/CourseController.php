<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

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

    public function show(Course $course)
    {
        $course->load(['enrollments.user']);
        return view('courses.show', compact('course'));
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
}

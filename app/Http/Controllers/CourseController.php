<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('enrollments')->get();
        return view('courses.index', compact('courses'));
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

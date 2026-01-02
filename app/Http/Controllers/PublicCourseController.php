<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoupleEnrollment;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function register(Course $course)
    {
        if (!$course->is_active || !$course->registration_open) {
            return redirect()->route('welcome')->with('error', 'Inscrições encerradas para este curso.');
        }

        // Se for o curso de casais, redireciona para o formulário específico (mantendo compatibilidade)
        if (str_contains(strtolower($course->slug), 'casais') || str_contains(strtolower($course->slug), 'nupcial')) {
            return view('public.courses.casais-enrollment', compact('course'));
        }

        return view('public.courses.register', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'observations' => 'nullable|string',
        ]);

        // Check if user exists
        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (!$user) {
            // Create new user
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // Default password
                'role' => 'membro',
                'is_active' => true,
            ]);
        }

        // Check if already enrolled
        $existingEnrollment = \App\Models\CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('welcome')->with('info', 'Você já está inscrito neste curso.');
        }

        // Create enrollment
        \App\Models\CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'pending', // Or 'active' depending on logic
            'enrolled_at' => now(),
        ]);

        return redirect()->route('welcome')->with('success', 'Inscrição realizada com sucesso! Entraremos em contacto em breve.');
    }

    public function showCasaisForm()
    {
        $course = Course::where('slug', 'like', '%casais%')
            ->orWhere('slug', 'like', '%nupcial%')
            ->first();

        if (!$course) {
            return redirect()->route('welcome')->with('error', 'Curso de Casais não encontrado.');
        }

        return view('public.courses.casais-enrollment', compact('course'));
    }

    public function storeCasaisEnrollment(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'husband_name' => 'required|string|max:255',
            'wife_name' => 'required|string|max:255',
            'relationship_type' => 'required|in:namoro,noivos,vivendo_maritalmente,casados',
            'address' => 'required|string|max:255',
            'contacts' => 'required|string|max:255',
            'cell_zone' => 'nullable|string|max:255',
            'years_together' => 'required|integer|min:0',
            'leader_name' => 'nullable|string|max:255',
            'has_pastoral_recommendation' => 'required|boolean',
            'observations' => 'nullable|string',
        ]);

        CoupleEnrollment::create($validated);

        return redirect()->route('welcome')->with('success', 'Inscrição realizada com sucesso! Entraremos em contacto em breve.');
    }
}

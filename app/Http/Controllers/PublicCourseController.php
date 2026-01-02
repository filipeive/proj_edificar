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

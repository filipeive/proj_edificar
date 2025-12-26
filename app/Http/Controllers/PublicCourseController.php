<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoupleEnrollment;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function showCasaisForm()
    {
        $course = Course::where('name', 'like', '%Casais%')->first();

        if (!$course) {
            // Create a default course if it doesn't exist
            $course = Course::create([
                'name' => 'Curso de Casais',
                'description' => 'Fortalecendo os laços matrimoniais e relacionamentos.',
                'is_active' => true
            ]);
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

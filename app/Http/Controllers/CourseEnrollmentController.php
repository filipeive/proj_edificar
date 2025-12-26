<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = auth()->user();

        // Check if already enrolled
        $exists = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Você já está matriculado neste curso.');
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);

        return back()->with('success', 'Matrícula realizada com sucesso!');
    }

    public function updateStatus(Request $request, CourseEnrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:enrolled,completed,dropped',
        ]);

        $data = ['status' => $validated['status']];

        if ($validated['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $enrollment->update($data);

        return back()->with('success', 'Status da matrícula atualizado!');
    }
}

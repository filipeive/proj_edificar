<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseEnrollment;
use App\Models\MinisterialEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MinisterialEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = MinisterialEnrollment::with('course', 'courseClass')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $courses = Course::orderBy('name')->get();

        $stats = [
            'total' => MinisterialEnrollment::count(),
            'pending' => MinisterialEnrollment::whereNull('course_class_id')->count(),
            'assigned' => MinisterialEnrollment::whereNotNull('course_class_id')->count(),
            'members' => MinisterialEnrollment::where('is_church_member', true)->count(),
        ];

        return view('admin.ministerial_enrollments.index', compact('enrollments', 'courses', 'stats'));
    }

    public function exportPdf(Request $request)
    {
        $query = MinisterialEnrollment::with('course', 'courseClass')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $enrollments = $query->get();
        $title = 'Relatório de Inscrições Ministeriais Públicas';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ministerial_enrollments.pdf', compact('enrollments', 'title'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('relatorio_inscricoes_ministeriais_' . date('Ymd_His') . '.pdf');
    }

    public function show(MinisterialEnrollment $ministerialEnrollment)
    {
        return view('admin.ministerial_enrollments.show', compact('ministerialEnrollment'));
    }

    public function edit(MinisterialEnrollment $ministerialEnrollment)
    {
        $courses = Course::orderBy('name')->get();
        return view('admin.ministerial_enrollments.edit', compact('ministerialEnrollment', 'courses'));
    }

    public function update(Request $request, MinisterialEnrollment $ministerialEnrollment)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'is_church_member' => 'required|boolean',
            'cell_name' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $ministerialEnrollment->update($validated);

        return redirect()->route('ministerial-enrollments.index')
            ->with('success', 'Inscrição atualizada com sucesso!');
    }

    public function destroy(MinisterialEnrollment $ministerialEnrollment)
    {
        $ministerialEnrollment->delete();
        return back()->with('success', 'Inscrição removida com sucesso!');
    }

    public function convertToUser(Request $request, MinisterialEnrollment $enrollment)
    {
        // 1. Check if user already exists
        $user = User::where('email', $enrollment->email)->first();

        if (!$user) {
            // Create user
            $tempPassword = Str::random(8);
            $user = User::create([
                'name' => $enrollment->full_name,
                'email' => $enrollment->email,
                'password' => Hash::make($tempPassword),
                'role' => 'user',
            ]);

            // Note: In a real app, send email here
            session()->flash('info', "Usuário criado com sucesso! Senha temporária: {$tempPassword}");
        }

        // 2. Assign to class if requested
        if ($request->course_class_id) {
            $class = CourseClass::findOrFail($request->course_class_id);

            // Check if already enrolled
            $exists = CourseEnrollment::where('course_class_id', $class->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                CourseEnrollment::create([
                    'course_id' => $class->course_id,
                    'course_class_id' => $class->id,
                    'user_id' => $user->id,
                    'status' => 'cursando',
                    'enrollment_date' => now(),
                ]);
            }

            // Mark public enrollment as processed (by assigning the class_id)
            $enrollment->update(['course_class_id' => $class->id, 'status' => 'enrolled']);

            return redirect()->route('course-classes.show', $class)
                ->with('success', "Aluno {$user->name} matriculado com sucesso na turma!");
        }

        return redirect()->route('ministerial-enrollments.show', $enrollment)
            ->with('success', 'Usuário vinculado/criado com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function enroll(Course $course, \App\Actions\Events\EnrollMemberAction $action)
    {
        try {
            $action->execute(auth()->user(), $course);
            return back()->with('success', 'Matrícula realizada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(CourseEnrollment $courseEnrollment)
    {
        $courseEnrollment->load(['course', 'courseClass.course', 'malePartner', 'femalePartner', 'user', 'attendances.meeting']);
        return view('course_enrollments.show', ['enrollment' => $courseEnrollment]);
    }

    public function edit(CourseEnrollment $courseEnrollment)
    {
        $this->abortIfSupervisorCannotManage();

        $courseEnrollment->load(['course', 'courseClass', 'malePartner', 'femalePartner', 'user']);
        $users = User::orderBy('name')->get();
        // Get classes for the same course to allow reassignment/initial assignment
        $classes = \App\Models\CourseClass::where('course_id', $courseEnrollment->course_id)
            ->where('status', '!=', 'cancelada')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('course_enrollments.edit', [
            'enrollment' => $courseEnrollment,
            'users' => $users,
            'classes' => $classes
        ]);
    }

    public function update(Request $request, CourseEnrollment $courseEnrollment)
    {
        $this->abortIfSupervisorCannotManage();

        $validated = $request->validate([
            'course_class_id' => 'nullable|exists:course_classes,id',
            'status' => 'required|in:cursando,aprovado,reprovado,desistente',
            'male_partner_name' => 'nullable|string|max:255',
            'female_partner_name' => 'nullable|string|max:255',
            'wedding_date' => 'nullable|date',
            'engagement_date' => 'nullable|date',
            'is_church_member' => 'required|boolean',
            'attendance_count' => 'required|integer|min:0',
            'absence_count' => 'required|integer|min:0',
            'absence_reasons' => 'nullable|string',
            'godparents_male' => 'nullable|string',
            'godparents_female' => 'nullable|string',
            'completed_pillars' => 'nullable|array',
            'recommendation' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Update partner names if provided
        if (isset($validated['male_partner_name']) && $courseEnrollment->malePartner) {
            $courseEnrollment->malePartner->update(['name' => $validated['male_partner_name']]);
        }
        if (isset($validated['female_partner_name']) && $courseEnrollment->femalePartner) {
            $courseEnrollment->femalePartner->update(['name' => $validated['female_partner_name']]);
        }

        // Integrate with Weddings table if wedding date is new or updated
        if (!empty($validated['wedding_date']) && $validated['wedding_date'] != optional($courseEnrollment->wedding_date)->format('Y-m-d')) {
            \App\Models\Wedding::updateOrCreate(
                [
                    'groom_name' => $courseEnrollment->malePartner->name ?? 'N/A',
                    'bride_name' => $courseEnrollment->femalePartner->name ?? 'N/A',
                ],
                [
                    'date' => $validated['wedding_date'],
                    'godparents' => ($validated['godparents_male'] ?? '') . ' & ' . ($validated['godparents_female'] ?? ''),
                    'status' => 'scheduled'
                ]
            );
        }

        $courseEnrollment->update($validated);

        if ($courseEnrollment->course_class_id) {
            return redirect()->route('course-classes.show', $courseEnrollment->course_class_id)
                ->with('success', 'Matrícula atualizada com sucesso!');
        }

        return redirect()->route('courses.index')->with('success', 'Matrícula atualizada com sucesso!');
    }

    public function destroy(CourseEnrollment $courseEnrollment)
    {
        $this->abortIfSupervisorCannotManage();

        $courseClassId = $courseEnrollment->course_class_id;
        $courseEnrollment->delete();

        if ($courseClassId) {
            return redirect()->route('course-classes.show', $courseClassId)
                ->with('success', 'Matrícula removida com sucesso!');
        }

        return redirect()->route('courses.index')->with('success', 'Matrícula removida com sucesso!');
    }

    public function updateStatus(Request $request, CourseEnrollment $courseEnrollment)
    {
        $this->abortIfSupervisorCannotManage();

        $validated = $request->validate([
            'status' => 'required|in:cursando,aprovado,reprovado,desistente',
        ]);

        $courseEnrollment->update($validated);

        return back()->with('success', 'Status da matrícula atualizado!');
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin() && auth()->user()->role !== 'pastor') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:course_enrollments,id'
        ]);

        $deletedCount = CourseEnrollment::whereIn('id', $validated['enrollment_ids'])->delete();

        return redirect()->back()->with('success', "{$deletedCount} matrícula(s) removida(s) com sucesso!");
    }

    public function assignClass(Request $request, CourseEnrollment $courseEnrollment)
    {
        if (!auth()->user()->isAdmin() && auth()->user()->role !== 'pastor' && auth()->user()->role !== 'secretaria') {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
        ]);

        $courseClass = \App\Models\CourseClass::findOrFail($validated['course_class_id']);

        if ($courseClass->course_id !== $courseEnrollment->course_id) {
            return back()->with('error', 'Esta turma não pertence ao curso da matrícula.');
        }

        $courseEnrollment->update([
            'course_class_id' => $courseClass->id,
            'status' => 'cursando', // Move to 'cursando' when assigned to a class
        ]);

        return back()->with('success', 'Aluno atribuído à turma com sucesso!');
    }

    private function abortIfSupervisorCannotManage(): void
    {
        if (auth()->user()->isSupervisor()) {
            abort(403, 'Supervisor tem acesso apenas para visualização e autoinscrição.');
        }
    }
}

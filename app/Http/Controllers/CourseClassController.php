<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseClassMeeting;
use App\Models\CourseClassAttendance;
use App\Models\CourseEnrollment;
use App\Models\CoupleEnrollment;
use App\Models\User;
use Illuminate\Http\Request;

class CourseClassController extends Controller
{
    public function index(Request $request)
    {
        $courseId = $request->query('course_id');
        $query = CourseClass::with(['course', 'leaderHusband', 'leaderWife']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $classes = $query->latest()->paginate(10);
        $courses = Course::where('is_active', true)->get();

        return view('course_classes.index', compact('classes', 'courses'));
    }

    public function create(Request $request)
    {
        $courses = Course::where('is_active', true)->get();
        $leaders = User::whereIn('role', ['pastor', 'supervisor', 'membro'])->get();
        $selectedCourseId = $request->query('course_id');

        return view('course_classes.create', compact('courses', 'leaders', 'selectedCourseId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'leader_husband_id' => 'nullable|exists:users,id',
            'leader_wife_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        CourseClass::create($validated);

        return redirect()->route('course-classes.index', ['course_id' => $request->course_id])
            ->with('success', 'Turma criada com sucesso!');
    }

    public function show(CourseClass $courseClass)
    {
        $courseClass->load(['course', 'leaderHusband', 'leaderWife', 'meetings.attendances', 'courseEnrollments.user', 'coupleEnrollments']);

        // Available enrollments for this course that are not yet assigned to a class
        $availableCourseEnrollments = CourseEnrollment::where('course_id', $courseClass->course_id)
            ->whereNull('course_class_id')
            ->with('user')
            ->get();

        $availableCoupleEnrollments = CoupleEnrollment::where('course_id', $courseClass->course_id)
            ->whereNull('course_class_id')
            ->get();

        return view('course_classes.show', compact('courseClass', 'availableCourseEnrollments', 'availableCoupleEnrollments'));
    }

    public function edit(CourseClass $courseClass)
    {
        $courses = Course::where('is_active', true)->get();
        $leaders = User::whereIn('role', ['pastor', 'supervisor', 'membro'])->get();

        return view('course_classes.edit', compact('courseClass', 'courses', 'leaders'));
    }

    public function update(Request $request, CourseClass $courseClass)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'leader_husband_id' => 'nullable|exists:users,id',
            'leader_wife_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $courseClass->update($validated);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(CourseClass $courseClass)
    {
        $courseClass->delete();
        return redirect()->route('course-classes.index')
            ->with('success', 'Turma removida com sucesso!');
    }

    public function storeMeeting(Request $request, CourseClass $courseClass)
    {
        $validated = $request->validate([
            'meeting_number' => 'required|integer',
            'date' => 'required|date',
            'topic' => 'nullable|string|max:255',
        ]);

        $courseClass->meetings()->create($validated);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Encontro agendado com sucesso!');
    }

    public function attendance(CourseClass $courseClass, CourseClassMeeting $meeting)
    {
        $courseClass->load(['courseEnrollments.user', 'coupleEnrollments']);
        $meeting->load('attendances');

        return view('course_classes.attendance', compact('courseClass', 'meeting'));
    }

    public function storeAttendance(Request $request, CourseClass $courseClass, CourseClassMeeting $meeting)
    {
        $attendances = $request->input('attendance', []);

        foreach ($attendances as $key => $status) {
            list($type, $id) = explode(':', $key);

            CourseClassAttendance::updateOrCreate(
                [
                    'course_class_meeting_id' => $meeting->id,
                    'enrollable_type' => $type == 'course' ? CourseEnrollment::class : CoupleEnrollment::class,
                    'enrollable_id' => $id,
                ],
                ['status' => $status]
            );
        }

        // Check for failure logic (more than 2 absences)
        $this->checkCompletionStatus($courseClass);

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Presenças registradas com sucesso!');
    }

    public function addEnrollment(Request $request, CourseClass $courseClass)
    {
        $validated = $request->validate([
            'enrollment_type' => 'required|in:course,couple',
            'enrollment_id' => 'required|integer',
        ]);

        if ($validated['enrollment_type'] == 'course') {
            CourseEnrollment::where('id', $validated['enrollment_id'])->update(['course_class_id' => $courseClass->id]);
        } else {
            CoupleEnrollment::where('id', $validated['enrollment_id'])->update(['course_class_id' => $courseClass->id]);
        }

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Inscrito adicionado à turma!');
    }

    public function removeEnrollment(Request $request, CourseClass $courseClass)
    {
        $validated = $request->validate([
            'enrollment_type' => 'required|in:course,couple',
            'enrollment_id' => 'required|integer',
        ]);

        if ($validated['enrollment_type'] == 'course') {
            CourseEnrollment::where('id', $validated['enrollment_id'])->update(['course_class_id' => null]);
        } else {
            CoupleEnrollment::where('id', $validated['enrollment_id'])->update(['course_class_id' => null]);
        }

        return redirect()->route('course-classes.show', $courseClass)
            ->with('success', 'Inscrito removido da turma!');
    }

    public function report(CourseClass $courseClass)
    {
        $courseClass->load(['course', 'meetings.attendances', 'courseEnrollments.user', 'coupleEnrollments']);

        $stats = [
            'total_enrolled' => $courseClass->enrollments_count,
            'started' => 0,
            'completed' => 0,
            'failed' => 0,
            'active' => 0,
        ];

        foreach ($courseClass->courseEnrollments as $enrollment) {
            $hasAttended = $enrollment->attendances()->where('status', 'present')->exists();
            if ($hasAttended)
                $stats['started']++;

            if ($enrollment->status == 'completed')
                $stats['completed']++;
            elseif ($enrollment->status == 'failed')
                $stats['failed']++;
            else
                $stats['active']++;
        }

        foreach ($courseClass->coupleEnrollments as $enrollment) {
            $hasAttended = $enrollment->attendances()->where('status', 'present')->exists();
            if ($hasAttended)
                $stats['started']++;

            if ($enrollment->status == 'completed')
                $stats['completed']++;
            elseif ($enrollment->status == 'failed')
                $stats['failed']++;
            else
                $stats['active']++;
        }

        return view('course_classes.report', compact('courseClass', 'stats'));
    }

    private function checkCompletionStatus(CourseClass $courseClass)
    {
        $meetingsCount = $courseClass->meetings()->count();
        if ($meetingsCount == 0)
            return;

        $courseEnrollments = $courseClass->courseEnrollments;
        foreach ($courseEnrollments as $enrollment) {
            $absences = $enrollment->attendances()->where('status', 'absent')->count();
            if ($absences > 2) {
                $enrollment->update(['status' => 'failed']);
            }
        }

        $coupleEnrollments = $courseClass->coupleEnrollments;
        foreach ($coupleEnrollments as $enrollment) {
            $absences = $enrollment->attendances()->where('status', 'absent')->count();
            if ($absences > 2) {
                $enrollment->update(['status' => 'failed']);
            }
        }
    }
}

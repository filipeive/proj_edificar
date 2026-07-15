<?php

namespace App\Actions\Events;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Setting;
use App\Models\User;
use Exception;

class EnrollMemberAction
{
    /**
     * Enroll a user in a course/event.
     * 
     * @throws Exception
     */
    public function execute(User $user, Course $course): CourseEnrollment
    {
        // Supervisor restriction
        if ($user->isSupervisor()) {
            $preMaritalCourseId = (int) Setting::get('pre_marital_course_id');
            if ($preMaritalCourseId > 0 && $course->id === $preMaritalCourseId) {
                throw new Exception('Supervisor não pode se inscrever em curso de casais.');
            }
        }

        // Check if already enrolled
        $exists = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            throw new Exception('Você já está matriculado neste curso.');
        }

        // Check if open
        if (!$course->isRegistrationOpen()) {
            throw new Exception('As inscrições para este curso estão encerradas.');
        }

        return CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'cursando',
        ]);
    }
}

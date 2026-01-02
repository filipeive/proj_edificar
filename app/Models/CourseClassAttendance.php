<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClassAttendance extends Model
{
    protected $table = 'course_class_attendance';

    protected $fillable = [
        'course_class_meeting_id',
        'enrollable_type',
        'enrollable_id',
        'status',
        'observations',
    ];

    public function meeting()
    {
        return $this->belongsTo(CourseClassMeeting::class, 'course_class_meeting_id');
    }

    public function enrollable()
    {
        return $this->morphTo();
    }
}

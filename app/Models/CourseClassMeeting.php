<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClassMeeting extends Model
{
    protected $fillable = [
        'course_class_id',
        'meeting_number',
        'date',
        'topic',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function attendances()
    {
        return $this->hasMany(CourseClassAttendance::class);
    }
}

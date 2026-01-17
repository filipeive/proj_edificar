<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'type',
        'teacher_male_id',
        'teacher_female_id',
        'assistant_male_id',
        'assistant_female_id',
        'status',
        'start_date',
        'end_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacherMale()
    {
        return $this->belongsTo(User::class, 'teacher_male_id');
    }

    public function teacherFemale()
    {
        return $this->belongsTo(User::class, 'teacher_female_id');
    }

    public function assistantMale()
    {
        return $this->belongsTo(User::class, 'assistant_male_id');
    }

    public function assistantFemale()
    {
        return $this->belongsTo(User::class, 'assistant_female_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function meetings()
    {
        return $this->hasMany(CourseClassMeeting::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function coupleEnrollments()
    {
        return $this->hasMany(CoupleEnrollment::class);
    }

    public function getEnrollmentsCountAttribute()
    {
        return $this->courseEnrollments()->count() + $this->coupleEnrollments()->count();
    }
}

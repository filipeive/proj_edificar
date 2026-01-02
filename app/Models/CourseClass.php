<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'leader_husband_id',
        'leader_wife_id',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function leaderHusband()
    {
        return $this->belongsTo(User::class, 'leader_husband_id');
    }

    public function leaderWife()
    {
        return $this->belongsTo(User::class, 'leader_wife_id');
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

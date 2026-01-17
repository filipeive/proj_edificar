<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'course_class_id',
        'user_id',
        'male_partner_id',
        'female_partner_id',
        'is_church_member',
        'attendance_count',
        'absence_count',
        'absence_reasons',
        'status',
        'wedding_date',
        'engagement_date',
        'godparents_male',
        'godparents_female',
        'completed_pillars',
        'recommendation',
        'notes',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'engagement_date' => 'date',
        'is_church_member' => 'boolean',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function malePartner()
    {
        return $this->belongsTo(User::class, 'male_partner_id');
    }

    public function femalePartner()
    {
        return $this->belongsTo(User::class, 'female_partner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->morphMany(CourseClassAttendance::class, 'enrollable');
    }

    public function syncAttendanceCounts()
    {
        $this->attendance_count = $this->attendances()->where('status', 'present')->count();
        $this->absence_count = $this->attendances()->where('status', 'absent')->count();
        $this->save();
    }
}

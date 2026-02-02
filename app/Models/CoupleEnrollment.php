<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'husband_name',
        'wife_name',
        'relationship_type',
        'address',
        'contacts',
        'cell_zone',
        'years_together',
        'leader_name',
        'has_pastoral_recommendation',
        'is_church_member',
        'observations',
        'status',
        'course_class_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function attendances()
    {
        return $this->morphMany(CourseClassAttendance::class, 'enrollable');
    }
}

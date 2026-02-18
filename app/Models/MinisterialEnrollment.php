<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinisterialEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'course_class_id',
        'full_name',
        'email',
        'phone',
        'is_church_member',
        'zone_id',
        'cell_name',
        'observations',
        'status',
    ];

    protected $casts = [
        'is_church_member' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}

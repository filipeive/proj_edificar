<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'duration',
        'is_active',
        'registration_open',
        'registration_deadline',
        'target_role',
    ];

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments');
    }

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }

    public function coupleEnrollments()
    {
        return $this->hasMany(CoupleEnrollment::class);
    }

    public function isRegistrationOpen()
    {
        if (!$this->registration_open) {
            return false;
        }

        if ($this->registration_deadline && now()->gt($this->registration_deadline)) {
            return false;
        }

        return true;
    }
}

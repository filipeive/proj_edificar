<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Congregation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'latitude',
        'longitude',
        'is_active',
        'is_primary',
        'has_edificar_project',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'has_edificar_project' => 'boolean',
        'settings' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function hasEdificar(): bool
    {
        return (bool) $this->has_edificar_project;
    }

    public function admins()
    {
        return $this->hasMany(User::class)->whereIn('role', ['admin', 'pastor_senior']);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
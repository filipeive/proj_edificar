<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($eventType) {
            if (empty($eventType->code)) {
                $eventType->code = \Str::slug($eventType->name);
            }
        });
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function quarterlyReportEvents()
    {
        return $this->hasMany(QuarterlyReportEvent::class);
    }
}

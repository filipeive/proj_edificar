<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'service_type',
        'preacher_id',
        'theme',
        'message',
        'observations',
        'adults_count',
        'children_count',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function preacher()
    {
        return $this->belongsTo(User::class, 'preacher_id');
    }

    public function offerings()
    {
        return $this->hasMany(ServiceOffering::class);
    }

    public function getTotalOfferingsAttribute()
    {
        return $this->offerings->sum('amount');
    }
}

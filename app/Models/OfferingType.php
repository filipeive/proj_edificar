<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function serviceOfferings()
    {
        return $this->hasMany(ServiceOffering::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'offering_type_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function offeringType()
    {
        return $this->belongsTo(OfferingType::class);
    }
}

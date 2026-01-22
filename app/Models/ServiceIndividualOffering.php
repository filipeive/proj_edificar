<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceIndividualOffering extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'service_id',
        'member_name',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

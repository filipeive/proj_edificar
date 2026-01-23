<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceZoneParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'zone_id',
        'adults_members',
        'adults_visitors',
        'leaders',
        'supervisors',
        'auxiliary_leaders',
        'zone_pastors',
        'children_members',
        'children_visitors',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function getTotalAttribute()
    {
        return $this->adults_members + $this->adults_visitors + $this->leaders + $this->supervisors + $this->auxiliary_leaders + $this->zone_pastors + $this->children_members + $this->children_visitors;
    }
}

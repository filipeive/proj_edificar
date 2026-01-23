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
        'preacher_name',
        'theme',
        'message',
        'observations',
        'adults_members',
        'adults_visitors',
        'adults_salvations',
        'children_members',
        'children_visitors',
        'children_salvations',
        'special_offerings_total',
    ];

    protected $casts = [
        'date' => 'date',
        'special_offerings_total' => 'decimal:2',
    ];

    public function preacher()
    {
        return $this->belongsTo(User::class, 'preacher_id');
    }

    public function offerings()
    {
        return $this->hasMany(ServiceOffering::class);
    }

    public function tithes()
    {
        return $this->hasMany(ServiceTithe::class);
    }

    public function individualOfferings()
    {
        return $this->hasMany(ServiceIndividualOffering::class);
    }

    public function zoneParticipations()
    {
        return $this->hasMany(ServiceZoneParticipation::class);
    }

    public function getTotalOfferingsAttribute()
    {
        return $this->offerings->sum('amount');
    }

    public function getTotalTithesAttribute()
    {
        return $this->tithes->sum('amount');
    }

    public function getTotalIndividualOfferingsAttribute()
    {
        return $this->individualOfferings->sum('amount');
    }

    public function getTotalFinancialAttribute()
    {
        return $this->total_offerings + $this->total_tithes + $this->total_individual_offerings + $this->special_offerings_total;
    }

    public function getTotalMembersAttribute()
    {
        if ($this->service_type === 'teaching') {
            return $this->zoneParticipations->sum(function ($p) {
                return $p->adults_members + $p->children_members + $p->leaders +
                    $p->auxiliary_leaders + $p->supervisors + $p->zone_pastors;
            });
        }

        return ($this->adults_members ?? 0) + ($this->children_members ?? 0);
    }

    public function getTotalVisitorsAttribute()
    {
        if ($this->service_type === 'teaching') {
            return ($this->adults_visitors ?? 0) + ($this->children_visitors ?? 0) +
                $this->zoneParticipations->sum(function ($p) {
                    return $p->adults_visitors + $p->children_visitors;
                });
        }

        return ($this->adults_visitors ?? 0) + ($this->children_visitors ?? 0);
    }

    public function getTotalParticipationAttribute()
    {
        return $this->total_members + $this->total_visitors + ($this->adults_salvations ?? 0) + ($this->children_salvations ?? 0);
    }
}

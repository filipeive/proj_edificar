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
        // General offerings are those registered in service_offerings that are NOT type 1 (Tithes)
        // plus special_offerings_total and individual offerings
        $baseOfferings = $this->offerings->where('offering_type_id', '!=', 1)->sum('amount');
        return $baseOfferings + ($this->special_offerings_total ?? 0) + $this->total_individual_offerings;
    }

    public function getTotalTithesAttribute()
    {
        // Combined tithes from both the dedicated table and general offerings marked as type 1
        $dedicatedTithes = $this->tithes->sum('amount');
        $offeringTithes = $this->offerings->where('offering_type_id', 1)->sum('amount');
        return $dedicatedTithes + $offeringTithes;
    }

    public function getTotalIndividualOfferingsAttribute()
    {
        return $this->individualOfferings->sum('amount');
    }

    public function getTotalFinancialAttribute()
    {
        return $this->total_offerings + $this->total_tithes;
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

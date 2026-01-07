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

    public function getTotalOfferingsAttribute()
    {
        return $this->offerings->sum('amount');
    }

    public function getTotalTithesAttribute()
    {
        return $this->tithes->sum('amount');
    }

    public function getTotalFinancialAttribute()
    {
        return $this->total_offerings + $this->total_tithes + $this->special_offerings_total;
    }

    public function getTotalParticipationAttribute()
    {
        return $this->adults_members + $this->adults_visitors + $this->adults_salvations +
            $this->children_members + $this->children_visitors + $this->children_salvations;
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitmentPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // RELACIONAMENTOS
    public function commitments()
    {
        return $this->hasMany(UserCommitment::class, 'package_id');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'package_id');
    }

    // ESCOPOS
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // HELPERS
    public function getActiveCommitmentsCount()
    {
        return $this->commitments()
            ->active()
            ->count();
    }

    public function getTotalContributionsThisMonth()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }
    public function userCommitments() {
        return $this->hasMany(UserCommitment::class, 'package_id');
    }

    public function getActiveMembersCount() {
        return $this->userCommitments()
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->count();
    }
}
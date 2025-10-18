<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cell extends Model {
    use HasFactory;

    protected $fillable = ['name', 'supervision_id', 'leader_id', 'member_count'];

    public function supervision() {
        return $this->belongsTo(Supervision::class);
    }

    public function leader() {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members() {
        return $this->hasMany(User::class, 'cell_id');
    }

    public function contributions() {
        return $this->hasMany(Contribution::class);
    }

    public function getTotalContributedThisMonth() {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }

    public function getMembersCount() {
        return $this->members()->where('is_active', true)->count();
    }

    public function getMembersContributedThisMonth() {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        return $this->members()
            ->where('is_active', true)
            ->whereHas('contributions', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('contribution_date', [$monthStart, $monthEnd])
                  ->where('status', 'verificada');
            })
            ->count();
    }
}
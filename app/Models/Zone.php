<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model {
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function supervisions() {
        return $this->hasMany(Supervision::class);
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
}

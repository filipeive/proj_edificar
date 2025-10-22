<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervision extends Model {

    use HasFactory;
    

    protected $fillable = [
        'name',
        'zone_id',
        'supervisor_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // RELACIONAMENTOS
    public function zone() {
        return $this->belongsTo(Zone::class);
    }

    public function cells() {
        return $this->hasMany(Cell::class);
    }

    public function contributions() {
        return $this->hasMany(Contribution::class);
    }

    // HELPERS
    public function getCellsCount()
    {
        return $this->cells()->where('is_active', true)->count();
    }

    public function getTotalMembers()
    {
        return User::whereIn('cell_id', 
            $this->cells()->pluck('id')
        )->where('is_active', true)->count();
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

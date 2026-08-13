<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cell extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'supervision_id', 'leader_id', 'member_count'];

    public const TYPE_MEMBROS = 'membros';
    public const TYPE_LIDERES = 'lideres';
    public const TYPE_SUPERVISORES = 'supervisores';
    public const TYPE_PASTORES_ZONA = 'pastores_zona';
    public const TYPE_PASTORES = 'pastores';

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LIDERES => 'Célula de Líderes',
            self::TYPE_SUPERVISORES => 'Célula de Supervisores',
            self::TYPE_PASTORES_ZONA => 'Célula de Pastores de Zona',
            self::TYPE_PASTORES => 'Célula de Pastores',
            default => 'Célula de Membros',
        };
    }

    public function getTypeBadgeClassesAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LIDERES => 'bg-purple-50 text-purple-600 border-purple-100',
            self::TYPE_SUPERVISORES => 'bg-pink-50 text-pink-600 border-pink-100',
            self::TYPE_PASTORES_ZONA => 'bg-blue-50 text-blue-600 border-blue-100',
            self::TYPE_PASTORES => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }

    public function supervision()
    {
        return $this->belongsTo(Supervision::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->hasMany(User::class, 'cell_id');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function timoteos()
    {
        return $this->hasMany(User::class, 'cell_id')->where('role', 'timoteo');
    }

    public function timoteo()
    {
        return $this->belongsTo(User::class, 'timoteo_id');
    }

    public function meetings()
    {
        return $this->hasMany(CellMeeting::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function discipleships()
    {
        return $this->hasMany(Discipleship::class);
    }

    public function conversions()
    {
        return $this->hasMany(Conversion::class);
    }

    public function getTotalContributedThisMonth()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }

    public function getMembersCount()
    {
        return $this->members()->where('is_active', true)->count();
    }

    public function getMembersContributedThisMonth()
    {
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

    public function getDisplayNameAttribute()
    {
        $zoneName = $this->supervision->zone->name ?? 'Sem Zona';
        return $this->name . ' - ' . $zoneName;
    }
}
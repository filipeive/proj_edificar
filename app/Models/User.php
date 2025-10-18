<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Breeze\Features;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'cell_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relacionamentos
    public function cell() {
        return $this->belongsTo(Cell::class);
    }

    public function contributions() {
        return $this->hasMany(Contribution::class);
    }

    public function commitments() {
        return $this->hasMany(UserCommitment::class);
    }

    public function ledCells() {
        return $this->hasMany(Cell::class, 'leader_id');
    }

    // Helpers
    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isLider() {
        return in_array($this->role, ['lider_celula', 'supervisor', 'pastor_zona']);
    }

    public function isSupervisor() {
        return in_array($this->role, ['supervisor', 'pastor_zona']);
    }

    public function isPastorZona() {
        return $this->role === 'pastor_zona';
    }

    public function getActiveCommitment() {
        return $this->commitments()
            ->where('end_date', null)
            ->orWhere('end_date', '>', now())
            ->first();
    }

    public function getTotalContributedThisMonth() {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19); // 20º dia
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4); // 5º dia

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }
}

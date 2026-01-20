<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Breeze\Features;

class User extends Authenticatable
{
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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relacionamentos
    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function commitments()
    {
        return $this->hasMany(UserCommitment::class);
    }

    public function ledCells()
    {
        return $this->hasMany(Cell::class, 'leader_id');
    }

    public function timoteoCells()
    {
        return $this->hasMany(Cell::class, 'timoteo_id');
    }

    public function supervisedSupervisions()
    {
        return $this->hasMany(Supervision::class, 'supervisor_id');
    }

    public function quarterlyReports()
    {
        return $this->hasMany(QuarterlyReport::class, 'supervisor_id');
    }

    public function preachedServices()
    {
        return $this->hasMany(Service::class, 'preacher_id');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPastorSenior()
    {
        return $this->role === 'pastor_senior';
    }

    public function isPastor()
    {
        return $this->role === 'pastor';
    }

    public function isPastorZona()
    {
        return $this->role === 'pastor_zona';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isLider()
    {
        return $this->role === 'lider_celula';
    }

    public function isTimoteo()
    {
        return $this->role === 'timoteo';
    }

    public function isSecretaria()
    {
        return $this->role === 'secretaria' || $this->role === 'tesouraria';
    }

    public function isTesouraria()
    {
        return $this->role === 'tesouraria' || $this->role === 'secretaria';
    }

    public function isEdificarManager()
    {
        return in_array($this->role, ['admin', 'pastor_senior', 'comissao_obra']);
    }

    public function isComissaoObra()
    {
        return $this->role === 'comissao_obra';
    }

    public function isResponsavelPacote()
    {
        return $this->role === 'responsavel_pacote';
    }

    public function hasRole($role)
    {
        // Admin total
        if ($this->role === 'admin') {
            return true;
        }

        // Secretaria vê coisas de tesouraria
        if ($role === 'tesouraria' && $this->role === 'secretaria') {
            return true;
        }

        // Tesouraria vê coisas de secretaria
        if ($role === 'secretaria' && $this->role === 'tesouraria') {
            return true;
        }

        if ($role === 'edificar_manager' && $this->isEdificarManager()) {
            return true;
        }

        return $this->role === $role;
    }

    public function getZoneId()
    {
        if ($this->role === 'pastor_zona') {
            return Zone::where('pastor_id', $this->id)->first()?->id;
        }

        if ($this->role === 'supervisor') {
            return $this->supervisedSupervisions()->first()?->zone_id;
        }

        return null;
    }

    public function getActiveCommitment()
    {
        return $this->commitments()
            ->where('end_date', null)
            ->orWhere('end_date', '>', now())
            ->first();
    }

    public function getTotalContributedThisMonth()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19); // 20º dia
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4); // 5º dia

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }
}

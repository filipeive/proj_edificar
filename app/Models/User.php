<?php
namespace App\Models;

use App\Models\Concerns\NormalizesMozPhone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Breeze\Features;

class User extends Authenticatable
{
    use HasFactory, Notifiable, NormalizesMozPhone, \App\Models\Concerns\LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'cell_id',
        'is_active',
        'observations',
        'notification_preferences',
        'last_login_at',
        'menu_permissions',
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
            'notification_preferences' => 'array',
            'last_login_at' => 'datetime',
            'menu_permissions' => 'array',
        ];
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $this->normalizeMozPhone($value);
    }

    public static function notificationPreferenceDefaults(): array
    {
        return [
            'contribution_created' => true,
            'contribution_pending_validation' => true,
            'pending_contributions' => true,
            'contribution_verified' => true,
            'contribution_rejected' => true,
            'contribution_verified_manager' => true,
            'contribution_rejected_manager' => true,
            'commitment_chosen' => true,
            'commitment_expiring' => true,
            'member_created' => true,
            'member_added_to_cell' => true,
            'user_promoted' => true,
        ];
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences ?? [];
        if (array_key_exists($type, $prefs)) {
            return (bool) $prefs[$type];
        }

        $defaults = self::notificationPreferenceDefaults();
        return $defaults[$type] ?? true;
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

    public function assignedCell()
    {
        return $this->belongsTo(Cell::class, 'cell_id');
    }

    public function timoteoCells()
    {
        return $this->hasMany(Cell::class, 'timoteo_id');
    }

    public function supervisedSupervisions()
    {
        return $this->hasMany(Supervision::class, 'supervisor_id');
    }

    public function subSupervisedSupervisions()
    {
        return $this->hasMany(Supervision::class, 'sub_supervisor_id');
    }

    public function quarterlyReports()
    {
        return $this->hasMany(QuarterlyReport::class, 'supervisor_id');
    }

    public function preachedServices()
    {
        return $this->hasMany(Service::class, 'preacher_id');
    }

    public function managedPackages()
    {
        return $this->hasMany(CommitmentPackage::class, 'responsible_id');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function courseEnrollmentsAsPartner()
    {
        return $this->hasMany(CourseEnrollment::class, 'male_partner_id')
            ->orWhere('female_partner_id', $this->id);
    }

    public function hasAnyCourseEnrollment()
    {
        return $this->courseEnrollments()->exists() ||
            CourseEnrollment::where('male_partner_id', $this->id)
                ->orWhere('female_partner_id', $this->id)
                ->exists();
    }

    public function isEnrolledInClass($classId)
    {
        return $this->courseEnrollments()->where('course_class_id', $classId)->exists() ||
            CourseEnrollment::where('course_class_id', $classId)
                ->where(function ($q) {
                    $q->where('male_partner_id', $this->id)
                        ->orWhere('female_partner_id', $this->id);
                })->exists();
    }

    public function isEnrolledInCourse($courseId)
    {
        return $this->courseEnrollments()->where('course_id', $courseId)->exists() ||
            CourseEnrollment::where('course_id', $courseId)
                ->where(function ($q) {
                    $q->where('male_partner_id', $this->id)
                        ->orWhere('female_partner_id', $this->id);
                })->exists();
    }

    // ESCOPOS
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
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

    public function isSubSupervisor()
    {
        return $this->role === 'sub_supervisor';
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
        return in_array($this->role, ['admin', 'pastor_senior', 'comissao_obra', 'pastor']);
    }

    public function isComissaoObra()
    {
        return $this->role === 'comissao_obra';
    }

    public function isResponsavelPacote()
    {
        return $this->role === 'responsavel_pacote';
    }

    public function isAdministracao()
    {
        return $this->role === 'administracao';
    }

    public function managesPackage(CommitmentPackage $package)
    {
        return $this->isAdmin() || $package->responsible_id === $this->id;
    }

    public function hasRole($role)
    {
        // Super Admin, Admin e Pastor Senior - acesso total
        if (in_array($this->role, ['super_admin', 'admin', 'pastor_senior'], true)) {
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
        return $this->getManagedZoneIds()->first();
    }

    /**
     * Get IDs of zones managed by this user (if pastor) or containing their managed supervisions (if supervisor)
     */
    public function getManagedZoneIds()
    {
        if ($this->isPastorZona()) {
            $zoneIds = Zone::where('pastor_id', $this->id)->pluck('id');
            if ($zoneIds->isEmpty() && $this->cell && $this->cell->supervision && $this->cell->supervision->zone_id) {
                $zoneIds = collect([$this->cell->supervision->zone_id]);
            }
            return $zoneIds;
        }

        if ($this->isSupervisor() || $this->isSubSupervisor()) {
            return Supervision::whereIn('id', $this->getManagedSupervisionIds())->pluck('zone_id')->unique();
        }

        return collect();
    }

    /**
     * Get IDs of supervisions managed by this user (if supervisor or pastor)
     */
    public function getManagedSupervisionIds()
    {
        if ($this->isPastorZona()) {
            return Supervision::whereIn('zone_id', $this->getManagedZoneIds())->pluck('id');
        }

        if ($this->isSupervisor()) {
            $ids = $this->supervisedSupervisions()->pluck('id');
            // Robust fallback: use cell context if no explicit assignment
            if ($ids->isEmpty() && $this->cell) {
                $ids = collect([$this->cell->supervision_id]);
            }
            return $ids;
        }

        if ($this->isSubSupervisor()) {
            return $this->subSupervisedSupervisions()->pluck('id');
        }

        return collect();
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

    public function hasAvailableCourses()
    {
        return Course::where('is_active', true)
            ->where('registration_open', true)
            ->where(function ($q) {
                if ($this->isAdmin() || $this->isPastor()) {
                    return;
                }
                $q->whereNull('target_role')
                    ->orWhere('target_role', $this->role);
            })->exists();
    }

    /**
     * Check if user has specific menu/feature permission
     */
    public function hasPermission(string $permission): bool
    {
        // Hard deny: responsável de pacote não deve aceder ao inventário
        if ($permission === 'menu_inventory' && $this->isResponsavelPacote()) {
            return false;
        }

        // Admin and Pastor Senior have all permissions
        if ($this->isAdmin() || $this->isPastorSenior()) {
            return true;
        }

        $perms = $this->menu_permissions ?? [];

        // If explicitly defined in custom permissions, return that
        if (array_key_exists($permission, $perms)) {
            return (bool) $perms[$permission];
        }

        // Check for role-based overrides in Settings
        $roleDefaults = Setting::get("permissions.role_{$this->role}", []);
        if (is_array($roleDefaults) && array_key_exists($permission, $roleDefaults)) {
            return (bool) $roleDefaults[$permission];
        }

        // Fallback to hardcoded role-based logic if not defined elsewhere
        return match ($permission) {
            'dashboard_edificar' => $this->isEdificarManager(),
            'dashboard_packages' => $this->isResponsavelPacote(),

            // Operação
            'menu_services' => $this->isAdmin() || $this->isSecretaria() || $this->isPastor() || $this->isAdministracao(),
            'menu_events' => true, // Default for most
            'menu_weddings' => $this->isAdmin() || $this->isSecretaria() || $this->isPastor(),
            'menu_visitors' => $this->isAdmin() || $this->isSecretaria() || $this->isPastorZona() || $this->isSupervisor() || $this->isPastor() || $this->isAdministracao(),
            'menu_courses' => ($this->isAdmin() || $this->isPastor() || $this->isSecretaria() || $this->hasAnyCourseEnrollment() || $this->hasAvailableCourses()) && !$this->isAdministracao(),
            'menu_public_enrollments' => $this->isAdmin() || $this->isPastor() || $this->isSecretaria() || $this->isPastorSenior(),
            'menu_quarterly_reports' => $this->isAdmin() || $this->isPastor() || $this->isPastorZona() || $this->isSupervisor() || $this->isSubSupervisor(),
            'menu_inventory' => $this->isAdmin() || $this->isSecretaria() || $this->isEdificarManager() || $this->isTesouraria(),

            // Células
            'menu_cell_meetings' => $this->isLider() || $this->isTimoteo() || $this->isSupervisor() || $this->isSubSupervisor() || $this->isPastorZona() || $this->isPastor() || $this->isAdmin(),
            'menu_members' => $this->isLider() || $this->isTimoteo() || $this->isSupervisor() || $this->isSubSupervisor() || $this->isPastorZona() || $this->isPastor() || $this->isAdmin() || $this->isSecretaria(),
            'menu_zones' => $this->isAdmin() || $this->isPastorZona() || $this->isPastor(),
            'menu_supervisions' => $this->isAdmin() || $this->isPastorZona() || $this->isPastor(),
            'menu_cells' => $this->isAdmin() || $this->isPastorZona() || $this->isSupervisor() || $this->isSubSupervisor() || $this->isPastor(),

            // Financeira
            'menu_packages' => $this->isEdificarManager() || $this->isResponsavelPacote(),
            'menu_contributions_all' => $this->isEdificarManager() || $this->isResponsavelPacote() || $this->isPastor() || $this->isPastorZona() || $this->isSupervisor() || $this->isSubSupervisor() || $this->isLider(),
            'menu_finance' => $this->isAdmin() || $this->isPastor() || $this->isTesouraria(),

            // Sistema
            'menu_stats' => $this->isAdmin() || $this->isPastor() || $this->isPastorZona() || $this->isSupervisor() || $this->isSubSupervisor() || $this->isComissaoObra(),
            'menu_users' => $this->isAdmin() || $this->isPastorSenior(),
            'menu_settings' => $this->isAdmin() || $this->isPastor(),

            default => false,
        };
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMozPhone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory, NormalizesMozPhone;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'neighborhood',
        'city',
        'phone',
        'invited_by_someone',
        'inviter_name',
        'visit_date',
        'service_id',
        'zone_id',
        'cell_id',
        'contact_status',
        'contacted_at',
        'contacted_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'contacted_at' => 'datetime',
        'invited_by_someone' => 'boolean',
    ];

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $this->normalizeMozPhone($value);
    }

    protected static function booted()
    {
        static::saved(function ($visitor) {
            if (($visitor->wasChanged('cell_id') && $visitor->cell_id) || ($visitor->wasChanged('zone_id') && $visitor->zone_id)) {
                $visitor->notifyAssignment();
            }
        });

        static::created(function ($visitor) {
            if ($visitor->cell_id || $visitor->zone_id) {
                $visitor->notifyAssignment();
            }
        });
    }

    /**
     * Notify supervisor and zone pastor via SMS about new visitor assignment.
     */
    public function notifyAssignment(): void
    {
        try {
            $smsService = app(\App\Services\Sms\SmsService::class);
            
            $cell = $this->cell;
            $zone = $this->zone;
            
            if ($cell) {
                $supervision = $cell->supervision;
                $zone = $supervision ? $supervision->zone : $zone;
                
                // 1. Notify Supervisor
                $supervisor = $supervision ? $supervision->supervisor : null;
                if ($supervisor && $supervisor->phone) {
                    $msgSupervisor = sprintf(
                        "Paz Supervisor, o visitante %s (%s) do bairro %s foi atribuido a celula %s (%s). Encaminhe ao lider de celula e de o feedback no sistema.",
                        $this->name,
                        $this->phone ?? 'Sem telefone',
                        $this->neighborhood ?? 'Sem bairro',
                        $cell->name,
                        $supervision->name ?? ''
                    );
                    $smsService->send($supervisor->phone, $msgSupervisor);
                }
                
                // 2. Notify Pastor de Zona
                $pastor = $zone ? $zone->pastor : null;
                if ($pastor && $pastor->phone) {
                    $msgPastor = sprintf(
                        "Paz Pastor, o visitante %s (%s) do bairro %s foi atribuido a celula %s (Zona: %s). Encaminhe ao supervisor para acompanhamento.",
                        $this->name,
                        $this->phone ?? 'Sem telefone',
                        $this->neighborhood ?? 'Sem bairro',
                        $cell->name,
                        $zone->name ?? ''
                    );
                    $smsService->send($pastor->phone, $msgPastor);
                }
            } elseif ($zone) {
                $pastor = $zone->pastor;
                if ($pastor && $pastor->phone) {
                    $msgPastor = sprintf(
                        "Paz Pastor, o visitante %s (%s) do bairro %s foi registado na sua zona (%s). Encaminhe ao supervisor para atribuicao de celula.",
                        $this->name,
                        $this->phone ?? 'Sem telefone',
                        $this->neighborhood ?? 'Sem bairro',
                        $zone->name
                    );
                    $smsService->send($pastor->phone, $msgPastor);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error notifying leaders about visitor: ' . $e->getMessage());
        }
    }

    /**
     * Compatibility wrapper for old method name.
     */
    public function notifyCellLeaderAboutAssignment(): void
    {
        $this->notifyAssignment();
    }


    /**
     * Relacionamentos
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function cell(): BelongsTo
    {
        return $this->belongsTo(Cell::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('contact_status', 'pendente');
    }

    public function scopeContacted($query)
    {
        return $query->where('contact_status', 'contatado');
    }

    public function scopeIntegrated($query)
    {
        return $query->where('contact_status', 'integrado');
    }

    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visit_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('visit_date', '>=', now()->subDays($days));
    }

    /**
     * Accessors
     */
    public function getFullInfoAttribute(): string
    {
        $info = $this->name;

        if ($this->age) {
            $info .= ", {$this->age} anos";
        }

        if ($this->neighborhood) {
            $info .= " - {$this->neighborhood}";
        }

        return $info;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->contact_status) {
            'pendente' => '<span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold">Pendente</span>',
            'contatado' => '<span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">Contatado</span>',
            'integrado' => '<span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold">Integrado</span>',
            'sem_interesse' => '<span class="px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-bold">Sem Interesse</span>',
            default => '<span class="px-3 py-1 bg-gray-50 text-gray-600 rounded-full text-xs font-bold">Desconhecido</span>',
        };
    }

    /**
     * Métodos auxiliares
     */
    public function isPending(): bool
    {
        return $this->contact_status === 'pendente';
    }

    public function isContacted(): bool
    {
        return $this->contact_status === 'contatado';
    }

    public function isIntegrated(): bool
    {
        return $this->contact_status === 'integrado';
    }

    public function markAsContacted(User $user): void
    {
        $this->update([
            'contact_status' => 'contatado',
            'contacted_at' => now(),
            'contacted_by' => $user->id,
        ]);
    }

    public function markAsIntegrated(): void
    {
        $this->update([
            'contact_status' => 'integrado',
        ]);
    }
}

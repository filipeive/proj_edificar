<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory;

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

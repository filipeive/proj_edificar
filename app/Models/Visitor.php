<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMozPhone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends CongregationModel
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
        static::created(function ($visitor) {
            if ($visitor->cell_id || $visitor->zone_id) {
                $visitor->notifyAssignment();
            }
        });

        static::updated(function ($visitor) {
            if (($visitor->wasChanged('cell_id') && $visitor->cell_id) || ($visitor->wasChanged('zone_id') && $visitor->zone_id)) {
                $visitor->notifyAssignment();
            }
        });
    }

    /**
     * Notify pastoral network (Pastor, Supervisor, Cell Leader, Visitor) via SMS & System Notifications.
     *
     * Rede de acolhimento pastoral inteligente:
     * - Pastor: Informe e cobertura ministerial
     * - Supervisor: Ação de acompanhamento e suporte ao líder
     * - Líder da Célula: Ação imediata de acolhimento, contato e convite
     * - Visitante: Boas-vindas calorosa da igreja e conexão à célula
     */
    public function notifyAssignment(): array
    {
        $results = [
            'pastor' => false,
            'supervisor' => false,
            'leader' => false,
            'visitor' => false,
        ];

        try {
            $smsService = app(\App\Services\Sms\SmsService::class);
            
            $cell = $this->cell;
            $zone = $this->zone;
            $metaBase = [
                'reference_type' => self::class,
                'reference_id'   => $this->id,
                'congregation_id'=> $this->congregation_id,
            ];
            
            if ($cell) {
                $supervision = $cell->supervision;
                $zone = $supervision ? $supervision->zone : $zone;
                $leader = $cell->leader;
                $supervisor = $supervision ? $supervision->supervisor : null;
                $pastor = $zone ? $zone->pastor : null;

                // 1. Notificar Líder de Célula (Ação Imediata de Acolhimento)
                if ($leader && $leader->phone) {
                    $msgLeader = sprintf(
                        "Paz Líder %s! [AÇÃO IMEDIATA]: Novo visitante alocado para a Célula %s: %s (Tel: %s, Bairro: %s). Favor fazer contato de acolhimento e integrá-lo no próximo encontro!",
                        $leader->name,
                        $cell->name,
                        $this->name,
                        $this->phone ?? 'Sem telefone',
                        $this->neighborhood ?? 'Sem bairro'
                    );
                    $results['leader'] = $smsService->send($leader->phone, $msgLeader, array_merge($metaBase, [
                        'recipient_name' => $leader->name,
                        'recipient_type' => 'lider',
                        'recipient_id'   => $leader->id,
                        'context'        => 'visitor_network_leader',
                    ]));

                    try {
                        $leader->notify(new \App\Notifications\VisitorAssignedNotification(
                            $this,
                            'lider',
                            "Ação imediata: Faça contato de acolhimento com {$this->name} ({$this->phone}) na Célula {$cell->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db líder: ' . $e->getMessage());
                    }
                }
                
                // 2. Notificar Supervisor (Ação de Acompanhamento)
                if ($supervisor && $supervisor->phone) {
                    $leaderContact = $leader ? "{$leader->name} ({$leader->phone})" : 'líder da célula';
                    $msgSupervisor = sprintf(
                        "Paz Supervisor %s! [AÇÃO]: O visitante %s (%s, bairro %s) foi alocado à célula %s (%s). Acompanhe o líder %s para acolhimento e feedback.",
                        $supervisor->name,
                        $this->name,
                        $this->phone ?? 'Sem tel',
                        $this->neighborhood ?? 'Sem bairro',
                        $cell->name,
                        $supervision->name ?? '',
                        $leaderContact
                    );
                    $results['supervisor'] = $smsService->send($supervisor->phone, $msgSupervisor, array_merge($metaBase, [
                        'recipient_name' => $supervisor->name,
                        'recipient_type' => 'supervisor',
                        'recipient_id'   => $supervisor->id,
                        'context'        => 'visitor_network_supervisor',
                    ]));

                    try {
                        $supervisor->notify(new \App\Notifications\VisitorAssignedNotification(
                            $this,
                            'supervisor',
                            "Ação de acompanhamento: Visitante {$this->name} alocado à Célula {$cell->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db supervisor: ' . $e->getMessage());
                    }
                }
                
                // 3. Notificar Pastor de Zona (Informe e Cobertura Pastoral)
                if ($pastor && $pastor->phone) {
                    $msgPastor = sprintf(
                        "Paz Pastor %s! [INFORME PASTORAL]: O visitante %s (%s, bairro %s) foi alocado à Célula %s (Zona: %s). Líder e Supervisor foram acionados para acolhimento.",
                        $pastor->name,
                        $this->name,
                        $this->phone ?? 'Sem tel',
                        $this->neighborhood ?? 'Sem bairro',
                        $cell->name,
                        $zone->name ?? ''
                    );
                    $results['pastor'] = $smsService->send($pastor->phone, $msgPastor, array_merge($metaBase, [
                        'recipient_name' => $pastor->name,
                        'recipient_type' => 'pastor',
                        'recipient_id'   => $pastor->id,
                        'context'        => 'visitor_network_pastor',
                    ]));

                    try {
                        $pastor->notify(new \App\Notifications\VisitorAssignedNotification(
                            $this,
                            'pastor',
                            "Informe pastoral: Visitante {$this->name} alocado à Célula {$cell->name} (Zona: {$zone->name})."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db pastor: ' . $e->getMessage());
                    }
                }

                // 4. Notificar o próprio Visitante (Boas-vindas e Conexão de Comunhão)
                if ($this->phone) {
                    $leaderRef = ($leader && $leader->phone) ? " com o Líder {$leader->name} ({$leader->phone})" : "";
                    $msgVisitor = sprintf(
                        "Olá %s! A família Life Church alegra-se com a sua visita! Conectamos você à Célula %s%s. Seja muito bem-vindo(a)!",
                        $this->name,
                        $cell->name,
                        $leaderRef
                    );
                    $results['visitor'] = $smsService->send($this->phone, $msgVisitor, array_merge($metaBase, [
                        'recipient_name' => $this->name,
                        'recipient_type' => 'visitante',
                        'context'        => 'visitor_welcome',
                    ]));
                }
            } elseif ($zone) {
                $pastor = $zone->pastor;
                if ($pastor && $pastor->phone) {
                    $msgPastor = sprintf(
                        "Paz Pastor %s! [NOVO VISITANTE]: %s (%s, bairro %s) foi registrado na sua Zona (%s). Favor encaminhar ao supervisor para atribuição de célula.",
                        $pastor->name,
                        $this->name,
                        $this->phone ?? 'Sem telefone',
                        $this->neighborhood ?? 'Sem bairro',
                        $zone->name
                    );
                    $results['pastor'] = $smsService->send($pastor->phone, $msgPastor, array_merge($metaBase, [
                        'recipient_name' => $pastor->name,
                        'recipient_type' => 'pastor',
                        'recipient_id'   => $pastor->id,
                        'context'        => 'visitor_network_pastor',
                    ]));

                    try {
                        $pastor->notify(new \App\Notifications\VisitorAssignedNotification(
                            $this,
                            'pastor',
                            "Novo visitante na Zona {$zone->name}: {$this->name} ({$this->phone})."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db pastor: ' . $e->getMessage());
                    }
                }

                // Boas-vindas simples ao visitante se tiver telefone
                if ($this->phone) {
                    $msgVisitor = sprintf(
                        "Olá %s! Agradecemos pela sua visita à Life Church! Em breve entraremos em contato para acolher e orar por si. Deus abençoe!",
                        $this->name
                    );
                    $results['visitor'] = $smsService->send($this->phone, $msgVisitor, array_merge($metaBase, [
                        'recipient_name' => $this->name,
                        'recipient_type' => 'visitante',
                        'context'        => 'visitor_welcome',
                    ]));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao notificar rede de líderes sobre visitante: ' . $e->getMessage());
        }

        return $results;
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

    public function smsMessages()
    {
        return $this->morphMany(SmsMessage::class, 'reference')->latest();
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
        return view('components.visitor-status-badge', [
            'status' => $this->contact_status,
        ])->render();
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

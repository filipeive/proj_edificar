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
     * Retorna o primeiro nome (ou nome curto amigável).
     */
    public function getFirstName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        $parts = preg_split('/\s+/', trim($name));
        return $parts[0] ?? '';
    }

    /**
     * Retorna uma descrição textual fluida de contato e bairro sem parênteses robóticos.
     * Ex: ", tel. 841234567, bairro Acordo de Lusaka" ou ", bairro Acordo de Lusaka" ou ""
     */
    public function getFormattedContactDetails(): string
    {
        $parts = [];
        if (!empty($this->phone)) {
            $parts[] = 'tel. ' . $this->phone;
        }
        if (!empty($this->neighborhood)) {
            $parts[] = 'bairro ' . $this->neighborhood;
        }

        if (empty($parts)) {
            return '';
        }

        return ', ' . implode(', ', $parts);
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

                $leaderFirstName = $leader ? $this->getFirstName($leader->name) : 'Líder';
                $supervisorFirstName = $supervisor ? $this->getFirstName($supervisor->name) : 'Supervisor';
                $pastorFirstName = $pastor ? $this->getFirstName($pastor->name) : 'Pastor';
                $contactSummary = $this->getFormattedContactDetails();

                // 1. Notificar Líder de Célula (Ação Imediata de Acolhimento)
                if ($leader && $leader->phone) {
                    $defaultTplLeader = "Paz, Líder {lider}. Temos um novo visitante direcionado para a sua Célula {celula}: {visitante}{contato}. Por favor, faça contato de acolhimento e convide-o para o próximo encontro da célula.";
                    $tplLeader = Setting::get('sms.template.visitor_leader', $defaultTplLeader);
                    $msgLeader = str_replace(
                        ['{lider}', '{lider_completo}', '{celula}', '{visitante}', '{contato}', '{telefone}', '{bairro}', '{supervisao}', '{zona}'],
                        [$leaderFirstName, $leader->name, $cell->name, $this->name, $contactSummary, $this->phone ?? '', $this->neighborhood ?? '', $supervision->name ?? '', $zone->name ?? ''],
                        $tplLeader
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
                            "Ação de acolhimento: Faça contato com {$this->name} na Célula {$cell->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db líder: ' . $e->getMessage());
                    }
                }
                
                // 2. Notificar Supervisor (Ação de Acompanhamento)
                if ($supervisor && $supervisor->phone) {
                    $leaderRef = $leader ? ($leader->phone ? "líder {$leaderFirstName}, tel. {$leader->phone}" : "líder {$leaderFirstName}") : 'líder da célula';
                    $defaultTplSupervisor = "Paz, Supervisor {supervisor}. O visitante {visitante}{contato} foi direcionado para a Célula {celula}. Solicitamos o seu acompanhamento junto ao {lider_info} para assegurar a integração.";
                    $tplSupervisor = Setting::get('sms.template.visitor_supervisor', $defaultTplSupervisor);
                    $msgSupervisor = str_replace(
                        ['{supervisor}', '{supervisor_completo}', '{visitante}', '{contato}', '{celula}', '{supervisao}', '{lider}', '{lider_info}', '{lider_telefone}', '{zona}'],
                        [$supervisorFirstName, $supervisor->name, $this->name, $contactSummary, $cell->name, $supervision->name ?? '', $leaderFirstName, $leaderRef, $leader?->phone ?? '', $zone->name ?? ''],
                        $tplSupervisor
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
                            "Acompanhamento: Visitante {$this->name} alocado à Célula {$cell->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db supervisor: ' . $e->getMessage());
                    }
                }
                
                // 3. Notificar Pastor de Zona (Informe e Cobertura Pastoral)
                if ($pastor && $pastor->phone) {
                    $defaultTplPastor = "Paz, Pastor {pastor}. Informamos que o visitante {visitante}{contato} foi acolhido e alocado à Célula {celula}, na Zona {zona}. O líder e o supervisor já foram acionados para o acolhimento.";
                    $tplPastor = Setting::get('sms.template.visitor_pastor', $defaultTplPastor);
                    $msgPastor = str_replace(
                        ['{pastor}', '{pastor_completo}', '{visitante}', '{contato}', '{celula}', '{supervisao}', '{zona}'],
                        [$pastorFirstName, $pastor->name, $this->name, $contactSummary, $cell->name, $supervision->name ?? '', $zone->name ?? ''],
                        $tplPastor
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
                            "Informe pastoral: Visitante {$this->name} alocado à Célula {$cell->name} na Zona {$zone->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db pastor: ' . $e->getMessage());
                    }
                }

                // 4. Notificar o próprio Visitante (Boas-vindas e Conexão de Comunhão)
                if ($this->phone) {
                    $leaderRef = ($leader && $leader->phone) ? ", com o Líder {$leaderFirstName}, tel. {$leader->phone}" : ($leader ? ", com o Líder {$leaderFirstName}" : "");
                    $defaultTplWelcome = "Paz e graça, {visitante}! Foi uma alegria receber você na Life Church. Conectamos você à nossa Célula {celula}{lider_info}. Seja muito bem-vindo à nossa família!";
                    $tplWelcome = Setting::get('sms.template.visitor_welcome_cell', $defaultTplWelcome);
                    $msgVisitor = str_replace(
                        ['{visitante}', '{visitante_primeiro_nome}', '{celula}', '{lider_info}', '{lider}', '{lider_telefone}'],
                        [$this->name, $this->getFirstName($this->name), $cell->name, $leaderRef, $leaderFirstName, $leader?->phone ?? ''],
                        $tplWelcome
                    );
                    $results['visitor'] = $smsService->send($this->phone, $msgVisitor, array_merge($metaBase, [
                        'recipient_name' => $this->name,
                        'recipient_type' => 'visitante',
                        'context'        => 'visitor_welcome',
                    ]));
                }
            } elseif ($zone) {
                $pastor = $zone->pastor;
                $pastorFirstName = $pastor ? $this->getFirstName($pastor->name) : 'Pastor';
                $contactSummary = $this->getFormattedContactDetails();

                if ($pastor && $pastor->phone) {
                    $defaultTplZonePastor = "Paz, Pastor {pastor}. Um novo visitante foi registrado na Zona {zona}: {visitante}{contato}. Favor orientar o supervisor para o devido acolhimento e encaminhamento à célula.";
                    $tplZonePastor = Setting::get('sms.template.visitor_zone_pastor', $defaultTplZonePastor);
                    $msgPastor = str_replace(
                        ['{pastor}', '{pastor_completo}', '{visitante}', '{contato}', '{telefone}', '{bairro}', '{zona}'],
                        [$pastorFirstName, $pastor->name, $this->name, $contactSummary, $this->phone ?? '', $this->neighborhood ?? '', $zone->name],
                        $tplZonePastor
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
                            "Novo visitante na Zona {$zone->name}: {$this->name}."
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Erro notificação db pastor: ' . $e->getMessage());
                    }
                }

                // Boas-vindas simples ao visitante se tiver telefone
                if ($this->phone) {
                    $defaultTplZoneWelcome = "Paz e graça, {visitante}! Foi uma honra receber você na Life Church. Em breve nossa equipe entrará em contato para orar e acolher você. Deus abençoe!";
                    $tplZoneWelcome = Setting::get('sms.template.visitor_welcome_general', $defaultTplZoneWelcome);
                    $msgVisitor = str_replace(
                        ['{visitante}', '{visitante_primeiro_nome}'],
                        [$this->name, $this->getFirstName($this->name)],
                        $tplZoneWelcome
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

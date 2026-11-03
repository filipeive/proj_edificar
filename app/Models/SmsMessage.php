<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMozPhone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsMessage extends CongregationModel
{
    use HasFactory, NormalizesMozPhone;

    protected $fillable = [
        'congregation_id',
        'user_id',
        'phone',
        'recipient_name',
        'recipient_type',
        'recipient_id',
        'message',
        'characters_count',
        'parts_count',
        'driver',
        'status',
        'context',
        'reference_type',
        'reference_id',
        'response_payload',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'sent_at' => 'datetime',
        'characters_count' => 'integer',
        'parts_count' => 'integer',
    ];

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $this->normalizeMozPhone($value);
    }

    /**
     * Autor que disparou a mensagem no sistema (null se disparo automático).
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuário destinatário (caso recipient_type seja user ou derivados).
     */
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Referência polimórfica (ex: Visitor, Cell, CommitmentPackage).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->today());
    }

    public function scopeByContext($query, $context)
    {
        return $query->where('context', $context);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('phone', 'like', "%{$term}%")
              ->orWhere('recipient_name', 'like', "%{$term}%")
              ->orWhere('message', 'like', "%{$term}%")
              ->orWhere('context', 'like', "%{$term}%");
        });
    }

    /**
     * Retorna o label humanizado do contexto.
     */
    public function getContextLabelAttribute(): string
    {
        return match ($this->context) {
            'visitor_network_pastor' => 'Rede: Informe Pastoral',
            'visitor_network_supervisor' => 'Rede: Ação Supervisor',
            'visitor_network_leader' => 'Rede: Ação Líder de Célula',
            'visitor_welcome' => 'Boas-vindas Visitante',
            'broadcast' => 'Comunicado Geral',
            'package_reminder' => 'Lembrete de Pacote',
            'custom' => 'Envio Manual',
            default => ucfirst(str_replace('_', ' ', $this->context ?? 'Geral')),
        };
    }
}


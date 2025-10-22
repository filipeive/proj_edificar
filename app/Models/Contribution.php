<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'cell_id',
        'supervision_id',
        'zone_id',
        'proof_path',
        'contribution_date',
        'package_id',
        'notes',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // RELACIONAMENTOS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
    public function supervision()
    {
        return $this->belongsTo(Supervision::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function package()
    {
        return $this->belongsTo(CommitmentPackage::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    // Adicionado: alias esperado por outras partes do código
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    // ESCOPOS
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verificada');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejeitada');
    }

    public function scopeThisMonth($query)
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);
        
        return $query->whereBetween('contribution_date', [$monthStart, $monthEnd]);
    }

    // HELPERS
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verificada';
    }

    public function isRejected()
    {
        return $this->status === 'rejeitada';
    }

    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pendente',
            'verificada' => 'Verificada ✓',
            'rejeitada' => 'Rejeitada ✗',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'verificada' => 'green',
            'rejeitada' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }
    //Regestered by
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
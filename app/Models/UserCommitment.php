<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCommitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'cell_id', // Adicionado
        'committed_amount', // Adicionado
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // RELACIONAMENTOS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(CommitmentPackage::class);
    }

    // ESCOPOS
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        });
    }

    // HELPERS
    public function isActive()
    {
        return $this->end_date === null || $this->end_date->isFuture();
    }

    public function daysUntilExpiration()
    {
        if (!$this->end_date)
            return null;
        return now()->diffInDays($this->end_date, false);
    }

    public function isExpiringSoon($days = 7)
    {
        $daysRemaining = $this->daysUntilExpiration();
        return $daysRemaining !== null && $daysRemaining > 0 && $daysRemaining <= $days;
    }

    // NOVOS HELPERS DE CAMPANHA
    public function getTotalContributed()
    {
        return Contribution::where('user_id', $this->user_id)
            ->where('package_id', $this->package_id)
            ->where('status', 'verificada')
            ->sum('amount');
    }

    public function getCampaignStatus()
    {
        $total = (float) $this->getTotalContributed();
        $committed = (float) $this->committed_amount;

        if ($total >= $committed) {
            return $total > $committed ? 'surplus' : 'paid';
        }

        return $total > 0 ? 'partial' : 'pending';
    }

    public function getSurplusAmount()
    {
        $total = (float) $this->getTotalContributed();
        $committed = (float) $this->committed_amount;

        return max(0, $total - $committed);
    }

    public function getProgressPercentage()
    {
        $committed = (float) $this->committed_amount;
        if ($committed <= 0)
            return 0;

        $total = (float) $this->getTotalContributed();
        return min(100, round(($total / $committed) * 100));
    }

    public function getTotalPending()
    {
        return Contribution::where('user_id', $this->user_id)
            ->where('package_id', $this->package_id)
            ->where('status', 'pendente')
            ->sum('amount');
    }

    public function getTotalCanceled()
    {
        return Contribution::where('user_id', $this->user_id)
            ->where('package_id', $this->package_id)
            ->where('status', 'cancelada')
            ->sum('amount');
    }
}

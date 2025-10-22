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
        'end_date'];

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
        if (!$this->end_date) return null;
        return now()->diffInDays($this->end_date, false);
    }

    public function isExpiringSoon($days = 7)
    {
        $daysRemaining = $this->daysUntilExpiration();
        return $daysRemaining !== null && $daysRemaining > 0 && $daysRemaining <= $days;
    }
}

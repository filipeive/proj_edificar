<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cell_id',
        'supervision_id',
        'zone_id',
        'amount',
        'contribution_date',
        'proof_path',
        'status',
        'registered_by_id',
        'verified_by_id',
        'notes',
    ];

    protected $casts = [
        'contribution_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cell() {
        return $this->belongsTo(Cell::class);
    }

    public function supervision() {
        return $this->belongsTo(Supervision::class);
    }

    public function zone() {
        return $this->belongsTo(Zone::class);
    }

    public function registeredBy() {
        return $this->belongsTo(User::class, 'registered_by_id');
    }

    public function verifiedBy() {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function isPending() {
        return $this->status === 'pendente';
    }

    public function isVerified() {
        return $this->status === 'verificada';
    }

    public function isRejected() {
        return $this->status === 'rejeitada';
    }
}
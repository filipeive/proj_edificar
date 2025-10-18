<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitmentPackage extends Model {
    use HasFactory;

    protected $fillable = ['name', 'min_amount', 'max_amount', 'description', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userCommitments() {
        return $this->hasMany(UserCommitment::class, 'package_id');
    }

    public function getActiveMembersCount() {
        return $this->userCommitments()
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
            })
            ->count();
    }
}
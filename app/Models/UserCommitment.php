<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCommitment extends Model {
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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function package() {
        return $this->belongsTo(CommitmentPackage::class);
    }


    public function isActive() {
        return is_null($this->end_date) || $this->end_date > now();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'condition',
        'location',
        'description',
        'purchased_at',
        'value',
    ];

    protected $casts = [
        'purchased_at' => 'date',
        'value' => 'decimal:2',
    ];
}

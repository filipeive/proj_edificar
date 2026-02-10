<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model instance (polymorphic manual).
     */
    public function subject()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Icon accessor based on action type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-left',
            'create' => 'bi-plus-circle-fill',
            'update' => 'bi-pencil-fill',
            'delete' => 'bi-trash-fill',
            'verify' => 'bi-check-circle-fill',
            'reject' => 'bi-x-circle-fill',
            'cancel' => 'bi-slash-circle-fill',
            'assign' => 'bi-person-plus-fill',
            'reset_password' => 'bi-key-fill',
            default => 'bi-activity',
        };
    }

    /**
     * Badge color accessor based on action type.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->action) {
            'login' => 'blue',
            'logout' => 'gray',
            'create' => 'green',
            'update' => 'orange',
            'delete' => 'red',
            'verify' => 'emerald',
            'reject' => 'red',
            'cancel' => 'yellow',
            'assign' => 'purple',
            'reset_password' => 'amber',
            default => 'gray',
        };
    }
}

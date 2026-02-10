<?php

namespace App\Models\Concerns;

use App\Models\UserActivity;

trait LogsActivity
{
    /**
     * Get all activities for the user.
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class)->orderByDesc('created_at');
    }

    /**
     * Log an activity for this user.
     */
    public function logActivity(string $action, ?string $description = null, $model = null): UserActivity
    {
        $data = [
            'user_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->getKey();
        }

        return UserActivity::create($data);
    }
}

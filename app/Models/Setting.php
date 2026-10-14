<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Services\CongregationContext;

class Setting extends CongregationModel
{
    protected $fillable = ['key', 'value', 'type', 'group', 'is_public'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember(self::cacheKey($key), 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        Cache::forget(self::cacheKey($key));
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)->get()->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        })->toArray();
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Clear only the cached setting entries without flushing the whole application cache.
     */
    public static function clearCache(): void
    {
        $settingKeys = self::query()->pluck('key')->map(function (string $key) {
            return self::cacheKey($key);
        })->all();

        foreach ($settingKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }

    private static function cacheKey(string $key): string
    {
        return 'setting.' . (app(CongregationContext::class)->id() ?? 'global') . ".{$key}";
    }
}

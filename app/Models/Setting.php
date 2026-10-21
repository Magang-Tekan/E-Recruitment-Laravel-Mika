<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Cache duration in seconds (24 hours).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Get a setting value by key with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('app_setting_' . $key, self::CACHE_TTL, function () use ($key, $default) {
            $record = static::where('key', $key)->first();
            return ($record !== null && $record->value !== null && $record->value !== '')
                ? $record->value
                : $default;
        });
    }

    /**
     * Set/update a setting value and invalidate its cache.
     */
    public static function set(string $key, mixed $value, ?string $description = null): static
    {
        Cache::forget('app_setting_' . $key);

        $attributes = ['value' => $value];
        if ($description !== null) {
            $attributes['description'] = $description;
        }

        return static::updateOrCreate(
            ['key' => $key],
            $attributes
        );
    }

    /**
     * Remove a setting and its cache.
     */
    public static function forget(string $key): bool
    {
        Cache::forget('app_setting_' . $key);
        return (bool) static::where('key', $key)->delete();
    }
}

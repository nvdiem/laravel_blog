<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type'];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a site setting value
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("site_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a site setting value
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        // Clear cache for this setting
        Cache::forget("site_setting_{$key}");
    }

    /**
     * Get all site settings as array
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever('site_settings_all', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all site settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');

        // Clear individual setting caches
        $settings = static::pluck('key');
        foreach ($settings as $key) {
            Cache::forget("site_setting_{$key}");
        }
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}

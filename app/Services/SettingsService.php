<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'global_app_settings';

    /**
     * Cache theke shob settings eksathe load kora
     */
    public static function getAll(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            // Database theke key-value pair hishebe tule ana
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Kono nirdishto (specific) setting er value get kora
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getAll();

        return $settings[$key] ?? $default;
    }

    /**
     * Notun setting add ba update kora ebong cache clear kora
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value, // Sob value string hishebe save hobe
                'group' => $group,
                'type' => $type
            ]
        );

        self::clearCache();
    }

    /**
     * Settings update hole cache delete kora (jate next request e abar fresh data ashe)
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
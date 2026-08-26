<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    /**
     * Get a global application setting from cache/database.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return SettingsService::get($key, $default);
    }
}
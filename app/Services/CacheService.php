<?php

namespace App\Services;

use App\Models\Location;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CacheService
{
    /**
     * Cache expiration duration: 24 hours (in seconds).
     */
    public const CACHE_TTL = 86400;

    /**
     * Cache keys used across the application.
     */
    public const KEY_SETTINGS = 'site_settings';
    public const KEY_TYPES = 'property_types';
    public const KEY_STATUSES = 'property_statuses';
    public const KEY_LOCATIONS = 'locations';

    /**
     * Retrieve cached site settings or query database.
     */
    public static function getSiteSettings(): array
    {
        return Cache::remember(self::KEY_SETTINGS, self::CACHE_TTL, function () {
            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                try {
                    return SiteSetting::pluck('value', 'key')->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            }
            return [];
        });
    }

    /**
     * Retrieve cached active property types or query database.
     */
    public static function getPropertyTypes()
    {
        return Cache::remember(self::KEY_TYPES, self::CACHE_TTL, function () {
            if (class_exists(PropertyType::class) && Schema::hasTable('property_types')) {
                try {
                    return PropertyType::where('is_active', true)->orderBy('name')->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }
            return collect();
        });
    }

    /**
     * Retrieve cached property statuses or query database.
     */
    public static function getPropertyStatuses()
    {
        return Cache::remember(self::KEY_STATUSES, self::CACHE_TTL, function () {
            if (class_exists(PropertyStatus::class) && Schema::hasTable('property_statuses')) {
                try {
                    return PropertyStatus::all();
                } catch (\Throwable $e) {
                    return collect();
                }
            }
            return collect();
        });
    }

    /**
     * Retrieve cached active locations or query database.
     */
    public static function getLocations()
    {
        return Cache::remember(self::KEY_LOCATIONS, self::CACHE_TTL, function () {
            if (class_exists(Location::class) && Schema::hasTable('locations')) {
                try {
                    return Location::where('is_active', true)->orderBy('name')->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            }
            return collect();
        });
    }

    /**
     * Forget a specific cached key.
     */
    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Invalidate all frontend cached datasets.
     */
    public static function forgetAll(): void
    {
        Cache::forget(self::KEY_SETTINGS);
        Cache::forget(self::KEY_TYPES);
        Cache::forget(self::KEY_STATUSES);
        Cache::forget(self::KEY_LOCATIONS);
    }
}

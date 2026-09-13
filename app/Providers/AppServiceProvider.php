<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\HashidsService::class, function () {
            return new \App\Services\HashidsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant 'Super Admin' role all permissions in Gates and @can directives
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Define rate limiter for lead submissions (5 requests per minute per IP)
        \Illuminate\Support\Facades\RateLimiter::for('inquiries', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // Automatic cache invalidation on model changes (24h cache lifecycle)
        \App\Models\SiteSetting::saved(fn () => \Illuminate\Support\Facades\Cache::forget('site_settings'));
        \App\Models\SiteSetting::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('site_settings'));

        \App\Models\PropertyType::saved(fn () => \Illuminate\Support\Facades\Cache::forget('property_types'));
        \App\Models\PropertyType::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('property_types'));

        \App\Models\PropertyStatus::saved(fn () => \Illuminate\Support\Facades\Cache::forget('property_statuses'));
        \App\Models\PropertyStatus::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('property_statuses'));

        \App\Models\Location::saved(fn () => \Illuminate\Support\Facades\Cache::forget('locations'));
        \App\Models\Location::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('locations'));
    }
}

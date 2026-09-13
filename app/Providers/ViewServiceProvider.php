<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share $settings across all customer views, components, and layouts
        View::composer([
            'layouts.customer',
            'components.layouts.customer',
            'components.customer.*',
            'livewire.customer.*',
            'welcome',
        ], function ($view) {
            $settings = [];

            if (class_exists(SiteSetting::class) && Schema::hasTable('site_settings')) {
                try {
                    $settings = SiteSetting::pluck('value', 'key')->toArray();
                } catch (\Throwable $e) {
                    $settings = [];
                }
            }

            // Defaults and aliases
            $settings['site_name'] = $settings['site_name'] ?? 'TISHA Real Estate';
            $settings['site_tagline'] = $settings['site_tagline'] ?? 'Discover Luxury Living & Premium Properties';
            $settings['phone'] = $settings['phone'] ?? $settings['contact_phone'] ?? '+971 4 123 4567';
            $settings['contact_phone'] = $settings['phone'];
            $settings['email'] = $settings['email'] ?? $settings['contact_email'] ?? 'info@tisharealty.com';
            $settings['contact_email'] = $settings['email'];
            $settings['address'] = $settings['address'] ?? $settings['contact_address'] ?? 'Suite 1402, Marina Plaza, Dubai Marina, Dubai, UAE';
            $settings['contact_address'] = $settings['address'];
            $settings['copyright_text'] = $settings['copyright_text'] ?? $settings['site_copyright'] ?? 'All rights reserved.';
            $settings['social_facebook'] = $settings['social_facebook'] ?? '#';
            $settings['social_twitter'] = $settings['social_twitter'] ?? '#';
            $settings['social_instagram'] = $settings['social_instagram'] ?? '#';
            $settings['social_linkedin'] = $settings['social_linkedin'] ?? '#';
            $settings['logo'] = $settings['logo'] ?? $settings['site_logo'] ?? null;
            $settings['favicon'] = $settings['favicon'] ?? $settings['site_favicon'] ?? null;

            $view->with('settings', $settings);
        });
    }
}

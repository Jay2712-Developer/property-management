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
            'customer.*',
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
            $settings['map'] = $settings['map'] ?? $settings['contact_map_iframe'] ?? $settings['google_map_embed'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14451.782875148675!2d55.1328005871582!3d25.076384599999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f134db2faec09%3A0x6b77ff0a7ef2049e!2sMarina%20Plaza%20-%20Dubai%20Marina%20-%20Dubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2sae!4v1700000000000!5m2!1sen!2sae" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
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

<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('admin.layouts.app')]
#[Title('Site Settings - TISHA Real Estate')]
class ManageSiteSettings extends Component
{
    use WithFileUploads;

    // Active Navigation Tab
    public string $activeTab = 'general';

    // 1. General Settings
    #[Rule('required|string|max:255', as: 'site name')]
    public string $site_name = 'TISHA Real Estate';

    #[Rule('nullable|string|max:255', as: 'copyright text')]
    public string $site_copyright = '';

    #[Rule('nullable|image|max:2048', as: 'site logo')]
    public $logo = null;

    public ?string $current_logo = null;

    #[Rule('nullable|file|mimes:ico,png,svg,jpg,jpeg,webp|max:1024', as: 'site favicon')]
    public $favicon = null;

    public ?string $current_favicon = null;

    // 2. Contact Settings
    #[Rule('nullable|string|max:50', as: 'contact phone')]
    public string $contact_phone = '';

    #[Rule('nullable|email|max:255', as: 'contact email')]
    public string $contact_email = '';

    #[Rule('nullable|string|max:500', as: 'office address')]
    public string $contact_address = '';

    #[Rule('nullable|string', as: 'Google Maps embed iframe')]
    public string $contact_map_iframe = '';

    // 3. Social Media URLs
    #[Rule('nullable|url|max:255', as: 'Facebook URL')]
    public string $social_facebook = '';

    #[Rule('nullable|url|max:255', as: 'Twitter/X URL')]
    public string $social_twitter = '';

    #[Rule('nullable|url|max:255', as: 'Instagram URL')]
    public string $social_instagram = '';

    #[Rule('nullable|url|max:255', as: 'LinkedIn URL')]
    public string $social_linkedin = '';

    // 4. SEO & Analytics
    #[Rule('nullable|string|max:255', as: 'meta title')]
    public string $seo_meta_title = '';

    #[Rule('nullable|string|max:500', as: 'meta description')]
    public string $seo_meta_description = '';

    #[Rule('nullable|string|max:50', as: 'Google Analytics ID')]
    public string $seo_google_analytics_id = '';

    /**
     * Mount and load all existing settings from database.
     */
    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('manage_settings'),
            403,
            'Unauthorized. You do not have permission to manage site settings.'
        );

        $settings = SiteSetting::all()->keyBy('key');

        // General
        $this->site_name = $settings->get('site_name')->value ?? 'TISHA Real Estate';
        $this->site_copyright = $settings->get('site_copyright')->value ?? '© ' . date('Y') . ' TISHA Real Estate. All rights reserved.';
        $this->current_logo = $settings->get('site_logo')->value ?? null;
        $this->current_favicon = $settings->get('site_favicon')->value ?? null;

        // Contact
        $this->contact_phone = $settings->get('contact_phone')->value ?? '';
        $this->contact_email = $settings->get('contact_email')->value ?? '';
        $this->contact_address = $settings->get('contact_address')->value ?? '';
        $this->contact_map_iframe = $settings->get('contact_map_iframe')->value ?? '';

        // Social Media
        $this->social_facebook = $settings->get('social_facebook')->value ?? '';
        $this->social_twitter = $settings->get('social_twitter')->value ?? '';
        $this->social_instagram = $settings->get('social_instagram')->value ?? '';
        $this->social_linkedin = $settings->get('social_linkedin')->value ?? '';

        // SEO
        $this->seo_meta_title = $settings->get('seo_meta_title')->value ?? 'TISHA Real Estate - Luxury Properties & Prime Investments';
        $this->seo_meta_description = $settings->get('seo_meta_description')->value ?? 'Explore exclusive luxury properties, penthouses, and private estates with TISHA Real Estate.';
        $this->seo_google_analytics_id = $settings->get('seo_google_analytics_id')->value ?? '';
    }

    /**
     * Switch active tab.
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['general', 'contact', 'social', 'seo'])) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Remove the current custom logo.
     */
    public function removeLogo(): void
    {
        abort_unless(auth()->user()?->can('manage_settings'), 403);

        if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
            Storage::disk('public')->delete($this->current_logo);
        }

        SiteSetting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => null, 'group_name' => 'general']
        );

        $this->current_logo = null;
        $this->logo = null;
        session()->flash('status', 'Site logo removed successfully.');
    }

    /**
     * Remove the current custom favicon.
     */
    public function removeFavicon(): void
    {
        abort_unless(auth()->user()?->can('manage_settings'), 403);

        if ($this->current_favicon && Storage::disk('public')->exists($this->current_favicon)) {
            Storage::disk('public')->delete($this->current_favicon);
        }

        SiteSetting::updateOrCreate(
            ['key' => 'site_favicon'],
            ['value' => null, 'group_name' => 'general']
        );

        $this->current_favicon = null;
        $this->favicon = null;
        session()->flash('status', 'Site favicon removed successfully.');
    }

    /**
     * Save site settings.
     */
    public function save(): void
    {
        abort_unless(
            auth()->user()?->can('manage_settings'),
            403,
            'Unauthorized. You do not have permission to manage site settings.'
        );

        $this->validate();

        // 1. Handle Logo Upload
        if ($this->logo) {
            if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
                Storage::disk('public')->delete($this->current_logo);
            }

            $logoPath = $this->logo->store('uploads/settings', 'public');
            SiteSetting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $logoPath, 'group_name' => 'general']
            );

            $this->current_logo = $logoPath;
            $this->logo = null;
        }

        // 2. Handle Favicon Upload
        if ($this->favicon) {
            if ($this->current_favicon && Storage::disk('public')->exists($this->current_favicon)) {
                Storage::disk('public')->delete($this->current_favicon);
            }

            $faviconPath = $this->favicon->store('uploads/settings', 'public');
            SiteSetting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => $faviconPath, 'group_name' => 'general']
            );

            $this->current_favicon = $faviconPath;
            $this->favicon = null;
        }

        // 3. Map of all settings keys, values, and groups
        $settingsToSave = [
            // General
            'site_name' => ['value' => $this->site_name, 'group' => 'general'],
            'site_copyright' => ['value' => $this->site_copyright, 'group' => 'general'],

            // Contact
            'contact_phone' => ['value' => $this->contact_phone, 'group' => 'contact'],
            'contact_email' => ['value' => $this->contact_email, 'group' => 'contact'],
            'contact_address' => ['value' => $this->contact_address, 'group' => 'contact'],
            'contact_map_iframe' => ['value' => $this->contact_map_iframe, 'group' => 'contact'],

            // Social Media
            'social_facebook' => ['value' => $this->social_facebook, 'group' => 'social'],
            'social_twitter' => ['value' => $this->social_twitter, 'group' => 'social'],
            'social_instagram' => ['value' => $this->social_instagram, 'group' => 'social'],
            'social_linkedin' => ['value' => $this->social_linkedin, 'group' => 'social'],

            // SEO & Analytics
            'seo_meta_title' => ['value' => $this->seo_meta_title, 'group' => 'seo'],
            'seo_meta_description' => ['value' => $this->seo_meta_description, 'group' => 'seo'],
            'seo_google_analytics_id' => ['value' => $this->seo_google_analytics_id, 'group' => 'seo'],
        ];

        foreach ($settingsToSave as $key => $data) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'group_name' => $data['group'],
                ]
            );
        }

        ActivityLog::record("Updated site configuration settings", 'Settings', null);

        session()->flash('status', 'Site settings have been successfully updated and saved.');
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('manage_settings'),
            403,
            'Unauthorized. You do not have permission to manage site settings.'
        );

        return view('livewire.admin.manage-site-settings');
    }
}

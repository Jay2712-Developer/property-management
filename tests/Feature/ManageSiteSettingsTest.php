<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManageSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $managerUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_settings@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->managerUser = User::factory()->create([
            'email' => 'manager_settings@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->managerUser->assignRole('Manager');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_settings@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_render_site_settings_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.settings'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageSiteSettings::class)
            ->assertSee('System &amp; Site Settings', false)
            ->assertSee('General Identity')
            ->assertSee('Contact &amp; Map', false)
            ->assertSee('Social Media')
            ->assertSee('SEO &amp; Analytics', false);
    }

    public function test_unauthorized_user_and_manager_forbidden_from_settings(): void
    {
        // 1. Unauthorized guest/user
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.settings'))
            ->assertStatus(403);

        // 2. Manager without manage_settings permission
        $this->actingAs($this->managerUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.settings'))
            ->assertStatus(403);
    }

    public function test_component_hydrates_existing_settings_on_mount(): void
    {
        SiteSetting::setValue('site_name', 'TISHA Elite Realty', 'general');
        SiteSetting::setValue('contact_email', 'concierge@tishaelite.com', 'contact');
        SiteSetting::setValue('seo_meta_title', 'TISHA Elite - Private Residences', 'seo');

        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->assertSet('site_name', 'TISHA Elite Realty')
            ->assertSet('contact_email', 'concierge@tishaelite.com')
            ->assertSet('seo_meta_title', 'TISHA Elite - Private Residences');
    }

    public function test_can_switch_tabs(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->assertSet('activeTab', 'general')
            ->call('setTab', 'contact')
            ->assertSet('activeTab', 'contact')
            ->call('setTab', 'social')
            ->assertSet('activeTab', 'social')
            ->call('setTab', 'seo')
            ->assertSet('activeTab', 'seo');
    }

    public function test_can_update_general_and_contact_settings(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->set('site_name', 'TISHA Luxury Estates')
            ->set('site_copyright', '© 2026 TISHA Luxury Estates. All rights reserved.')
            ->set('contact_phone', '+1 (555) 999-8888')
            ->set('contact_email', 'advisory@tishaluxury.com')
            ->set('contact_address', 'Level 50, Burj Daman, DIFC, Dubai, UAE')
            ->set('contact_map_iframe', '<iframe src="https://maps.google.com/test"></iframe>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Site settings have been successfully updated and saved.');

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => 'TISHA Luxury Estates',
            'group_name' => 'general',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'contact_email',
            'value' => 'advisory@tishaluxury.com',
            'group_name' => 'contact',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'contact_address',
            'value' => 'Level 50, Burj Daman, DIFC, Dubai, UAE',
            'group_name' => 'contact',
        ]);
    }

    public function test_can_update_social_and_seo_settings(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->set('social_facebook', 'https://facebook.com/tishaproperty')
            ->set('social_twitter', 'https://x.com/tishaproperty')
            ->set('social_instagram', 'https://instagram.com/tishaproperty')
            ->set('social_linkedin', 'https://linkedin.com/company/tishaproperty')
            ->set('seo_meta_title', 'TISHA Real Estate | Luxury Homes & Penthouses')
            ->set('seo_meta_description', 'Discover prime real estate listings with TISHA advisory.')
            ->set('seo_google_analytics_id', 'G-1234567890')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'social_instagram',
            'value' => 'https://instagram.com/tishaproperty',
            'group_name' => 'social',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'seo_google_analytics_id',
            'value' => 'G-1234567890',
            'group_name' => 'seo',
        ]);
    }

    public function test_can_upload_logo_and_favicon(): void
    {
        Storage::fake('public');

        $logoFile = UploadedFile::fake()->image('brand-logo.png', 400, 100);
        $faviconFile = UploadedFile::fake()->image('favicon.png', 32, 32);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->set('logo', $logoFile)
            ->set('favicon', $faviconFile)
            ->call('save')
            ->assertHasNoErrors();

        $logoSetting = SiteSetting::where('key', 'site_logo')->first();
        $this->assertNotNull($logoSetting);
        $this->assertNotNull($logoSetting->value);
        Storage::disk('public')->assertExists($logoSetting->value);

        $faviconSetting = SiteSetting::where('key', 'site_favicon')->first();
        $this->assertNotNull($faviconSetting);
        $this->assertNotNull($faviconSetting->value);
        Storage::disk('public')->assertExists($faviconSetting->value);
    }

    public function test_can_remove_logo_and_favicon(): void
    {
        Storage::fake('public');

        $logoFile = UploadedFile::fake()->image('logo-to-delete.png');
        $storedLogo = $logoFile->store('uploads/settings', 'public');
        SiteSetting::setValue('site_logo', $storedLogo, 'general');

        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->assertSet('current_logo', $storedLogo)
            ->call('removeLogo')
            ->assertSet('current_logo', null);

        Storage::disk('public')->assertMissing($storedLogo);
        $this->assertNull(SiteSetting::getValue('site_logo'));
    }

    public function test_validation_fails_on_invalid_data(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageSiteSettings::class)
            ->set('site_name', '') // Required
            ->set('contact_email', 'not-a-valid-email')
            ->set('social_facebook', 'invalid-url')
            ->call('save')
            ->assertHasErrors(['site_name', 'contact_email', 'social_facebook']);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Customer\NewsletterSubscribe;
use App\Models\ContactInquiry;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerNavbarFooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_service_provider_shares_settings_with_customer_views(): void
    {
        SiteSetting::create([
            'key' => 'site_name',
            'value' => 'TISHA Custom Luxury',
            'group_name' => 'general',
        ]);

        SiteSetting::create([
            'key' => 'contact_phone',
            'value' => '+971 50 999 8888',
            'group_name' => 'contact',
        ]);

        SiteSetting::create([
            'key' => 'social_facebook',
            'value' => 'https://facebook.com/tishacustom',
            'group_name' => 'social',
        ]);

        $rendered = Blade::render('<x-customer.navbar />');
        $this->assertStringContainsString('TISHA Custom Luxury', $rendered);
        $this->assertStringContainsString('+971 50 999 8888', $rendered);

        $footerRendered = Blade::render('<x-customer.footer />');
        $this->assertStringContainsString('TISHA Custom Luxury', $footerRendered);
        $this->assertStringContainsString('https://facebook.com/tishacustom', $footerRendered);
    }

    public function test_navbar_component_renders_all_required_links_and_actions(): void
    {
        $rendered = Blade::render('<x-customer.navbar />');

        // Check required navigation links
        $this->assertStringContainsString('Home', $rendered);
        $this->assertStringContainsString('For Sale', $rendered);
        $this->assertStringContainsString('For Rent', $rendered);
        $this->assertStringContainsString('About', $rendered);
        $this->assertStringContainsString('Contact', $rendered);

        // Check CTA button & theme toggle
        $this->assertStringContainsString('Book Consultation', $rendered);
        $this->assertStringContainsString('darkMode = !darkMode', $rendered);
        $this->assertStringContainsString('fa-moon', $rendered);
        $this->assertStringContainsString('fa-sun', $rendered);

        // Check styling classes
        $this->assertStringContainsString('backdrop-blur-md', $rendered);
        $this->assertStringContainsString('dark:bg-[#1A1A1A]', $rendered);
    }

    public function test_footer_component_renders_columns_and_newsletter(): void
    {
        $rendered = Blade::render('<x-customer.footer />');

        // Columns check
        $this->assertStringContainsString('Quick Links', $rendered);
        $this->assertStringContainsString('Our Services', $rendered);
        $this->assertStringContainsString('Newsletter', $rendered);
        $this->assertStringContainsString('Privacy Policy', $rendered);
        $this->assertStringContainsString('Terms of Service', $rendered);
        $this->assertStringContainsString('bg-[#1A1A1A]', $rendered);
    }

    public function test_newsletter_subscribe_livewire_component_validates_and_subscribes(): void
    {
        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'invalid-email')
            ->call('subscribe')
            ->assertHasErrors(['email' => 'email'])
            ->assertSet('subscribed', false);

        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'vipbuyer@luxury.com')
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscribed', true)
            ->assertSee('Thank you for subscribing!');

        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'vipbuyer@luxury.com',
            'subject' => 'VIP Newsletter Subscription',
        ]);
    }
}

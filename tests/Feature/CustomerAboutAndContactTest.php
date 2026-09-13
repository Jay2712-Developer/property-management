<?php

namespace Tests\Feature;

use App\Livewire\Customer\ContactForm;
use App\Livewire\Customer\NewsletterSubscribe;
use App\Models\Agent;
use App\Models\ContactInquiry;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\SiteSetting;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerAboutAndContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);
    }

    public function test_about_page_renders_successfully_with_content_from_pages_table_and_active_agents(): void
    {
        // 1. Ensure about page exists in pages table
        Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About TISHA Signature Brokerage',
                'content' => '<h2>Excellence In Ultra-Prime Properties</h2><p>Pioneering the bespoke luxury lifestyle across the Emirates.</p>',
                'meta_title' => 'About TISHA - Private Brokerage',
                'meta_description' => 'Discover our bespoke real estate advisory services.',
                'is_active' => true,
            ]
        );

        // 2. Create an active agent
        $agent = Agent::create([
            'name' => 'Julian Vance-Croft',
            'designation' => 'Executive Director - Waterfront Assets',
            'phone' => '+971 50 888 1234',
            'email' => 'julian.croft@tisharealty.com',
            'experience_years' => 15,
            'is_active' => true,
        ]);

        // 3. Create an inactive agent (should not be displayed)
        $inactiveAgent = Agent::create([
            'name' => 'Hidden Inactive Agent',
            'designation' => 'Former Associate',
            'phone' => '+971 50 000 0000',
            'email' => 'hidden@tisharealty.com',
            'experience_years' => 2,
            'is_active' => false,
        ]);

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSee('About TISHA Signature Brokerage');
        $response->assertSee('Excellence In Ultra-Prime Properties');
        $response->assertSee('Meet The Experts');
        $response->assertSee('Julian Vance-Croft');
        $response->assertSee('Executive Director - Waterfront Assets');
        $response->assertDontSee('Hidden Inactive Agent');
    }

    public function test_contact_page_renders_successfully_with_site_settings_and_contact_form(): void
    {
        SiteSetting::updateOrCreate(['key' => 'contact_phone'], ['value' => '+971 4 999 8888', 'group_name' => 'contact']);
        SiteSetting::updateOrCreate(['key' => 'contact_email'], ['value' => 'concierge.team@tishaproperty.com', 'group_name' => 'contact']);
        SiteSetting::updateOrCreate(['key' => 'contact_address'], ['value' => 'Level 88, Burj Crown, Downtown Dubai, UAE', 'group_name' => 'contact']);

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('+971 4 999 8888');
        $response->assertSee('concierge.team@tishaproperty.com');
        $response->assertSee('Level 88, Burj Crown, Downtown Dubai, UAE');
        $response->assertSeeLivewire(ContactForm::class);
    }

    public function test_contact_form_submits_and_saves_to_contact_inquiries_table(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'H.E. Lord Sterling')
            ->set('email', 'sterling@highgrove.co.uk')
            ->set('phone', '+44 7700 900077')
            ->set('subject', 'Property Buying Consultation')
            ->set('message', 'We wish to inquire regarding waterfront estates along the Palm Jumeirah crescent.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSee('Message Received');

        $this->assertDatabaseHas('contact_inquiries', [
            'name' => 'H.E. Lord Sterling',
            'email' => 'sterling@highgrove.co.uk',
            'phone' => '+44 7700 900077',
            'subject' => 'Property Buying Consultation',
            'status' => 'new',
        ]);
    }

    public function test_contact_form_validation_requires_mandatory_fields(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', '')
            ->set('email', 'invalid-email-address')
            ->set('phone', '')
            ->set('message', 'Short')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'phone', 'message']);
    }

    public function test_newsletter_subscription_saves_to_newsletter_subscribers_table_without_duplicates(): void
    {
        $subscriberEmail = 'investor@monacocapital.mc';

        // 1. Initial subscription
        Livewire::test(NewsletterSubscribe::class)
            ->set('email', $subscriberEmail)
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscribed', true)
            ->assertSee('Thank you for subscribing!');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $subscriberEmail,
            'is_active' => true,
        ]);

        // Also check contact_inquiries recorded newsletter lead
        $this->assertDatabaseHas('contact_inquiries', [
            'email' => $subscriberEmail,
            'subject' => 'VIP Newsletter Subscription',
        ]);

        // 2. Duplicate subscription attempt should handle gracefully without SQL violation
        Livewire::test(NewsletterSubscribe::class)
            ->set('email', $subscriberEmail)
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscribed', true);

        $this->assertEquals(1, NewsletterSubscriber::where('email', $subscriberEmail)->count());
    }

    public function test_customer_pages_include_newsletter_subscription_in_footer(): void
    {
        $this->get(route('about'))
            ->assertStatus(200)
            ->assertSeeLivewire(NewsletterSubscribe::class);

        $this->get(route('contact'))
            ->assertStatus(200)
            ->assertSeeLivewire(NewsletterSubscribe::class);
    }
}

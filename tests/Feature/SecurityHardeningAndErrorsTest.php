<?php

namespace Tests\Feature;

use App\Livewire\Customer\ContactForm;
use App\Livewire\Customer\NewsletterSubscribe;
use App\Livewire\Customer\ScheduleVisitForm;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityHardeningAndErrorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('contact-inquiry:127.0.0.1');
        RateLimiter::clear('newsletter-subscribe:127.0.0.1');
        RateLimiter::clear('schedule-visit:127.0.0.1');

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);
    }

    public function test_custom_404_error_page_renders_with_branding_and_customer_layout(): void
    {
        $response = $this->get('/non-existent-luxury-estate-url');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Page Not Found');
        $response->assertSeeText("The property or page you are looking for doesn't exist", false);
        $response->assertSee('Return to Homepage');
        $response->assertSee('TISHA');
    }

    public function test_custom_403_error_page_renders_with_branding_and_customer_layout(): void
    {
        $response = $this->get('/admin/roles'); // Guest visiting protected admin route triggers 403 or redirect

        // Let's test the 403 view directly
        $view = $this->view('errors.403', [
            'exception' => null,
            'message' => 'Access Denied. You do not have permission to view this page.',
        ]);

        $view->assertSee('403');
        $view->assertSee('Access Denied');
        $view->assertSee('You do not have permission to view this page.');
        $view->assertSee('Return to Homepage');
        $view->assertSee('TISHA');
    }

    public function test_custom_500_error_page_renders_with_branding_and_customer_layout(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Server Error');
        $view->assertSee('Something went wrong on our end');
        $view->assertSee('Return to Homepage');
        $view->assertSee('TISHA');
    }

    public function test_security_headers_are_attached_to_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
    }

    public function test_contact_form_enforces_rate_limiting_after_five_submissions(): void
    {
        // 5 valid submissions should pass
        for ($i = 1; $i <= 5; $i++) {
            Livewire::test(ContactForm::class)
                ->set('name', "Client {$i}")
                ->set('email', "client{$i}@luxury.com")
                ->set('phone', '+971 50 123 4567')
                ->set('subject', 'General Inquiry')
                ->set('message', 'Inquiring on prime residential properties in Dubai.')
                ->call('submit')
                ->assertHasNoErrors()
                ->assertSet('submitted', true);
        }

        // 6th submission must be throttled
        Livewire::test(ContactForm::class)
            ->set('name', 'Client Spammer')
            ->set('email', 'spam@bot.com')
            ->set('phone', '+971 50 999 9999')
            ->set('subject', 'General Inquiry')
            ->set('message', 'Spam message sent in rapid succession.')
            ->call('submit')
            ->assertHasErrors(['email'])
            ->assertSee('Too many inquiry submissions');
    }

    public function test_newsletter_subscribe_enforces_rate_limiting_after_five_submissions(): void
    {
        // 5 valid subscriptions should pass
        for ($i = 1; $i <= 5; $i++) {
            Livewire::test(NewsletterSubscribe::class)
                ->set('email', "subscriber{$i}@wealth.com")
                ->call('subscribe')
                ->assertHasNoErrors()
                ->assertSet('subscribed', true);
        }

        // 6th subscription must be throttled
        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'subscriber_spam@wealth.com')
            ->call('subscribe')
            ->assertHasErrors(['email'])
            ->assertSee('Too many subscription attempts');
    }

    public function test_schedule_visit_form_enforces_rate_limiting_after_five_submissions(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'villa'], ['name' => 'Villa', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'palm-jumeirah'], ['name' => 'Palm Jumeirah', 'is_active' => true]);

        $property = Property::create([
            'title' => 'The Grand Estate',
            'slug' => 'the-grand-estate',
            'description' => 'A luxury estate for visit requests.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 10000000,
            'is_active' => true,
        ]);

        // 5 valid submissions
        for ($i = 1; $i <= 5; $i++) {
            Livewire::test(ScheduleVisitForm::class, ['propertyId' => $property->id])
                ->set('name', "Visitor {$i}")
                ->set('email', "visitor{$i}@estate.com")
                ->set('phone', '+971 50 111 2222')
                ->set('visit_date', now()->addDays(2)->format('Y-m-d\TH:i'))
                ->call('submit')
                ->assertHasNoErrors()
                ->assertSet('submitted', true);
        }

        // 6th submission must be throttled
        Livewire::test(ScheduleVisitForm::class, ['propertyId' => $property->id])
            ->set('name', 'Visitor Spammer')
            ->set('email', 'spammer@estate.com')
            ->set('phone', '+971 50 999 8888')
            ->set('visit_date', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('submit')
            ->assertHasErrors(['email'])
            ->assertSee('Too many visit requests');
    }
}

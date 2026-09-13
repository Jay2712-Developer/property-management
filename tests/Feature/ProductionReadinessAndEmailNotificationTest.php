<?php

namespace Tests\Feature;

use App\Livewire\Customer\ContactForm;
use App\Livewire\Customer\ScheduleVisitForm;
use App\Mail\ContactInquiryReceived;
use App\Mail\VisitRequestReceived;
use App\Models\Agent;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ProductionReadinessAndEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('contact-inquiry:127.0.0.1');
        RateLimiter::clear('schedule-visit:127.0.0.1');

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);
    }

    public function test_mailables_implement_should_queue_interface(): void
    {
        $this->assertTrue(is_subclass_of(ContactInquiryReceived::class, ShouldQueue::class));
        $this->assertTrue(is_subclass_of(VisitRequestReceived::class, ShouldQueue::class));
    }

    public function test_contact_form_submission_dispatches_queued_email_to_admin(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Baron Von Sterling')
            ->set('email', 'baron@sterling.ch')
            ->set('phone', '+41 22 123 4567')
            ->set('subject', 'Property Buying Consultation')
            ->set('message', 'We wish to schedule private appointments for Emirates Hills estates.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        Mail::assertQueued(ContactInquiryReceived::class, function (ContactInquiryReceived $mail) {
            return $mail->inquiry->name === 'Baron Von Sterling' &&
                   $mail->inquiry->email === 'baron@sterling.ch' &&
                   str_contains($mail->envelope()->subject, 'Property Buying Consultation');
        });
    }

    public function test_schedule_visit_form_submission_dispatches_queued_email_to_admin(): void
    {
        Mail::fake();

        $type = PropertyType::firstOrCreate(['slug' => 'palace'], ['name' => 'Palace', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'palm-jumeirah'], ['name' => 'Palm Jumeirah', 'is_active' => true]);

        $agent = Agent::create([
            'name' => 'Alexander Vance',
            'phone' => '+971 50 123 4567',
            'email' => 'alexander@tisharealty.com',
            'is_active' => true,
        ]);

        $property = Property::create([
            'title' => 'The Grand Palm Palace',
            'slug' => 'the-grand-palm-palace',
            'description' => 'A private oceanfront retreat on Palm Jumeirah.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'agent_id' => $agent->id,
            'price' => 55000000,
            'is_active' => true,
        ]);

        Livewire::test(ScheduleVisitForm::class, ['propertyId' => $property->id])
            ->set('name', 'Duchess Eleanor')
            ->set('email', 'eleanor@kensington.co.uk')
            ->set('phone', '+44 20 7946 0991')
            ->set('visit_date', now()->addDays(3)->format('Y-m-d\TH:i'))
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        Mail::assertQueued(VisitRequestReceived::class, function (VisitRequestReceived $mail) use ($property) {
            return $mail->visitRequest->name === 'Duchess Eleanor' &&
                   $mail->property->id === $property->id &&
                   str_contains($mail->envelope()->subject, 'The Grand Palm Palace');
        });
    }
}

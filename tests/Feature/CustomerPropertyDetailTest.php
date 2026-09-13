<?php

namespace Tests\Feature;

use App\Livewire\Customer\ScheduleVisitForm;
use App\Models\Agent;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\VisitRequest;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerPropertyDetailTest extends TestCase
{
    use RefreshDatabase;

    protected Property $property;
    protected Agent $agent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $type = PropertyType::firstOrCreate(['slug' => 'villa'], ['name' => 'Villa', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'palm-jumeirah'], ['name' => 'Palm Jumeirah', 'is_active' => true]);

        $this->agent = Agent::create([
            'name' => 'Julian Montgomery',
            'designation' => 'Principal Luxury Partner',
            'phone' => '+971 50 777 9999',
            'email' => 'julian@tisharealty.com',
            'experience_years' => 12,
            'is_active' => true,
        ]);

        $this->property = Property::create([
            'title' => 'The Imperial Waterfront Palace',
            'slug' => 'the-imperial-waterfront-palace',
            'description' => 'A masterwork architectural residence boasting panoramic Arabian Gulf views and private yacht dock.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'agent_id' => $this->agent->id,
            'price' => 32000000,
            'bedrooms' => 7,
            'bathrooms' => 8,
            'sqft' => 14500,
            'garage' => 6,
            'year_built' => 2025,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $amenity = Amenity::firstOrCreate(['name' => 'Private Beach Access'], ['category' => 'Outdoor', 'sort_order' => 1, 'is_active' => true]);
        $this->property->amenities()->attach($amenity->id);
    }

    public function test_property_detail_page_renders_with_encrypted_hashid(): void
    {
        // Route should resolve using Hashid / route key
        $url = route('property.show', $this->property);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('The Imperial Waterfront Palace');
        $response->assertSee('₹3.20 Cr');
        $response->assertSee('Palm Jumeirah');
        $response->assertSee('7');
        $response->assertSee('Bedrooms');
        $response->assertSee('14,500');
        $response->assertSee('Private Beach Access');
        $response->assertSee('Julian Montgomery');
        $response->assertSee('+971 50 777 9999');
        $response->assertSee('Schedule a Visit');
        $response->assertSee('Chat on WhatsApp');
        $response->assertSeeLivewire(ScheduleVisitForm::class);
    }

    public function test_schedule_visit_form_validates_and_submits_request(): void
    {
        Livewire::test(ScheduleVisitForm::class, ['propertyId' => $this->property->id])
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['name', 'email'])
            ->assertSet('submitted', false);

        $visitDate = now()->addDays(3)->format('Y-m-d\TH:i');

        Livewire::test(ScheduleVisitForm::class, ['propertyId' => $this->property->id])
            ->set('name', 'Lady Genevieve Sterling')
            ->set('email', 'genevieve@sterling.co.uk')
            ->set('phone', '+44 7700 900077')
            ->set('visit_date', $visitDate)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSee('Tour Request Confirmed');

        $this->assertDatabaseHas('visit_requests', [
            'property_id' => $this->property->id,
            'name' => 'Lady Genevieve Sterling',
            'email' => 'genevieve@sterling.co.uk',
            'phone' => '+44 7700 900077',
            'status' => 'pending',
            'assigned_agent_id' => $this->agent->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Customer\FeaturedProperties;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Testimonial;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerHomePageTest extends TestCase
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

    public function test_customer_home_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(FeaturedProperties::class);

        // 1. Hero Section & Search
        $response->assertSee('Curated Luxury Living');
        $response->assertSee('Search Properties');
        $response->assertSee('Trending Areas:');
        $response->assertSee('Palm Jumeirah');

        // 2. Stats Section
        $response->assertSee('Exclusive Properties');
        $response->assertSee('Portfolio Transacted');

        // 3. Services Section
        $response->assertSee('Buy Luxury Homes');
        $response->assertSee('Rent Signature Properties');
        $response->assertSee('Sell & Asset Marketing', false);

        // 4. Testimonials & CTA
        $response->assertSee('Trusted by Global Investors');
        $response->assertSee('Speak with Concierge');
        $response->assertSee('Browse All Residences');
    }

    public function test_featured_properties_livewire_component_displays_properties(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'luxury-villa'], ['name' => 'Luxury Villa', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['name' => 'For Sale'], ['color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'palm-jumeirah'], ['name' => 'Palm Jumeirah', 'is_active' => true]);

        $featuredProperty = Property::create([
            'title' => 'Signature Palm Waterfront Villa',
            'slug' => 'signature-palm-waterfront-villa',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 18500000,
            'bedrooms' => 6,
            'bathrooms' => 7,
            'sqft' => 9500,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $nonFeaturedProperty = Property::create([
            'title' => 'Hidden Standard Apartment',
            'slug' => 'hidden-standard-apartment',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 1200000,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'sqft' => 1200,
            'is_featured' => false,
            'is_active' => true,
        ]);

        Livewire::test(FeaturedProperties::class)
            ->assertSee('Signature Palm Waterfront Villa')
            ->assertSee('₹1.85 Cr')
            ->assertSee('Palm Jumeirah')
            ->assertSee('6')
            ->assertSee('Beds')
            ->assertDontSee('Hidden Standard Apartment');
    }

    public function test_testimonials_are_rendered_on_home_page(): void
    {
        Testimonial::create([
            'client_name' => 'Lord Archibald Sterling',
            'rating' => 5,
            'message' => 'The absolute finest real estate transaction team in the Middle East.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Lord Archibald Sterling');
        $response->assertSee('The absolute finest real estate transaction team');
    }

    public function test_properties_search_route_redirects_gracefully(): void
    {
        $response = $this->get('/properties');
        $response->assertRedirect('/#properties');
    }
}

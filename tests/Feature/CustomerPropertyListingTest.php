<?php

namespace Tests\Feature;

use App\Livewire\Customer\PropertyListing;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerPropertyListingTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyType $villaType;
    protected PropertyType $penthouseType;
    protected PropertyStatus $saleStatus;
    protected PropertyStatus $rentStatus;
    protected Location $palmLocation;
    protected Location $marinaLocation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->villaType = PropertyType::firstOrCreate(['slug' => 'villa'], ['name' => 'Villa', 'is_active' => true]);
        $this->penthouseType = PropertyType::firstOrCreate(['slug' => 'penthouse'], ['name' => 'Penthouse', 'is_active' => true]);

        $this->saleStatus = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $this->rentStatus = PropertyStatus::firstOrCreate(['slug' => 'for-rent'], ['name' => 'For Rent', 'color_code' => '#3B82F6', 'is_system_default' => false]);

        $this->palmLocation = Location::firstOrCreate(['slug' => 'palm-jumeirah'], ['name' => 'Palm Jumeirah', 'is_active' => true]);
        $this->marinaLocation = Location::firstOrCreate(['slug' => 'dubai-marina'], ['name' => 'Dubai Marina', 'is_active' => true]);
    }

    public function test_for_sale_page_renders_successfully(): void
    {
        $response = $this->get(route('sales'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PropertyListing::class);
        $response->assertSee('Properties For Sale');
    }

    public function test_for_rent_page_renders_successfully(): void
    {
        $response = $this->get(route('rentals'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PropertyListing::class);
        $response->assertSee('Properties For Rent');
    }

    public function test_listing_filters_by_sale_status_prop(): void
    {
        // 1 Sale Property
        Property::create([
            'title' => 'Palm Crown Mansion',
            'slug' => 'palm-crown-mansion',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->saleStatus->id,
            'location_id' => $this->palmLocation->id,
            'price' => 15000000,
            'bedrooms' => 5,
            'bathrooms' => 6,
            'sqft' => 8000,
            'is_active' => true,
        ]);

        // 1 Rent Property
        Property::create([
            'title' => 'Marina Sky Residence',
            'slug' => 'marina-sky-residence',
            'property_type_id' => $this->penthouseType->id,
            'property_status_id' => $this->rentStatus->id,
            'location_id' => $this->marinaLocation->id,
            'price' => 250000,
            'price_label' => 'year',
            'bedrooms' => 3,
            'bathrooms' => 3,
            'sqft' => 2400,
            'is_active' => true,
        ]);

        // When listing status="sale"
        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->assertSee('Palm Crown Mansion')
            ->assertDontSee('Marina Sky Residence');

        // When listing status="rent"
        Livewire::test(PropertyListing::class, ['status' => 'rent'])
            ->assertSee('Marina Sky Residence')
            ->assertDontSee('Palm Crown Mansion');
    }

    public function test_listing_filters_by_search_location_and_bedrooms(): void
    {
        $propertyA = Property::create([
            'title' => 'Royal Palm Villa',
            'slug' => 'royal-palm-villa',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->saleStatus->id,
            'location_id' => $this->palmLocation->id,
            'price' => 12000000,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'sqft' => 6000,
            'is_active' => true,
        ]);

        $propertyB = Property::create([
            'title' => 'Ocean Crest Estate',
            'slug' => 'ocean-crest-estate',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->saleStatus->id,
            'location_id' => $this->marinaLocation->id,
            'price' => 8500000,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'sqft' => 3000,
            'is_active' => true,
        ]);

        // Keyword filter
        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->set('search', 'Royal')
            ->assertSee('Royal Palm Villa')
            ->assertDontSee('Ocean Crest Estate');

        // Location filter
        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->set('location_id', (string) $this->marinaLocation->id)
            ->assertSee('Ocean Crest Estate')
            ->assertDontSee('Royal Palm Villa');

        // Bedrooms filter
        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->set('bedrooms', '4')
            ->assertSee('Royal Palm Villa')
            ->assertDontSee('Ocean Crest Estate');

        // Reset filter
        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->set('search', 'Royal')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSee('Royal Palm Villa')
            ->assertSee('Ocean Crest Estate');
    }

    public function test_listing_filters_by_price_range(): void
    {
        Property::create([
            'title' => 'Mid Tier Villa',
            'slug' => 'mid-tier-villa',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->saleStatus->id,
            'location_id' => $this->palmLocation->id,
            'price' => 5000000,
            'is_active' => true,
        ]);

        Property::create([
            'title' => 'Mega Mansion',
            'slug' => 'mega-mansion',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->saleStatus->id,
            'location_id' => $this->palmLocation->id,
            'price' => 25000000,
            'is_active' => true,
        ]);

        Livewire::test(PropertyListing::class, ['status' => 'sale'])
            ->set('max_price', 10000000)
            ->assertSee('Mid Tier Villa')
            ->assertDontSee('Mega Mansion');
    }
}

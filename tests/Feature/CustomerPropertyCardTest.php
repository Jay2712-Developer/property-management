<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CustomerPropertyCardTest extends TestCase
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

    public function test_property_card_renders_for_sale_property_correctly(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'penthouse'], ['name' => 'Penthouse', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['name' => 'For Sale'], ['color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'downtown-dubai'], ['name' => 'Downtown Dubai', 'is_active' => true]);

        $property = Property::create([
            'title' => 'Skyline Luxury Penthouse',
            'slug' => 'skyline-luxury-penthouse',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 7850000,
            'bedrooms' => 4,
            'bathrooms' => 5,
            'sqft' => 5200,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $rendered = Blade::render('<x-customer.property-card :property="$property" />', ['property' => $property]);

        // 1. Link check: wrapped entirely in <a> tag linking to property detail page
        $expectedUrl = route('property.show', $property->id);
        $this->assertStringContainsString('<a', $rendered);
        $this->assertStringContainsString('href="' . $expectedUrl . '"', $rendered);

        // 2. Image hover zoom effect
        $this->assertStringContainsString('group-hover:scale-110', $rendered);

        // 3. Badges: For Sale is Orange
        $this->assertStringContainsString('bg-[#FF6B35]', $rendered);
        $this->assertStringContainsString('For Sale', $rendered);
        $this->assertStringContainsString('Penthouse', $rendered);

        // 4. Bottom Right Price on image
        $this->assertStringContainsString('7,850,000', $rendered);

        // 5. Content: Title and Location with icon
        $this->assertStringContainsString('Skyline Luxury Penthouse', $rendered);
        $this->assertStringContainsString('Downtown Dubai', $rendered);
        $this->assertStringContainsString('fa-location-dot', $rendered);

        // 6. Bottom row: Beds, Baths, Sqft with icons
        $this->assertStringContainsString('fa-bed', $rendered);
        $this->assertStringContainsString('4', $rendered);
        $this->assertStringContainsString('Beds', $rendered);

        $this->assertStringContainsString('fa-bath', $rendered);
        $this->assertStringContainsString('5', $rendered);
        $this->assertStringContainsString('Baths', $rendered);

        $this->assertStringContainsString('fa-vector-square', $rendered);
        $this->assertStringContainsString('5,200', $rendered);
        $this->assertStringContainsString('Sqft', $rendered);

        // 7. Styling & Dark Mode & Lift up hover effect
        $this->assertStringContainsString('bg-white', $rendered);
        $this->assertStringContainsString('dark:bg-[#1A1A1A]', $rendered);
        $this->assertStringContainsString('dark:border-gray-800', $rendered);
        $this->assertStringContainsString('hover:-translate-y-2', $rendered);
        $this->assertStringContainsString('hover:shadow-2xl', $rendered);
    }

    public function test_property_card_renders_for_rent_badge_as_black(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'apartment'], ['name' => 'Apartment', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['name' => 'For Rent'], ['color_code' => '#3B82F6', 'is_system_default' => false]);
        $location = Location::firstOrCreate(['slug' => 'marina'], ['name' => 'Dubai Marina', 'is_active' => true]);

        $property = Property::create([
            'title' => 'Waterfront Marina Flat',
            'slug' => 'waterfront-marina-flat',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 180000,
            'price_label' => 'year',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'sqft' => 1400,
            'is_active' => true,
        ]);

        $rendered = Blade::render('<x-customer.property-card :property="$property" />', ['property' => $property]);

        // For Rent status badge should be Black bg-[#1A1A1A]
        $this->assertStringContainsString('bg-[#1A1A1A]', $rendered);
        $this->assertStringContainsString('For Rent', $rendered);
        $this->assertStringContainsString('/ year', $rendered);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageProperties;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManagePropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $unauthorizedUser;
    protected PropertyType $houseType;
    protected PropertyType $villaType;
    protected PropertyStatus $forSale;
    protected PropertyStatus $forRent;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_list@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_list@tishaproperty.com',
            'is_active' => true,
        ]);
        // No roles assigned

        $this->houseType = PropertyType::firstOrCreate(
            ['slug' => 'luxury-house'],
            ['name' => 'Luxury House', 'is_active' => true]
        );

        $this->villaType = PropertyType::firstOrCreate(
            ['slug' => 'modern-villa'],
            ['name' => 'Modern Villa', 'is_active' => true]
        );

        $this->forSale = PropertyStatus::firstOrCreate(
            ['slug' => 'for-sale'],
            ['name' => 'For Sale', 'color_code' => '#10B981']
        );

        $this->forRent = PropertyStatus::firstOrCreate(
            ['slug' => 'for-rent'],
            ['name' => 'For Rent', 'color_code' => '#3B82F6']
        );

        $this->location = Location::firstOrCreate(
            ['slug' => 'palm-jumeirah'],
            ['name' => 'Palm Jumeirah', 'is_active' => true]
        );
    }

    public function test_unauthorized_user_cannot_access_property_listing(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.properties.index'))
            ->assertStatus(403);
    }

    public function test_authorized_admin_can_render_property_listing(): void
    {
        Property::create([
            'title' => 'Coastal Luxury Villa',
            'slug' => 'coastal-luxury-villa',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 3200000,
            'bedrooms' => 4,
            'bathrooms' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.properties.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageProperties::class)
            ->assertSee('Property Portfolio')
            ->assertSee('Coastal Luxury Villa')
            ->assertSee('$3,200,000');
    }

    public function test_search_filter_filters_properties_by_title(): void
    {
        Property::create([
            'title' => 'Downtown Modern Penthouse',
            'slug' => 'downtown-modern-penthouse',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 1500000,
        ]);

        Property::create([
            'title' => 'Suburban Family Residence',
            'slug' => 'suburban-family-residence',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forRent->id,
            'location_id' => $this->location->id,
            'price' => 4500,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->set('search', 'Downtown')
            ->assertSee('Downtown Modern Penthouse')
            ->assertDontSee('Suburban Family Residence');
    }

    public function test_dropdown_filters_work_correctly(): void
    {
        Property::create([
            'title' => 'Villa A',
            'slug' => 'villa-a',
            'property_type_id' => $this->villaType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 2000000,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Property::create([
            'title' => 'House B',
            'slug' => 'house-b',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forRent->id,
            'location_id' => $this->location->id,
            'price' => 600000,
            'is_featured' => false,
            'is_active' => false,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->set('typeFilter', (string) $this->villaType->id)
            ->assertSee('Villa A')
            ->assertDontSee('House B')
            ->set('typeFilter', '')
            ->set('featuredFilter', '1')
            ->assertSee('Villa A')
            ->assertDontSee('House B')
            ->set('featuredFilter', '')
            ->set('activeFilter', '0')
            ->assertSee('House B')
            ->assertDontSee('Villa A');
    }

    public function test_can_toggle_property_active_status(): void
    {
        $property = Property::create([
            'title' => 'Toggleable Listing',
            'slug' => 'toggleable-listing',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 800000,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->call('toggleActive', $property->hashid);

        $this->assertFalse($property->fresh()->is_active);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->call('toggleActive', $property->hashid);

        $this->assertTrue($property->fresh()->is_active);
    }

    public function test_can_delete_property_via_modal_confirmation(): void
    {
        Storage::fake('public');

        $property = Property::create([
            'title' => 'Property To Delete',
            'slug' => 'property-to-delete',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 500000,
        ]);

        $img = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/image-to-delete.jpg',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        Storage::disk('public')->put($img->image_path, 'fake image content');

        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->call('confirmDelete', $property->hashid)
            ->assertSet('showDeleteModal', true)
            ->assertSet('propertyToDeleteId', $property->id)
            ->call('deleteProperty')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('property_images', ['id' => $img->id]);
        Storage::disk('public')->assertMissing($img->image_path);
    }

    public function test_bulk_actions_delete_and_toggle_active(): void
    {
        Storage::fake('public');

        $prop1 = Property::create([
            'title' => 'Bulk Prop 1',
            'slug' => 'bulk-prop-1',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 400000,
            'is_active' => true,
        ]);

        $prop2 = Property::create([
            'title' => 'Bulk Prop 2',
            'slug' => 'bulk-prop-2',
            'property_type_id' => $this->houseType->id,
            'property_status_id' => $this->forSale->id,
            'location_id' => $this->location->id,
            'price' => 550000,
            'is_active' => true,
        ]);

        // Test bulk toggle active
        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->set('selectedProperties', [(string) $prop1->id, (string) $prop2->id])
            ->call('confirmBulkAction', 'toggle_active')
            ->assertSet('showBulkModal', true)
            ->assertSet('bulkAction', 'toggle_active')
            ->call('executeBulkAction')
            ->assertSet('showBulkModal', false)
            ->assertSet('selectedProperties', []);

        $this->assertFalse($prop1->fresh()->is_active);
        $this->assertFalse($prop2->fresh()->is_active);

        // Test bulk delete
        Livewire::actingAs($this->superAdmin)
            ->test(ManageProperties::class)
            ->set('selectedProperties', [(string) $prop1->id, (string) $prop2->id])
            ->call('confirmBulkAction', 'delete')
            ->assertSet('showBulkModal', true)
            ->assertSet('bulkAction', 'delete')
            ->call('executeBulkAction');

        $this->assertDatabaseMissing('properties', ['id' => $prop1->id]);
        $this->assertDatabaseMissing('properties', ['id' => $prop2->id]);
    }
}

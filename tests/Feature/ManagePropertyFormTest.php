<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManagePropertyForm;
use App\Models\ActivityLog;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagePropertyFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $viewerUser;
    protected PropertyType $type;
    protected PropertyStatus $status;
    protected Location $location;
    protected Amenity $amenity1;
    protected Amenity $amenity2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_prop@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->viewerUser = User::factory()->create([
            'email' => 'viewer_prop@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->viewerUser->assignRole('Viewer');

        $this->type = PropertyType::firstOrCreate(
            ['slug' => 'luxury-villa'],
            ['name' => 'Luxury Villa', 'is_active' => true]
        );

        $this->status = PropertyStatus::firstOrCreate(
            ['slug' => 'for-sale'],
            ['name' => 'For Sale', 'color_code' => '#10B981']
        );

        $this->location = Location::firstOrCreate(
            ['slug' => 'beverly-hills'],
            ['name' => 'Beverly Hills', 'is_active' => true]
        );

        $this->amenity1 = Amenity::firstOrCreate(
            ['name' => 'Private Swimming Pool'],
            ['category' => 'Exterior', 'sort_order' => 1, 'is_active' => true]
        );

        $this->amenity2 = Amenity::firstOrCreate(
            ['name' => 'Smart Home Automation'],
            ['category' => 'Interior', 'sort_order' => 2, 'is_active' => true]
        );
    }

    public function test_unauthorized_user_cannot_access_property_create(): void
    {
        $this->actingAs($this->viewerUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.properties.create'))
            ->assertStatus(403);
    }

    public function test_authorized_admin_can_render_property_create_form(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.properties.create'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManagePropertyForm::class)
            ->assertSee('Create New Property')
            ->assertSee('Basic Information')
            ->assertSee('Pricing &amp; Key Specifications', false);
    }

    public function test_slug_is_automatically_generated_from_title(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyForm::class)
            ->set('title', 'Spectacular Oceanfront Villa')
            ->assertSet('slug', 'spectacular-oceanfront-villa');
    }

    public function test_can_create_property_with_specs_amenities_and_image_upload(): void
    {
        Storage::fake('public');

        $imageFile1 = UploadedFile::fake()->image('villa-front.jpg');
        $imageFile2 = UploadedFile::fake()->image('villa-interior.jpg');

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyForm::class)
            ->set('title', 'Bel Air Panoramic Mansion')
            ->set('slug', 'bel-air-panoramic-mansion')
            ->set('description', 'Spectacular view property in Prime Bel Air location.')
            ->set('property_type_id', $this->type->id)
            ->set('property_status_id', $this->status->id)
            ->set('location_id', $this->location->id)
            ->set('price', 4850000)
            ->set('price_label', 'Guide Price')
            ->set('bedrooms', 5)
            ->set('bathrooms', 6)
            ->set('sqft', 6500)
            ->set('garage', 3)
            ->set('year_built', 2024)
            ->set('is_featured', true)
            ->set('is_active', true)
            ->set('selectedAmenities', [$this->amenity1->id, $this->amenity2->id])
            ->set('newImages', [$imageFile1, $imageFile2])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('properties', [
            'title' => 'Bel Air Panoramic Mansion',
            'slug' => 'bel-air-panoramic-mansion',
            'bedrooms' => 5,
            'bathrooms' => 6,
            'is_featured' => true,
        ]);

        $property = Property::where('slug', 'bel-air-panoramic-mansion')->first();
        $this->assertNotNull($property);
        $this->assertCount(2, $property->amenities);
        $this->assertCount(2, $property->images);

        // Check first image is marked primary
        $primaryImage = $property->images()->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Properties',
            'record_id' => $property->id,
        ]);
    }

    public function test_can_edit_existing_property_using_hashid(): void
    {
        Storage::fake('public');

        $property = Property::create([
            'title' => 'Original Mountain Retreat',
            'slug' => 'original-mountain-retreat',
            'description' => 'Quiet cabin in the woods.',
            'property_type_id' => $this->type->id,
            'property_status_id' => $this->status->id,
            'location_id' => $this->location->id,
            'price' => 750000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'sqft' => 1800,
            'garage' => 1,
            'year_built' => 2018,
            'is_featured' => false,
            'is_active' => true,
        ]);

        $property->amenities()->attach([$this->amenity1->id]);

        $image = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/dummy.jpg',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyForm::class, ['propertyId' => $property->hashid])
            ->assertSet('title', 'Original Mountain Retreat')
            ->assertSet('price', 750000.0)
            ->assertSet('bedrooms', 3)
            ->assertSet('selectedAmenities', [$this->amenity1->id])
            ->assertCount('existingImages', 1)
            ->set('title', 'Renovated Luxury Mountain Retreat')
            ->set('price', 890000)
            ->set('bedrooms', 4)
            ->set('selectedAmenities', [$this->amenity1->id, $this->amenity2->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Renovated Luxury Mountain Retreat',
            'price' => 890000,
            'bedrooms' => 4,
        ]);

        $this->assertCount(2, $property->fresh()->amenities);
    }

    public function test_can_remove_existing_image_and_toggle_primary(): void
    {
        Storage::fake('public');

        $property = Property::create([
            'title' => 'Sample Image Property',
            'slug' => 'sample-image-property',
            'property_type_id' => $this->type->id,
            'property_status_id' => $this->status->id,
            'location_id' => $this->location->id,
            'price' => 500000,
            'bedrooms' => 2,
            'bathrooms' => 1,
        ]);

        $img1 = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/test1.jpg',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $img2 = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/test2.jpg',
            'sort_order' => 1,
            'is_primary' => false,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyForm::class, ['propertyId' => $property->hashid])
            ->call('setAsPrimary', $img2->id);

        $this->assertTrue($img2->fresh()->is_primary);
        $this->assertFalse($img1->fresh()->is_primary);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyForm::class, ['propertyId' => $property->hashid])
            ->call('removeExistingImage', $img2->id);

        $this->assertDatabaseMissing('property_images', ['id' => $img2->id]);
    }
}

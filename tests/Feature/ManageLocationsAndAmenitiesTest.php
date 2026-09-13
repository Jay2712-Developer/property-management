<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageAmenities;
use App\Livewire\Admin\ManageLocations;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageLocationsAndAmenitiesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->superAdmin = User::factory()->create([
            'email' => 'admin_loc@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_loc@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_unauthorized_user_cannot_access_locations_or_amenities(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.locations.index'))
            ->assertStatus(403);

        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.amenities.index'))
            ->assertStatus(403);
    }

    public function test_authorized_admin_can_render_locations_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.locations.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageLocations::class)
            ->assertSee('Location Hierarchy');
    }

    public function test_can_create_root_and_child_locations_with_unique_per_parent_rule(): void
    {
        // 1. Create root city
        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->set('name', 'Dubai')
            ->set('parent_id', null)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $dubai = Location::where('name', 'Dubai')->whereNull('parent_id')->first();
        $this->assertNotNull($dubai);

        // 2. Create sub-area under Dubai
        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->set('name', 'Marina')
            ->set('parent_id', $dubai->id)
            ->call('save')
            ->assertHasNoErrors();

        $marina = Location::where('name', 'Marina')->where('parent_id', $dubai->id)->first();
        $this->assertNotNull($marina);

        // 3. Duplicate Marina under Dubai should fail validation (unique per parent)
        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->set('name', 'Marina')
            ->set('parent_id', $dubai->id)
            ->call('save')
            ->assertHasErrors(['name']);

        // 4. Create Marina under a different parent (Abu Dhabi) should pass
        $abuDhabi = Location::create(['name' => 'Abu Dhabi', 'slug' => 'abu-dhabi', 'parent_id' => null]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->set('name', 'Marina')
            ->set('parent_id', $abuDhabi->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('locations', [
            'name' => 'Marina',
            'parent_id' => $abuDhabi->id,
        ]);
    }

    public function test_parent_dropdown_excludes_current_location_when_editing_to_prevent_cycles(): void
    {
        $city = Location::create(['name' => 'Miami', 'slug' => 'miami']);
        $subArea = Location::create(['name' => 'South Beach', 'slug' => 'south-beach', 'parent_id' => $city->id]);

        $component = Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->call('editLocation', $city->hashid);

        $parentOptions = $component->get('parentOptions');

        // Miami itself and its child South Beach must NOT be in the options
        $this->assertFalse($parentOptions->contains('id', $city->id));
        $this->assertFalse($parentOptions->contains('id', $subArea->id));
    }

    public function test_can_toggle_location_active_status(): void
    {
        $loc = Location::create(['name' => 'Paris', 'slug' => 'paris', 'is_active' => true]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->call('toggleActive', $loc->hashid);

        $this->assertFalse($loc->fresh()->is_active);
    }

    public function test_cannot_delete_location_with_sub_areas_or_properties(): void
    {
        $parent = Location::create(['name' => 'London', 'slug' => 'london']);
        $child = Location::create(['name' => 'Mayfair', 'slug' => 'mayfair', 'parent_id' => $parent->id]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->call('confirmDelete', $parent->hashid)
            ->call('deleteLocation');

        // Parent should still exist because child exists
        $this->assertDatabaseHas('locations', ['id' => $parent->id]);
    }

    public function test_can_delete_location_without_dependencies(): void
    {
        $loc = Location::create(['name' => 'Temporary Area', 'slug' => 'temporary-area']);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageLocations::class)
            ->call('confirmDelete', $loc->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteLocation')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('locations', ['id' => $loc->id]);
    }

    public function test_authorized_admin_can_render_amenities_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.amenities.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageAmenities::class)
            ->assertSee('Property Amenities');
    }

    public function test_can_create_and_edit_amenity(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageAmenities::class)
            ->set('name', 'Infinity Rooftop Pool')
            ->set('icon', 'fa-solid fa-water-ladder')
            ->set('category', 'Exterior')
            ->set('sort_order', 5)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('amenities', [
            'name' => 'Infinity Rooftop Pool',
            'category' => 'Exterior',
            'sort_order' => 5,
        ]);

        $amenity = Amenity::where('name', 'Infinity Rooftop Pool')->first();

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAmenities::class)
            ->call('editAmenity', $amenity->hashid)
            ->assertSet('name', 'Infinity Rooftop Pool')
            ->set('name', 'Heated Rooftop Pool')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('amenities', [
            'id' => $amenity->id,
            'name' => 'Heated Rooftop Pool',
        ]);
    }

    public function test_can_toggle_amenity_active_status_and_delete(): void
    {
        $amenity = Amenity::create([
            'name' => 'Private Elevator',
            'icon' => 'fa-solid fa-elevator',
            'category' => 'Community',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAmenities::class)
            ->call('toggleActive', $amenity->hashid);

        $this->assertFalse($amenity->fresh()->is_active);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAmenities::class)
            ->call('confirmDelete', $amenity->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteAmenity')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('amenities', ['id' => $amenity->id]);
    }
}

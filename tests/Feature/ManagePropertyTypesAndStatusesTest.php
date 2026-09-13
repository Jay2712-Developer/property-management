<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManagePropertyStatuses;
use App\Livewire\Admin\ManagePropertyTypes;
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

class ManagePropertyTypesAndStatusesTest extends TestCase
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
            'email' => 'admin_types@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_types@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_unauthorized_user_cannot_access_property_types_or_statuses(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.property-types.index'))
            ->assertStatus(403);

        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.property-statuses.index'))
            ->assertStatus(403);
    }

    public function test_authorized_admin_can_render_property_types_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.property-types.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManagePropertyTypes::class)
            ->assertSee('Property Types');
    }

    public function test_can_create_property_type_with_unique_name(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->set('name', 'Bungalow')
            ->set('icon', 'fa-solid fa-house')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('property_types', [
            'name' => 'Bungalow',
            'slug' => 'bungalow',
            'is_active' => true,
        ]);

        // Validate name unique
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->set('name', 'Bungalow')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_can_edit_property_type_using_hashid(): void
    {
        $type = PropertyType::create([
            'name' => 'Original Chalet',
            'slug' => 'original-chalet',
            'icon' => 'fa-solid fa-hotel',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->call('editType', $type->hashid)
            ->assertSet('isEditing', true)
            ->assertSet('name', 'Original Chalet')
            ->set('name', 'Alpine Chalet')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('property_types', [
            'id' => $type->id,
            'name' => 'Alpine Chalet',
            'slug' => 'alpine-chalet',
        ]);
    }

    public function test_can_toggle_property_type_active_status(): void
    {
        $type = PropertyType::create([
            'name' => 'Duplex Penthouse',
            'slug' => 'duplex-penthouse',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->call('toggleActive', $type->hashid);

        $this->assertFalse($type->fresh()->is_active);
    }

    public function test_can_delete_property_type_with_modal_confirmation(): void
    {
        $type = PropertyType::create([
            'name' => 'Temporary Type',
            'slug' => 'temporary-type',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->call('confirmDelete', $type->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteType')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('property_types', ['id' => $type->id]);
    }

    public function test_cannot_delete_property_type_with_assigned_properties(): void
    {
        $type = PropertyType::create([
            'name' => 'Mansion',
            'slug' => 'mansion',
            'is_active' => true,
        ]);

        $status = PropertyStatus::first();
        $location = Location::firstOrCreate(['slug' => 'test-loc'], ['name' => 'Test Loc', 'is_active' => true]);

        Property::create([
            'title' => 'Sample Mansion',
            'slug' => 'sample-mansion',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 1000000,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyTypes::class)
            ->call('confirmDelete', $type->hashid)
            ->call('deleteType');

        // Should not be deleted
        $this->assertDatabaseHas('property_types', ['id' => $type->id]);
    }

    public function test_authorized_admin_can_render_property_statuses_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.property-statuses.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManagePropertyStatuses::class)
            ->assertSee('Property Statuses');
    }

    public function test_can_create_property_status_with_color_picker(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyStatuses::class)
            ->set('name', 'Under Offer')
            ->set('color_code', '#F59E0B')
            ->set('is_system_default', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('property_statuses', [
            'name' => 'Under Offer',
            'slug' => 'under-offer',
            'color_code' => '#F59E0B',
            'is_system_default' => false,
        ]);
    }

    public function test_system_default_status_cannot_be_deleted(): void
    {
        $defaultStatus = PropertyStatus::where('is_system_default', true)->first();
        $this->assertNotNull($defaultStatus);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyStatuses::class)
            ->call('confirmDelete', $defaultStatus->hashid)
            ->assertSet('showDeleteModal', false); // Should not open delete modal

        $this->assertDatabaseHas('property_statuses', ['id' => $defaultStatus->id]);
    }

    public function test_can_delete_custom_property_status(): void
    {
        $customStatus = PropertyStatus::create([
            'name' => 'Off Market Custom',
            'slug' => 'off-market-custom',
            'color_code' => '#6B7280',
            'is_system_default' => false,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePropertyStatuses::class)
            ->call('confirmDelete', $customStatus->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteStatus')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('property_statuses', ['id' => $customStatus->id]);
    }
}

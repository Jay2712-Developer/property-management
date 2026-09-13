<?php

namespace Tests\Feature;

use App\Facades\HashidsHelper;
use App\Livewire\Admin\ManageRoles;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManageRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_roles_management_page_can_be_rendered_by_authorized_user(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.roles'))
            ->assertStatus(200)
            ->assertSee('Roles & Permissions')
            ->assertSee('Create New Role')
            ->assertSee('Super Admin')
            ->assertSee('Manager')
            ->assertSee('Agent')
            ->assertSee('Viewer');
    }

    public function test_super_admin_role_has_no_edit_or_delete_buttons_and_is_protected(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $superAdminRole = Role::findByName('Super Admin');
        $hashid = HashidsHelper::forModel(Role::class)->encode($superAdminRole->id);

        Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->assertSee('System Protected')
            // Attempting to trigger edit or delete directly on Super Admin should be blocked
            ->call('editRole', $hashid)
            ->assertSet('showRoleModal', false)
            ->call('confirmDelete', $hashid)
            ->assertSet('showDeleteModal', false);
    }

    public function test_can_create_a_new_role_with_dynamic_permissions(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $permissionsToGrant = ['view_properties', 'create_properties', 'view_inquiries'];

        Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->call('createRole')
            ->assertSet('showRoleModal', true)
            ->set('name', 'Listing Coordinator')
            ->set('selectedPermissions', $permissionsToGrant)
            ->call('saveRole')
            ->assertHasNoErrors()
            ->assertSet('showRoleModal', false);

        $createdRole = Role::where('name', 'Listing Coordinator')->first();
        $this->assertNotNull($createdRole);
        $this->assertEqualsCanonicalizing(
            $permissionsToGrant,
            $createdRole->permissions->pluck('name')->toArray()
        );
    }

    public function test_can_toggle_module_group_permissions_selection(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $component = Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->call('createRole');

        // Toggle Properties group
        $component->call('toggleGroupSelect', 'Properties');

        // All properties permissions should be selected
        $propertyPermissions = Permission::where('name', 'like', '%propert%')->pluck('name')->toArray();
        foreach ($propertyPermissions as $perm) {
            $this->assertContains($perm, $component->get('selectedPermissions'));
        }

        // Toggle again to deselect
        $component->call('toggleGroupSelect', 'Properties');
        foreach ($propertyPermissions as $perm) {
            $this->assertNotContains($perm, $component->get('selectedPermissions'));
        }
    }

    public function test_can_edit_existing_role_and_sync_permissions(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        // Create a custom role to edit
        $customRole = Role::create(['name' => 'Junior Realtor', 'guard_name' => 'web']);
        $customRole->syncPermissions(['view_properties']);
        $hashid = HashidsHelper::forModel(Role::class)->encode($customRole->id);

        $newPermissions = ['view_properties', 'create_properties', 'view_visits'];

        Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->call('editRole', $hashid)
            ->assertSet('showRoleModal', true)
            ->assertSet('name', 'Junior Realtor')
            ->set('name', 'Associate Realtor')
            ->set('selectedPermissions', $newPermissions)
            ->call('saveRole')
            ->assertHasNoErrors()
            ->assertSet('showRoleModal', false);

        $customRole->refresh();
        $this->assertEquals('Associate Realtor', $customRole->name);
        $this->assertEqualsCanonicalizing(
            $newPermissions,
            $customRole->permissions->pluck('name')->toArray()
        );
    }

    public function test_can_delete_role_using_hashid(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $roleToDelete = Role::create(['name' => 'Temporary Role', 'guard_name' => 'web']);
        $hashid = HashidsHelper::forModel(Role::class)->encode($roleToDelete->id);

        Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->call('confirmDelete', $hashid)
            ->assertSet('showDeleteModal', true)
            ->assertSet('roleToDeleteName', 'Temporary Role')
            ->call('deleteRole')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('roles', ['name' => 'Temporary Role']);
    }

    public function test_role_name_is_required_and_must_be_unique(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(ManageRoles::class)
            ->call('createRole')
            ->set('name', '')
            ->call('saveRole')
            ->assertHasErrors(['name' => 'required'])
            ->set('name', 'Manager') // Existing role from seeder
            ->call('saveRole')
            ->assertHasErrors(['name' => 'unique']);
    }

    public function test_unauthorized_user_cannot_access_roles_management(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('Agent');

        $this->actingAs($agent)
            ->get(route('admin.roles'))
            ->assertStatus(403);
    }
}

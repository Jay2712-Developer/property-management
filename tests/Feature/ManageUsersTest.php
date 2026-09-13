<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageUsers;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_users_management_page_can_be_rendered_by_authorized_user(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Harrison Ford',
            'email' => 'harrison@tisharealty.com',
        ]);
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.users'))
            ->assertStatus(200)
            ->assertSee('User Management')
            ->assertSee('Create New User')
            ->assertSee('Harrison Ford')
            ->assertSee('harrison@tisharealty.com')
            ->assertSee('Super Admin');
    }

    public function test_can_create_a_new_user_with_roles(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('createUser')
            ->assertSet('showUserModal', true)
            ->set('name', 'Evelyn Cross')
            ->set('email', 'evelyn@tisharealty.com')
            ->set('password', 'Secret123!')
            ->set('password_confirmation', 'Secret123!')
            ->set('selectedRoles', ['Manager', 'Agent'])
            ->call('saveUser')
            ->assertHasNoErrors()
            ->assertSet('showUserModal', false);

        $newUser = User::where('email', 'evelyn@tisharealty.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('Evelyn Cross', $newUser->name);
        $this->assertTrue(Hash::check('Secret123!', $newUser->password));
        $this->assertTrue($newUser->hasRole('Manager'));
        $this->assertTrue($newUser->hasRole('Agent'));
    }

    public function test_can_edit_existing_user_without_overwriting_password_if_blank(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $userToEdit = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@tisharealty.com',
            'password' => Hash::make('OriginalPassword123!'),
        ]);
        $userToEdit->assignRole('Agent');

        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('editUser', $userToEdit->hashid)
            ->assertSet('showUserModal', true)
            ->assertSet('name', 'Original Name')
            ->set('name', 'Updated Name')
            ->set('password', '') // Left blank to keep existing password
            ->set('password_confirmation', '')
            ->set('selectedRoles', ['Manager'])
            ->call('saveUser')
            ->assertHasNoErrors()
            ->assertSet('showUserModal', false);

        $userToEdit->refresh();
        $this->assertEquals('Updated Name', $userToEdit->name);
        // Password must remain original password
        $this->assertTrue(Hash::check('OriginalPassword123!', $userToEdit->password));
        $this->assertTrue($userToEdit->hasRole('Manager'));
        $this->assertFalse($userToEdit->hasRole('Agent'));
    }

    public function test_security_rule_user_cannot_delete_their_own_account(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('confirmDelete', $superAdmin->hashid)
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_security_rule_cannot_remove_super_admin_role_from_the_last_remaining_super_admin(): void
    {
        // Only 1 Super Admin in database
        $lastSuperAdmin = User::factory()->create();
        $lastSuperAdmin->assignRole('Super Admin');

        $this->assertEquals(1, User::role('Super Admin')->count());

        Livewire::actingAs($lastSuperAdmin)
            ->test(ManageUsers::class)
            ->call('editUser', $lastSuperAdmin->hashid)
            // Attempt to remove Super Admin and assign only Manager
            ->set('selectedRoles', ['Manager'])
            ->call('saveUser')
            ->assertHasErrors(['selectedRoles']);

        $lastSuperAdmin->refresh();
        $this->assertTrue($lastSuperAdmin->hasRole('Super Admin'));
    }

    public function test_security_rule_only_super_admin_can_assign_super_admin_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin'); // Admin role, NOT Super Admin

        $targetUser = User::factory()->create();
        $targetUser->assignRole('Agent');

        // Admin attempts to grant Super Admin role to targetUser
        Livewire::actingAs($admin)
            ->test(ManageUsers::class)
            ->call('editUser', $targetUser->hashid)
            ->set('selectedRoles', ['Super Admin', 'Agent'])
            ->call('saveUser')
            ->assertHasErrors(['selectedRoles']);

        $targetUser->refresh();
        $this->assertFalse($targetUser->hasRole('Super Admin'));
    }

    public function test_can_toggle_user_active_status(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $testUser = User::factory()->create(['is_active' => true]);
        $testUser->assignRole('Agent');

        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('toggleUserStatus', $testUser->hashid);

        $testUser->refresh();
        $this->assertFalse($testUser->is_active);

        // Toggle back
        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('toggleUserStatus', $testUser->hashid);

        $testUser->refresh();
        $this->assertTrue($testUser->is_active);
    }

    public function test_cannot_deactivate_own_account(): void
    {
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(ManageUsers::class)
            ->call('toggleUserStatus', $superAdmin->hashid);

        $superAdmin->refresh();
        $this->assertTrue($superAdmin->is_active);
    }
}

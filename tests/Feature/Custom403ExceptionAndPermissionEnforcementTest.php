<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Custom403ExceptionAndPermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_unauthorized_user_is_served_custom_styled_403_page(): void
    {
        // An Agent role user does NOT have 'manage_roles' permission
        $agent = User::factory()->create();
        $agent->assignRole('Agent');

        $response = $this->actingAs($agent)->get(route('admin.roles'));

        // Assert 403 status
        $response->assertStatus(403);

        // Assert our custom styled 403 page is rendered
        $response->assertSee('HTTP 403');
        $response->assertSee('Access Forbidden');
        $response->assertSee('Permission Denied');
        $response->assertSee('Back to Dashboard');
        $response->assertSee('Current Role:');
        $response->assertSee('Agent');
    }

    public function test_unauthorized_user_cannot_access_users_management_and_receives_styled_403(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $response = $this->actingAs($viewer)->get(route('admin.users'));

        $response->assertStatus(403);
        $response->assertSee('HTTP 403');
        $response->assertSee('Permission Denied');
        $response->assertSee('Viewer');
    }

    public function test_authorized_super_admin_bypasses_and_can_access_all_protected_modules(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.roles'))
            ->assertStatus(200)
            ->assertSee('Roles & Permissions');

        $this->actingAs($superAdmin)
            ->get(route('admin.users'))
            ->assertStatus(200)
            ->assertSee('User Management');
    }

    public function test_json_requests_receive_json_403_with_permission_details(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $response = $this->actingAs($viewer)
            ->getJson(route('admin.roles'));

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message',
                'required_permissions',
            ]);
    }
}

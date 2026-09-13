<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTopbarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_topbar_renders_with_user_details_and_menu_options(): void
    {
        $admin = User::factory()->create([
            'name' => 'Alexander Vance',
            'email' => 'alexander@tisharealty.com',
        ]);
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 1. User Name and Role
        $response->assertSee('Alexander Vance');
        $response->assertSee('Super Admin');
        $response->assertSee('alexander@tisharealty.com');

        // 2. Profile Options
        $response->assertSee('My Profile');
        $response->assertSee('2FA Settings');
        $response->assertSee('Logout');

        // 3. Theme Toggle & Notifications
        $response->assertSee('Toggle theme (Light / Dark Mode)');
        $response->assertSee('Activity Feed');
        $response->assertSee('Live updates');
    }

    public function test_admin_logout_clears_session_and_redirects_to_login_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);
        $this->assertAuthenticatedAs($admin);

        // Post to logout route
        $response = $this->post(route('admin.logout'));

        // Assert redirected to admin login
        $response->assertRedirect(route('admin.login'));

        // Assert user is logged out
        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_super_admin_sees_all_sidebar_menu_items(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Properties');
        $response->assertSee('Agents & Team');
        $response->assertSee('Users & Roles');
        $response->assertSee('Site Settings');
        $response->assertSee('Inquiries & Visits');
        $response->assertSee('Testimonials');
        $response->assertSee('Pages & Media');
    }

    public function test_manager_sees_properties_and_inquiries_but_not_agents_or_settings(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $response = $this->actingAs($manager)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Properties');
        $response->assertSee('Inquiries & Visits');
        $response->assertSee('Testimonials');

        // Manager MUST NOT see Agent management, Roles management, or Site Settings
        $response->assertDontSee('Agents & Team');
        $response->assertDontSee('Users & Roles');
        $response->assertDontSee('Site Settings');
    }

    public function test_agent_does_not_see_agent_management_or_role_management(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('Agent');

        $response = $this->actingAs($agent)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Properties');
        $response->assertSee('Inquiries & Visits');

        // Agent lacks create_properties, manage_agents, manage_roles, manage_settings
        $response->assertDontSee('Add New');
        $response->assertDontSee('Agents & Team');
        $response->assertDontSee('Users & Roles');
        $response->assertDontSee('Site Settings');
    }

    public function test_viewer_has_read_only_limited_sidebar_visibility(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $response = $this->actingAs($viewer)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Properties');
        $response->assertSee('Testimonials');

        $response->assertDontSee('Add New');
        $response->assertDontSee('Agents & Team');
        $response->assertDontSee('Inquiries & Visits');
        $response->assertDontSee('Users & Roles');
        $response->assertDontSee('Site Settings');
    }
}

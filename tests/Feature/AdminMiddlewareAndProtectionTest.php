<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareAndProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_user_without_admin_role_gets_403(): void
    {
        // User with no roles
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_authenticated_user_with_admin_role_can_access_dashboard(): void
    {
        $roles = ['Super Admin', 'Admin', 'Manager', 'Agent', 'Viewer'];

        foreach ($roles as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $response = $this->actingAs($user)->get('/admin/dashboard');
            $response->assertStatus(200);
        }
    }

    public function test_user_with_2fa_enabled_must_verify_in_session_before_access(): void
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'SECRETKEY123456',
        ]);
        $user->assignRole('Admin');

        // Without 2fa.verified in session -> redirected to 2FA challenge
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect(route('admin.two-factor'));

        // With 2fa.verified in session -> allowed access
        $verifiedResponse = $this->actingAs($user)
            ->withSession(['2fa.verified' => true])
            ->get('/admin/dashboard');

        $verifiedResponse->assertStatus(200);
    }

    public function test_spatie_role_and_permission_middleware_aliases_are_registered(): void
    {
        Route::get('/test/role-protected', function () {
            return 'Role granted';
        })->middleware(['auth', 'role:Super Admin']);

        Route::get('/test/permission-protected', function () {
            return 'Permission granted';
        })->middleware(['auth', 'permission:manage_roles']);

        $user = User::factory()->create();
        $user->assignRole('Admin'); // Admin does not have manage_roles

        // Fails role:Super Admin
        $this->actingAs($user)->get('/test/role-protected')->assertStatus(403);

        // Fails permission:manage_roles
        $this->actingAs($user)->get('/test/permission-protected')->assertStatus(403);

        // Super Admin succeeds
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin)->get('/test/role-protected')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/test/permission-protected')->assertStatus(200);
    }
}

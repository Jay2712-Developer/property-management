<?php

namespace Tests\Unit;

use App\Models\Amenity;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_initialize_roles_permissions_superadmin_and_default_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Roles existence
        $superAdminRole = Role::findByName('Super Admin', 'web');
        $adminRole = Role::findByName('Admin', 'web');
        $managerRole = Role::findByName('Manager', 'web');
        $agentRole = Role::findByName('Agent', 'web');
        $viewerRole = Role::findByName('Viewer', 'web');

        $this->assertNotNull($superAdminRole);
        $this->assertNotNull($adminRole);
        $this->assertNotNull($managerRole);
        $this->assertNotNull($agentRole);
        $this->assertNotNull($viewerRole);

        // 2. Permission checks
        $this->assertTrue($superAdminRole->hasPermissionTo('manage_roles'));
        $this->assertTrue($superAdminRole->hasPermissionTo('manage_settings'));

        $this->assertFalse($adminRole->hasPermissionTo('manage_roles'));
        $this->assertFalse($adminRole->hasPermissionTo('manage_settings'));
        $this->assertTrue($adminRole->hasPermissionTo('create_properties'));

        $this->assertTrue($managerRole->hasPermissionTo('create_properties'));
        $this->assertTrue($managerRole->hasPermissionTo('view_inquiries'));
        $this->assertFalse($managerRole->hasPermissionTo('edit_agents'));

        $this->assertTrue($agentRole->hasPermissionTo('view_properties'));
        $this->assertTrue($agentRole->hasPermissionTo('edit_properties'));
        $this->assertFalse($agentRole->hasPermissionTo('delete_properties'));

        $this->assertTrue($viewerRole->hasPermissionTo('view_properties'));
        $this->assertFalse($viewerRole->hasPermissionTo('create_properties'));

        // 3. Super Admin user
        $superAdmin = User::where('email', 'admin@tisha.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('Super Admin'));

        // 4. Default Property Types & Statuses & Amenities
        $this->assertGreaterThanOrEqual(6, PropertyType::count());
        $this->assertGreaterThanOrEqual(4, PropertyStatus::count());
        $this->assertGreaterThanOrEqual(14, Amenity::count());
    }
}

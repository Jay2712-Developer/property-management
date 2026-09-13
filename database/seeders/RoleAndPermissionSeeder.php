<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define permissions grouped by module
        $permissionsByModule = [
            'Properties' => [
                'view_properties',
                'create_properties',
                'edit_properties',
                'delete_properties',
                'manage_properties',
                'view_property_types',
                'create_property_types',
                'edit_property_types',
                'delete_property_types',
                'view_property_statuses',
                'create_property_statuses',
                'edit_property_statuses',
                'delete_property_statuses',
                'view_locations',
                'create_locations',
                'edit_locations',
                'delete_locations',
                'view_amenities',
                'create_amenities',
                'edit_amenities',
                'delete_amenities',
            ],
            'Agents' => [
                'view_agents',
                'create_agents',
                'edit_agents',
                'delete_agents',
                'manage_agents',
            ],
            'Inquiries & Visits' => [
                'view_inquiries',
                'reply_inquiries',
                'delete_inquiries',
                'view_visits',
                'manage_visits',
            ],
            'Content & Testimonials' => [
                'view_testimonials',
                'manage_testimonials',
                'manage_pages',
            ],
            'Administration & System' => [
                'manage_roles',
                'manage_settings',
                'view_activity_logs',
            ],
        ];

        // 3. Create all permissions
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
        }

        // 4. Create Roles and Assign Permissions

        // A. Super Admin: Has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // B. Admin: All permissions EXCEPT manage_roles and manage_settings
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = Permission::whereNotIn('name', ['manage_roles', 'manage_settings'])->get();
        $adminRole->syncPermissions($adminPermissions);

        // C. Manager: Full property CRUD, view inquiries & visits, view agents & testimonials (NO agent editing or settings)
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'view_properties',
            'create_properties',
            'edit_properties',
            'delete_properties',
            'view_property_types',
            'create_property_types',
            'edit_property_types',
            'delete_property_types',
            'view_property_statuses',
            'create_property_statuses',
            'edit_property_statuses',
            'delete_property_statuses',
            'view_locations',
            'create_locations',
            'edit_locations',
            'delete_locations',
            'view_amenities',
            'create_amenities',
            'edit_amenities',
            'delete_amenities',
            'view_inquiries',
            'reply_inquiries',
            'view_visits',
            'manage_visits',
            'view_testimonials',
        ]);

        // D. Agent: View & edit properties (policy handles restricting to own listings), view inquiries & visits
        $agentRole = Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
        $agentRole->syncPermissions([
            'view_properties',
            'edit_properties',
            'view_inquiries',
            'view_visits',
        ]);

        // E. Viewer: Read-only access to properties, agents, and testimonials
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewerRole->syncPermissions([
            'view_properties',
            'view_agents',
            'view_testimonials',
        ]);
    }
}

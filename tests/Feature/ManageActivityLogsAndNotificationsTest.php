<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageActivityLogs;
use App\Models\ActivityLog;
use App\Models\ContactInquiry;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\VisitRequest;
use App\Traits\LogsActivity;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageActivityLogsAndNotificationsTest extends TestCase
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
            'name' => 'Alexander Vance',
            'email' => 'superadmin_logs@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_logs@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* 1. ACTIVITY LOGGING TRAIT / HELPER TESTS                                   */
    /* -------------------------------------------------------------------------- */

    public function test_logs_activity_trait_inserts_records_accurately(): void
    {
        $tester = new class {
            use LogsActivity;
        };

        $this->actingAs($this->superAdmin);

        $log = $tester->logActivity(
            action: 'Created new luxury villa',
            module: 'Property',
            recordId: 42,
            userId: $this->superAdmin->id,
            ipAddress: '192.168.1.100',
            userAgent: 'Mozilla/5.0 TestBrowser'
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'user_id' => $this->superAdmin->id,
            'action' => 'Created new luxury villa',
            'module' => 'Property',
            'record_id' => 42,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 TestBrowser',
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* 2. ACTIVITY LOGS UI TESTS                                                  */
    /* -------------------------------------------------------------------------- */

    public function test_super_admin_can_render_activity_logs_page(): void
    {
        ActivityLog::record('Published new blog article', 'Pages', 10, $this->superAdmin->id);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.activity-logs.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageActivityLogs::class)
            ->assertSee('System &amp; Audit Logs', false)
            ->assertSee('Published new blog article')
            ->assertSee('Alexander Vance');
    }

    public function test_unauthorized_user_cannot_access_activity_logs(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.activity-logs.index'))
            ->assertStatus(403);
    }

    public function test_can_filter_activity_logs_by_user_module_and_search(): void
    {
        $agentUser = User::factory()->create(['name' => 'Agent Smith', 'is_active' => true]);

        ActivityLog::record('Updated beachfront penthouse price', 'Property', 1, $this->superAdmin->id);
        ActivityLog::record('Approved private visit request', 'Visits', 2, $agentUser->id);

        // Search test
        Livewire::actingAs($this->superAdmin)
            ->test(ManageActivityLogs::class)
            ->set('search', 'beachfront')
            ->assertSee('Updated beachfront penthouse price')
            ->assertDontSee('Approved private visit request');

        // Module filter test
        Livewire::actingAs($this->superAdmin)
            ->test(ManageActivityLogs::class)
            ->set('moduleFilter', 'Visits')
            ->assertSee('Approved private visit request')
            ->assertDontSee('Updated beachfront penthouse price');

        // User filter test: user names appear in the select dropdown options,
        // so we verify that the filtered results in the table only contain Agent Smith's action
        Livewire::actingAs($this->superAdmin)
            ->test(ManageActivityLogs::class)
            ->set('userFilter', (string) $agentUser->id)
            ->assertSee('Approved private visit request')
            ->assertDontSee('Updated beachfront penthouse price');

        // Reset filter test
        Livewire::actingAs($this->superAdmin)
            ->test(ManageActivityLogs::class)
            ->set('search', 'something')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('userFilter', '')
            ->assertSet('moduleFilter', '');
    }

    /* -------------------------------------------------------------------------- */
    /* 3. TOPBAR NOTIFICATIONS TESTS                                              */
    /* -------------------------------------------------------------------------- */

    public function test_topbar_renders_accurate_notifications_badge_count(): void
    {
        ContactInquiry::create([
            'name' => 'Prospective Buyer',
            'email' => 'buyer@example.com',
            'subject' => 'Tour Inquiry',
            'message' => 'Interested in the property.',
            'status' => 'new',
        ]);

        $location = Location::firstOrCreate(
            ['slug' => 'downtown-test'],
            ['name' => 'Downtown Test', 'is_active' => true]
        );

        $property = Property::create([
            'title' => 'Sample Villa',
            'slug' => 'sample-villa-' . uniqid(),
            'property_type_id' => PropertyType::first()?->id,
            'property_status_id' => PropertyStatus::first()?->id,
            'location_id' => $location->id,
            'price' => 500000,
            'is_active' => true,
        ]);

        VisitRequest::create([
            'property_id' => $property->id,
            'name' => 'Visit Client',
            'email' => 'visit@example.com',
            'phone' => '+1 555 222 3333',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Total notifications: 1 new inquiry + 1 pending visit = 2
        $response->assertSee('2 Pending');
        $response->assertSee('New Messages');
        $response->assertSee('Pending Tours');
    }
}

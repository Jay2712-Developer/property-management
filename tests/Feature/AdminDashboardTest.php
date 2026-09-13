<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Models\ActivityLog;
use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\User;
use App\Models\VisitRequest;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_dashboard_can_be_rendered_with_stats_and_components(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        // Create sample inquiry
        ContactInquiry::create([
            'name' => 'Sophia Loren',
            'email' => 'sophia@example.com',
            'phone' => '+1 555 0192',
            'subject' => 'Inquiry for Beverly Hills Penthouse',
            'message' => 'I would like to schedule an inspection.',
            'status' => 'new',
        ]);

        // Create sample activity log
        ActivityLog::record('Created luxury listing Villa Riviera', 'Properties', null, $superAdmin->id);

        $response = $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 1. Stats Cards
        $response->assertSee('Total Properties');
        $response->assertSee('Active Listings');
        $response->assertSee('Pending Inquiries');
        $response->assertSee('Scheduled Visits');

        // 2. Chart Section
        $response->assertSee('Properties Added per Month');
        $response->assertSee('propertiesMonthlyChart');

        // 3. Recent Activities
        $response->assertSee('Recent Activities');
        $response->assertSee('Created luxury listing Villa Riviera');

        // 4. Recent Inquiries
        $response->assertSee('Recent Client Inquiries');
        $response->assertSee('Sophia Loren');
        $response->assertSee('sophia@example.com');
    }

    public function test_dashboard_livewire_component_passes_accurate_kpi_counts(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(Dashboard::class)
            ->assertViewHas('totalProperties')
            ->assertViewHas('activeListings')
            ->assertViewHas('pendingInquiries')
            ->assertViewHas('scheduledVisits')
            ->assertViewHas('monthLabels')
            ->assertViewHas('monthlyCounts')
            ->assertViewHas('recentActivities')
            ->assertViewHas('recentInquiries');
    }
}

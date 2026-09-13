<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageInquiries;
use App\Livewire\Admin\ManageVisitRequests;
use App\Models\Agent;
use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\User;
use App\Models\VisitRequest;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadHidingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $managerUser;
    protected User $agent1User;
    protected User $agent2User;
    protected User $unlinkedAgentUser;

    protected Agent $agent1;
    protected Agent $agent2;

    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        // Admin User
        $this->adminUser = User::factory()->create([
            'email' => 'admin_lead_test@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('Admin');

        // Manager User
        $this->managerUser = User::factory()->create([
            'email' => 'manager_lead_test@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->managerUser->assignRole('Manager');

        // Agent 1 & Linked User
        $this->agent1 = Agent::create([
            'name' => 'Agent One',
            'email' => 'agent1_lead_test@tishaproperty.com',
            'phone' => '+91 9876543210',
            'designation' => 'Senior Realtor',
            'experience_years' => 5,
            'is_active' => true,
        ]);
        $this->agent1User = User::factory()->create([
            'email' => 'agent1_lead_test@tishaproperty.com',
            'name' => 'Agent One',
            'is_active' => true,
        ]);
        $this->agent1User->assignRole('Agent');

        // Agent 2 & Linked User
        $this->agent2 = Agent::create([
            'name' => 'Agent Two',
            'email' => 'agent2_lead_test@tishaproperty.com',
            'phone' => '+91 9876543211',
            'designation' => 'Property Advisor',
            'experience_years' => 3,
            'is_active' => true,
        ]);
        $this->agent2User = User::factory()->create([
            'email' => 'agent2_lead_test@tishaproperty.com',
            'name' => 'Agent Two',
            'is_active' => true,
        ]);
        $this->agent2User->assignRole('Agent');

        // Agent User without linked Agent record
        $this->unlinkedAgentUser = User::factory()->create([
            'email' => 'unlinked_agent@tishaproperty.com',
            'name' => 'Unlinked Agent',
            'is_active' => true,
        ]);
        $this->unlinkedAgentUser->assignRole('Agent');

        // Existing or new property
        $type = \App\Models\PropertyType::firstOrCreate(['slug' => 'villa'], ['name' => 'Villa', 'is_active' => true]);
        $status = \App\Models\PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = \App\Models\Location::firstOrCreate(['slug' => 'downtown'], ['name' => 'Downtown', 'is_active' => true]);

        $this->property = Property::create([
            'title' => 'The Imperial Waterfront Palace',
            'slug' => 'the-imperial-waterfront-palace',
            'description' => 'A masterwork architectural residence.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 32000000,
            'bedrooms' => 5,
            'bathrooms' => 6,
            'sqft' => 10000,
            'is_active' => true,
        ]);
    }

    public function test_user_get_linked_agent_resolves_correctly(): void
    {
        $this->assertEquals($this->agent1->id, $this->agent1User->getLinkedAgent()?->id);
        $this->assertEquals($this->agent2->id, $this->agent2User->getLinkedAgent()?->id);
        $this->assertNull($this->unlinkedAgentUser->getLinkedAgent());
    }

    public function test_contact_inquiry_relationships_and_scopes(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@example.com',
            'phone' => '9876543210',
            'subject' => 'Villa in Mumbai',
            'message' => 'Need pricing details',
            'status' => 'new',
            'assigned_agent_id' => $this->agent1->id,
        ]);

        $this->assertEquals($this->agent1->id, $inquiry->assignedAgent->id);
        $this->assertEquals($this->agent1->id, $inquiry->agent->id);
        $this->assertTrue($this->agent1->contactInquiries->contains($inquiry));

        $scoped = ContactInquiry::forAgent($this->agent1->id)->get();
        $this->assertTrue($scoped->contains($inquiry));

        $scopedOther = ContactInquiry::forAgent($this->agent2->id)->get();
        $this->assertFalse($scopedOther->contains($inquiry));
    }

    public function test_agent_only_sees_assigned_contact_inquiries(): void
    {
        $inquiry1 = ContactInquiry::create([
            'name' => 'Client for Agent 1',
            'email' => 'client1@example.com',
            'message' => 'Message for Agent 1',
            'status' => 'new',
            'assigned_agent_id' => $this->agent1->id,
        ]);

        $inquiry2 = ContactInquiry::create([
            'name' => 'Client for Agent 2',
            'email' => 'client2@example.com',
            'message' => 'Message for Agent 2',
            'status' => 'new',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        $unassigned = ContactInquiry::create([
            'name' => 'Unassigned Client',
            'email' => 'unassigned@example.com',
            'message' => 'Unassigned inquiry message',
            'status' => 'new',
            'assigned_agent_id' => null,
        ]);

        // Agent 1 sees only inquiry 1
        Livewire::actingAs($this->agent1User)
            ->test(ManageInquiries::class)
            ->assertSee('Client for Agent 1')
            ->assertDontSee('Client for Agent 2')
            ->assertDontSee('Unassigned Client');

        // Agent 2 sees only inquiry 2
        Livewire::actingAs($this->agent2User)
            ->test(ManageInquiries::class)
            ->assertSee('Client for Agent 2')
            ->assertDontSee('Client for Agent 1')
            ->assertDontSee('Unassigned Client');

        // Unlinked Agent sees none
        Livewire::actingAs($this->unlinkedAgentUser)
            ->test(ManageInquiries::class)
            ->assertDontSee('Client for Agent 1')
            ->assertDontSee('Client for Agent 2')
            ->assertDontSee('Unassigned Client')
            ->assertSee('No inquiries assigned to you yet');

        // Admin sees all
        Livewire::actingAs($this->adminUser)
            ->test(ManageInquiries::class)
            ->assertSee('Client for Agent 1')
            ->assertSee('Client for Agent 2')
            ->assertSee('Unassigned Client');

        // Manager sees all
        Livewire::actingAs($this->managerUser)
            ->test(ManageInquiries::class)
            ->assertSee('Client for Agent 1')
            ->assertSee('Client for Agent 2')
            ->assertSee('Unassigned Client');
    }

    public function test_agent_cannot_view_or_modify_unassigned_or_other_agent_inquiry(): void
    {
        $inquiry2 = ContactInquiry::create([
            'name' => 'Client for Agent 2',
            'email' => 'client2@example.com',
            'message' => 'Confidential inquiry for Agent 2',
            'status' => 'new',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        // Agent 1 attempting to view Agent 2's inquiry -> 403
        Livewire::actingAs($this->agent1User)
            ->test(ManageInquiries::class)
            ->call('viewDetails', $inquiry2->hashid)
            ->assertForbidden();

        // Agent 1 attempting to mark as replied -> 403
        Livewire::actingAs($this->agent1User)
            ->test(ManageInquiries::class)
            ->call('markAsReplied', $inquiry2->hashid)
            ->assertForbidden();

        // Agent 2 CAN view and mark as replied
        Livewire::actingAs($this->agent2User)
            ->test(ManageInquiries::class)
            ->call('viewDetails', $inquiry2->hashid)
            ->assertOk()
            ->call('markAsReplied', $inquiry2->hashid)
            ->assertOk();

        $this->assertEquals('replied', $inquiry2->fresh()->status);
    }

    public function test_agent_only_sees_assigned_visit_requests(): void
    {
        $visit1 = VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Visit Client 1',
            'email' => 'vclient1@example.com',
            'phone' => '9876543210',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
            'assigned_agent_id' => $this->agent1->id,
        ]);

        $visit2 = VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Visit Client 2',
            'email' => 'vclient2@example.com',
            'phone' => '9876543211',
            'visit_date' => now()->addDays(3),
            'status' => 'pending',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        $unassignedVisit = VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Unassigned Visit Client',
            'email' => 'vunassigned@example.com',
            'phone' => '9876543212',
            'visit_date' => now()->addDays(4),
            'status' => 'pending',
            'assigned_agent_id' => null,
        ]);

        // Agent 1 sees only visit 1
        Livewire::actingAs($this->agent1User)
            ->test(ManageVisitRequests::class)
            ->assertSee('Visit Client 1')
            ->assertDontSee('Visit Client 2')
            ->assertDontSee('Unassigned Visit Client');

        // Agent 2 sees only visit 2
        Livewire::actingAs($this->agent2User)
            ->test(ManageVisitRequests::class)
            ->assertSee('Visit Client 2')
            ->assertDontSee('Visit Client 1')
            ->assertDontSee('Unassigned Visit Client');

        // Unlinked Agent sees none
        Livewire::actingAs($this->unlinkedAgentUser)
            ->test(ManageVisitRequests::class)
            ->assertDontSee('Visit Client 1')
            ->assertDontSee('Visit Client 2')
            ->assertDontSee('Unassigned Visit Client')
            ->assertSee('No visit requests assigned to you yet');

        // Admin sees all
        Livewire::actingAs($this->adminUser)
            ->test(ManageVisitRequests::class)
            ->assertSee('Visit Client 1')
            ->assertSee('Visit Client 2')
            ->assertSee('Unassigned Visit Client');
    }

    public function test_agent_cannot_view_or_modify_other_agent_visit_request(): void
    {
        $visit2 = VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Visit Client 2',
            'email' => 'vclient2@example.com',
            'phone' => '9876543211',
            'visit_date' => now()->addDays(3),
            'status' => 'pending',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        // Agent 1 attempting to view Agent 2's visit -> 403
        Livewire::actingAs($this->agent1User)
            ->test(ManageVisitRequests::class)
            ->call('viewDetails', $visit2->hashid)
            ->assertForbidden();

        // Agent 2 CAN view their visit
        Livewire::actingAs($this->agent2User)
            ->test(ManageVisitRequests::class)
            ->call('viewDetails', $visit2->hashid)
            ->assertOk();
    }

    public function test_admin_and_manager_can_assign_agent_to_inquiry_and_visit(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Unassigned Buyer',
            'email' => 'buyer@example.com',
            'message' => 'Looking for luxury home',
            'status' => 'new',
            'assigned_agent_id' => null,
        ]);

        $visit = VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Unassigned Visitor',
            'email' => 'visitor@example.com',
            'phone' => '9876543210',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
            'assigned_agent_id' => null,
        ]);

        // Admin assigns inquiry to Agent 1
        Livewire::actingAs($this->adminUser)
            ->test(ManageInquiries::class)
            ->call('assignAgent', $inquiry->hashid, $this->agent1->id)
            ->assertOk();

        $this->assertEquals($this->agent1->id, $inquiry->fresh()->assigned_agent_id);

        // Manager assigns visit to Agent 2
        Livewire::actingAs($this->managerUser)
            ->test(ManageVisitRequests::class)
            ->call('assignAgent', $visit->hashid, $this->agent2->id)
            ->assertOk();

        $this->assertEquals($this->agent2->id, $visit->fresh()->assigned_agent_id);

        // Agent CANNOT assign agents -> 403
        Livewire::actingAs($this->agent1User)
            ->test(ManageInquiries::class)
            ->call('assignAgent', $inquiry->hashid, $this->agent2->id)
            ->assertForbidden();

        Livewire::actingAs($this->agent1User)
            ->test(ManageVisitRequests::class)
            ->call('assignAgent', $visit->hashid, $this->agent1->id)
            ->assertForbidden();
    }

    public function test_dashboard_stats_and_inquiries_are_scoped_for_agents(): void
    {
        // Clear existing inquiries/visits to have deterministic counts
        ContactInquiry::query()->delete();
        VisitRequest::query()->delete();

        // Inquiry 1 for Agent 1
        ContactInquiry::create([
            'name' => 'Agent 1 Lead',
            'email' => 'lead1@example.com',
            'message' => 'Agent 1 lead text',
            'status' => 'new',
            'assigned_agent_id' => $this->agent1->id,
        ]);

        // Inquiry 2 for Agent 2
        ContactInquiry::create([
            'name' => 'Agent 2 Lead',
            'email' => 'lead2@example.com',
            'message' => 'Agent 2 lead text',
            'status' => 'new',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        // Visit 1 for Agent 1
        VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Agent 1 Visit',
            'email' => 'v1@example.com',
            'phone' => '9876543210',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
            'assigned_agent_id' => $this->agent1->id,
        ]);

        // Visit 2 for Agent 2
        VisitRequest::create([
            'property_id' => $this->property->id,
            'name' => 'Agent 2 Visit',
            'email' => 'v2@example.com',
            'phone' => '9876543211',
            'visit_date' => now()->addDays(3),
            'status' => 'pending',
            'assigned_agent_id' => $this->agent2->id,
        ]);

        // Agent 1 Dashboard sees count = 1 for pending inquiries, count = 1 for visits, sees Agent 1 Lead, does not see Agent 2 Lead
        Livewire::actingAs($this->agent1User)
            ->test(Dashboard::class)
            ->assertViewHas('pendingInquiries', 1)
            ->assertViewHas('scheduledVisits', 1)
            ->assertSee('Agent 1 Lead')
            ->assertDontSee('Agent 2 Lead');

        // Admin Dashboard sees count = 2 for pending inquiries, count = 2 for visits, sees both leads
        Livewire::actingAs($this->adminUser)
            ->test(Dashboard::class)
            ->assertViewHas('pendingInquiries', 2)
            ->assertViewHas('scheduledVisits', 2)
            ->assertSee('Agent 1 Lead')
            ->assertSee('Agent 2 Lead');
    }
}

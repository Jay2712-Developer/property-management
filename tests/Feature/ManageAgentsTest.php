<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageAgentForm;
use App\Livewire\Admin\ManageAgents;
use App\Models\Agent;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManageAgentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $managerUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_agent@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->managerUser = User::factory()->create([
            'email' => 'manager_agent@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->managerUser->assignRole('Manager');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_agent@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_manager_role_strictly_forbidden_from_viewing_creating_or_editing_agents(): void
    {
        // 1. Manager cannot view agents list (403)
        $this->actingAs($this->managerUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.agents.index'))
            ->assertStatus(403);

        // 2. Manager cannot access create form (403)
        $this->actingAs($this->managerUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.agents.create'))
            ->assertStatus(403);

        // 3. Manager cannot access edit form (403)
        $agent = Agent::create([
            'name' => 'Existing Agent',
            'email' => 'agent_test@tishaproperty.com',
            'is_active' => true,
        ]);

        $this->actingAs($this->managerUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.agents.edit', ['agentId' => $agent->hashid]))
            ->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_access_agent_routes(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.agents.index'))
            ->assertStatus(403);
    }

    public function test_authorized_admin_can_render_agents_page(): void
    {
        Agent::create([
            'name' => 'Alexander Hayes',
            'designation' => 'Principal Broker',
            'phone' => '+1 (555) 789-0123',
            'email' => 'alexander@tishaproperty.com',
            'experience_years' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.agents.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageAgents::class)
            ->assertSee('Alexander Hayes')
            ->assertSee('Principal Broker');
    }

    public function test_can_create_new_agent_with_photo_and_social_links(): void
    {
        Storage::fake('public');

        $photoFile = UploadedFile::fake()->image('agent-headshot.jpg');

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAgentForm::class)
            ->set('name', 'Sophia Laurent')
            ->set('designation', 'Senior Luxury Consultant')
            ->set('experience_years', 8)
            ->set('phone', '+1 555 456 7890')
            ->set('email', 'sophia@tishaproperty.com')
            ->set('bio', 'Specializing in beachfront estates and luxury penthouses.')
            ->set('photo', $photoFile)
            ->set('social_links', [
                'facebook' => 'https://facebook.com/sophia.realty',
                'linkedin' => 'https://linkedin.com/in/sophialaurent',
                'twitter' => '',
                'instagram' => 'https://instagram.com/sophia.luxury',
                'whatsapp' => '+15554567890',
            ])
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.agents.index'));

        $this->assertDatabaseHas('agents', [
            'name' => 'Sophia Laurent',
            'email' => 'sophia@tishaproperty.com',
            'experience_years' => 8,
            'is_active' => true,
        ]);

        $agent = Agent::where('email', 'sophia@tishaproperty.com')->first();
        $this->assertNotNull($agent->photo_path);
        $this->assertEquals('https://linkedin.com/in/sophialaurent', $agent->social_links['linkedin']);
        Storage::disk('public')->assertExists($agent->photo_path);
    }

    public function test_agent_email_must_be_unique(): void
    {
        Agent::create([
            'name' => 'Agent One',
            'email' => 'duplicate@tishaproperty.com',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAgentForm::class)
            ->set('name', 'Agent Two')
            ->set('email', 'duplicate@tishaproperty.com')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_can_edit_agent_using_hashid(): void
    {
        $agent = Agent::create([
            'name' => 'Marcus Vance',
            'designation' => 'Associate Broker',
            'email' => 'marcus@tishaproperty.com',
            'experience_years' => 4,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAgentForm::class, ['agentId' => $agent->hashid])
            ->assertSet('name', 'Marcus Vance')
            ->assertSet('email', 'marcus@tishaproperty.com')
            ->set('name', 'Marcus Vance, MBA')
            ->set('designation', 'Managing Director')
            ->set('experience_years', 5)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.agents.index'));

        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'name' => 'Marcus Vance, MBA',
            'designation' => 'Managing Director',
            'experience_years' => 5,
        ]);
    }

    public function test_can_toggle_agent_active_status(): void
    {
        $agent = Agent::create([
            'name' => 'Active Agent',
            'email' => 'active_toggle@tishaproperty.com',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAgents::class)
            ->call('toggleActive', $agent->hashid);

        $this->assertFalse($agent->fresh()->is_active);
    }

    public function test_can_delete_agent_with_photo_cleanup(): void
    {
        Storage::fake('public');

        $photoPath = 'agents/sample-agent.jpg';
        Storage::disk('public')->put($photoPath, 'fake photo content');

        $agent = Agent::create([
            'name' => 'Agent To Delete',
            'email' => 'delete_agent@tishaproperty.com',
            'photo_path' => $photoPath,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageAgents::class)
            ->call('confirmDelete', $agent->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteAgent')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('agents', ['id' => $agent->id]);
        Storage::disk('public')->assertMissing($photoPath);
    }
}

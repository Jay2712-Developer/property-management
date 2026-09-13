<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManagePages;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManagePagesTest extends TestCase
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
            'email' => 'superadmin_pages@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_pages@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_render_pages_management_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.pages.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManagePages::class)
            ->assertSee('Dynamic Pages &amp; Legal Content', false)
            ->assertSee('About Us')
            ->assertSee('Terms of Service')
            ->assertSee('Privacy Policy');
    }

    public function test_unauthorized_user_cannot_access_pages_module(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.pages.index'))
            ->assertStatus(403);
    }

    public function test_can_create_new_page_with_sanitized_content(): void
    {
        $maliciousHtml = '<p>Welcome to our FAQ.</p><script>alert("xss")</script><a href="javascript:void(0)" onclick="steal()">Click here</a>';

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('title', 'Frequently Asked Questions')
            ->assertSet('slug', 'frequently-asked-questions')
            ->set('content', $maliciousHtml)
            ->set('meta_title', 'FAQ - TISHA Real Estate')
            ->set('meta_description', 'Find answers to common questions about buying and selling properties.')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('pages', [
            'title' => 'Frequently Asked Questions',
            'slug' => 'frequently-asked-questions',
            'meta_title' => 'FAQ - TISHA Real Estate',
            'is_active' => 1,
        ]);

        $page = Page::where('slug', 'frequently-asked-questions')->first();
        $this->assertStringNotContainsString('<script>', $page->content);
        $this->assertStringNotContainsString('onclick=', $page->content);
        $this->assertStringContainsString('Welcome to our FAQ', $page->content);
    }

    public function test_slug_must_be_unique(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('openCreateModal')
            ->set('title', 'Duplicate About Us')
            ->set('slug', 'about-us') // already exists from seeder
            ->set('content', '<p>Some content</p>')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_can_edit_existing_page_using_hashid(): void
    {
        $page = Page::where('slug', 'about-us')->first();

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('openEditModal', $page->hashid)
            ->assertSet('showModal', true)
            ->assertSet('title', 'About Us')
            ->assertSet('slug', 'about-us')
            ->set('title', 'About Our Firm')
            ->set('content', '<h3>Updated Company Heritage</h3><p>Providing premier property investments since 2010.</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'About Our Firm',
        ]);
    }

    public function test_can_toggle_page_active_status(): void
    {
        $page = Page::where('slug', 'privacy-policy')->first();
        $this->assertTrue($page->is_active);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('toggleActive', $page->hashid)
            ->assertHasNoErrors();

        $this->assertFalse((bool) $page->fresh()->is_active);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('toggleActive', $page->hashid)
            ->assertHasNoErrors();

        $this->assertTrue((bool) $page->fresh()->is_active);
    }

    public function test_can_delete_page_with_confirmation_modal(): void
    {
        $page = Page::create([
            'title' => 'Temporary Promo Terms',
            'slug' => 'promo-terms-' . uniqid(),
            'content' => '<p>Temporary page.</p>',
            'is_active' => false,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->call('confirmDelete', $page->hashid)
            ->assertSet('showDeleteModal', true)
            ->assertSet('pageToDeleteId', $page->id)
            ->call('deletePage')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    public function test_can_search_and_filter_pages(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManagePages::class)
            ->set('search', 'Privacy')
            ->assertSee('Privacy Policy')
            ->assertDontSee('Terms of Service')
            ->set('search', '')
            ->set('statusFilter', '1')
            ->assertSee('About Us');
    }
}

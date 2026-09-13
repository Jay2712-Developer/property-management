<?php

namespace Tests\Feature;

use App\Livewire\Admin\MediaManager;
use App\Models\User;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaManagerTest extends TestCase
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
            'email' => 'superadmin_media@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_media@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_render_media_manager(): void
    {
        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.media.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(MediaManager::class)
            ->assertSee('Media &amp; Asset Library', false)
            ->assertSee('php artisan storage:link');
    }

    public function test_unauthorized_user_cannot_access_media_manager(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.media.index'))
            ->assertStatus(403);
    }

    public function test_can_browse_and_preview_files_in_uploads(): void
    {
        Storage::fake('public');

        // Put sample files in uploads and subdirectories
        Storage::disk('public')->put('uploads/villa-exterior.jpg', 'fake-image-bytes');
        Storage::disk('public')->put('uploads/properties/penthouse-plan.pdf', 'fake-pdf-bytes');
        Storage::disk('public')->put('uploads/settings/site-logo.png', 'fake-logo-bytes');

        Livewire::actingAs($this->superAdmin)
            ->test(MediaManager::class)
            ->assertSee('villa-exterior.jpg')
            ->assertSee('penthouse-plan.pdf')
            ->assertSee('site-logo.png');
    }

    public function test_can_search_files_by_name(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('uploads/luxury-yacht.jpg', 'fake-bytes');
        Storage::disk('public')->put('uploads/city-apartment.jpg', 'fake-bytes');

        Livewire::actingAs($this->superAdmin)
            ->test(MediaManager::class)
            ->set('search', 'yacht')
            ->assertSee('luxury-yacht.jpg')
            ->assertDontSee('city-apartment.jpg');
    }

    public function test_can_filter_by_type(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('uploads/beachfront.jpg', 'fake-img');
        Storage::disk('public')->put('uploads/brochure.pdf', 'fake-doc');

        Livewire::actingAs($this->superAdmin)
            ->test(MediaManager::class)
            ->set('typeFilter', 'images')
            ->assertSee('beachfront.jpg')
            ->assertDontSee('brochure.pdf')
            ->set('typeFilter', 'documents')
            ->assertSee('brochure.pdf')
            ->assertDontSee('beachfront.jpg');
    }

    public function test_can_upload_new_file_to_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('aerial-view.jpg', 600, 400);

        Livewire::actingAs($this->superAdmin)
            ->test(MediaManager::class)
            ->call('openUploadModal')
            ->assertSet('showUploadModal', true)
            ->set('uploadFolder', 'uploads/properties')
            ->set('newFile', $file)
            ->call('uploadFile')
            ->assertHasNoErrors()
            ->assertSet('showUploadModal', false);

        Storage::disk('public')->assertExists('uploads/properties/' . $file->hashName());
    }

    public function test_can_delete_file_with_confirmation_modal(): void
    {
        Storage::fake('public');

        $filePath = 'uploads/obsolete-banner.jpg';
        Storage::disk('public')->put($filePath, 'obsolete-content');
        Storage::disk('public')->assertExists($filePath);

        Livewire::actingAs($this->superAdmin)
            ->test(MediaManager::class)
            ->call('confirmDelete', $filePath)
            ->assertSet('showDeleteModal', true)
            ->assertSet('fileToDeletePath', $filePath)
            ->assertSet('fileToDeleteName', 'obsolete-banner.jpg')
            ->call('deleteFile')
            ->assertSet('showDeleteModal', false);

        Storage::disk('public')->assertMissing($filePath);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageInquiries;
use App\Livewire\Admin\ManageTestimonials;
use App\Livewire\Admin\ManageVisitRequests;
use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VisitRequest;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ManageInquiriesVisitsTestimonialsTest extends TestCase
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
            'email' => 'superadmin_modules@tishaproperty.com',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $this->unauthorizedUser = User::factory()->create([
            'email' => 'unauth_modules@tishaproperty.com',
            'is_active' => true,
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* 1. CONTACT INQUIRIES TESTS                                                 */
    /* -------------------------------------------------------------------------- */

    public function test_super_admin_can_view_contact_inquiries(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'phone' => '+1 555 123 4567',
            'subject' => 'Interested in Penthouse',
            'message' => 'Hello, I would like to know if this property is still on the market.',
            'status' => 'new',
        ]);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.inquiries.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageInquiries::class)
            ->assertSee('John Doe')
            ->assertSee('Interested in Penthouse');
    }

    public function test_can_mark_inquiry_as_replied_and_closed(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Eleanor Vance',
            'email' => 'eleanor@example.com',
            'phone' => '+1 555 987 6543',
            'subject' => 'Villa Inquiry',
            'message' => 'Please provide the floor plans for the villa.',
            'status' => 'new',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageInquiries::class)
            ->call('markAsReplied', $inquiry->hashid)
            ->assertHasNoErrors();

        $this->assertEquals('replied', $inquiry->fresh()->status);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageInquiries::class)
            ->call('markAsClosed', $inquiry->hashid)
            ->assertHasNoErrors();

        $this->assertEquals('closed', $inquiry->fresh()->status);
    }

    public function test_can_delete_inquiry_with_confirmation_modal(): void
    {
        $inquiry = ContactInquiry::create([
            'name' => 'Spam Sender',
            'email' => 'spam@example.com',
            'phone' => '+1 000 000 0000',
            'subject' => 'Spam Message',
            'message' => 'Buy cheap loans today!',
            'status' => 'new',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageInquiries::class)
            ->call('confirmDelete', $inquiry->hashid)
            ->assertSet('showDeleteModal', true)
            ->assertSet('inquiryToDeleteId', $inquiry->id)
            ->call('deleteInquiry')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('contact_inquiries', [
            'id' => $inquiry->id,
        ]);
    }

    public function test_unauthorized_user_cannot_access_inquiries(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.inquiries.index'))
            ->assertStatus(403);
    }

    protected function createProperty(): Property
    {
        $type = \App\Models\PropertyType::first();
        $status = \App\Models\PropertyStatus::first();
        $location = \App\Models\Location::firstOrCreate(
            ['slug' => 'downtown-dubai'],
            ['name' => 'Downtown Dubai', 'is_active' => true]
        );

        return Property::create([
            'title' => 'Azure Heights Penthouse',
            'slug' => 'azure-heights-penthouse-' . uniqid(),
            'property_type_id' => $type?->id,
            'property_status_id' => $status?->id,
            'location_id' => $location->id,
            'price' => 2500000,
            'is_active' => true,
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* 2. VISIT REQUESTS TESTS                                                    */
    /* -------------------------------------------------------------------------- */

    public function test_super_admin_can_view_visit_requests(): void
    {
        $property = $this->createProperty();

        $visit = VisitRequest::create([
            'property_id' => $property->id,
            'name' => 'Robert Langdon',
            'email' => 'robert@example.com',
            'phone' => '+1 555 333 4444',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
        ]);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.visits.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageVisitRequests::class)
            ->assertSee('Robert Langdon')
            ->assertSee($property->title);
    }

    public function test_can_approve_and_reject_visit_request(): void
    {
        $property = $this->createProperty();

        $visit = VisitRequest::create([
            'property_id' => $property->id,
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '+1 555 777 8888',
            'visit_date' => now()->addDays(3),
            'status' => 'pending',
        ]);

        // Approve
        Livewire::actingAs($this->superAdmin)
            ->test(ManageVisitRequests::class)
            ->call('approveVisit', $visit->hashid)
            ->assertHasNoErrors();

        $this->assertEquals('approved', $visit->fresh()->status);

        // Reject
        Livewire::actingAs($this->superAdmin)
            ->test(ManageVisitRequests::class)
            ->call('rejectVisit', $visit->hashid)
            ->assertHasNoErrors();

        $this->assertEquals('rejected', $visit->fresh()->status);
    }

    public function test_can_delete_visit_request(): void
    {
        $property = $this->createProperty();

        $visit = VisitRequest::create([
            'property_id' => $property->id,
            'name' => 'Cancelled Client',
            'email' => 'cancel@example.com',
            'phone' => '+1 555 999 0000',
            'visit_date' => now()->addDay(),
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageVisitRequests::class)
            ->call('confirmDelete', $visit->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteVisit')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('visit_requests', [
            'id' => $visit->id,
        ]);
    }

    public function test_unauthorized_user_cannot_access_visits(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.visits.index'))
            ->assertStatus(403);
    }

    /* -------------------------------------------------------------------------- */
    /* 3. TESTIMONIALS TESTS                                                      */
    /* -------------------------------------------------------------------------- */

    public function test_super_admin_can_view_testimonials(): void
    {
        $testimonial = Testimonial::create([
            'client_name' => 'Arthur Pendelton',
            'rating' => 5,
            'message' => 'Exceptional service! Found our dream penthouse in record time.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($this->superAdmin)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.testimonials.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ManageTestimonials::class)
            ->assertSee('Arthur Pendelton')
            ->assertSee('Exceptional service!');
    }

    public function test_can_create_testimonial_with_photo_and_rating(): void
    {
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('client-avatar.jpg');

        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('client_name', 'Grace Sterling')
            ->set('client_photo', $photo)
            ->set('rating', 5)
            ->set('message', 'TISHA Real Estate provided white-glove advisory throughout.')
            ->set('is_active', true)
            ->set('sort_order', 2)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('testimonials', [
            'client_name' => 'Grace Sterling',
            'rating' => 5,
            'is_active' => 1,
            'sort_order' => 2,
        ]);

        $testimonial = Testimonial::where('client_name', 'Grace Sterling')->first();
        $this->assertNotNull($testimonial->client_photo);
        Storage::disk('public')->assertExists($testimonial->client_photo);
    }

    public function test_can_edit_existing_testimonial(): void
    {
        $testimonial = Testimonial::create([
            'client_name' => 'Michael Chang',
            'rating' => 4,
            'message' => 'Great experience overall.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('openEditModal', $testimonial->hashid)
            ->assertSet('showModal', true)
            ->assertSet('client_name', 'Michael Chang')
            ->assertSet('rating', 4)
            ->set('rating', 5)
            ->set('message', 'Outstanding experience from start to close!')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'rating' => 5,
            'message' => 'Outstanding experience from start to close!',
        ]);
    }

    public function test_can_toggle_testimonial_active_status(): void
    {
        $testimonial = Testimonial::create([
            'client_name' => 'Julia Roberts',
            'rating' => 5,
            'message' => 'Loved working with the team.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('toggleActive', $testimonial->hashid)
            ->assertHasNoErrors();

        $this->assertFalse((bool) $testimonial->fresh()->is_active);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('toggleActive', $testimonial->hashid)
            ->assertHasNoErrors();

        $this->assertTrue((bool) $testimonial->fresh()->is_active);
    }

    public function test_can_delete_testimonial(): void
    {
        $testimonial = Testimonial::create([
            'client_name' => 'Obsolete Reviewer',
            'rating' => 3,
            'message' => 'Average transaction.',
            'is_active' => false,
            'sort_order' => 99,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('confirmDelete', $testimonial->hashid)
            ->assertSet('showDeleteModal', true)
            ->call('deleteTestimonial')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('testimonials', [
            'id' => $testimonial->id,
        ]);
    }

    public function test_testimonial_validation_rules(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(ManageTestimonials::class)
            ->call('openCreateModal')
            ->set('client_name', '')
            ->set('rating', 6) // invalid rating > 5
            ->set('message', 'Hi') // too short
            ->call('save')
            ->assertHasErrors(['client_name', 'rating', 'message']);
    }

    public function test_unauthorized_user_cannot_access_testimonials(): void
    {
        $this->actingAs($this->unauthorizedUser)
            ->withSession(['admin_2fa_passed' => true])
            ->get(route('admin.testimonials.index'))
            ->assertStatus(403);
    }
}

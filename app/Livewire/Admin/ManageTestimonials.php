<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Testimonials Management - TISHA Real Estate')]
class ManageTestimonials extends Component
{
    use WithPagination, WithFileUploads;

    // Search & Filter
    public string $search = '';
    public string $activeFilter = '';

    // Modal State
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $testimonialId = null;
    public ?string $existingPhoto = null;

    // Form Fields with Livewire 3 #[Rule]
    #[Rule('required|string|max:255', as: 'client name')]
    public string $client_name = '';

    #[Rule('nullable|image|max:2048', as: 'client photo')]
    public $client_photo = null;

    #[Rule('required|integer|min:1|max:5', as: 'rating')]
    public int $rating = 5;

    #[Rule('required|string|min:5', as: 'review message')]
    public string $message = '';

    #[Rule('boolean')]
    public bool $is_active = true;

    #[Rule('required|integer|min:0', as: 'sort order')]
    public int $sort_order = 0;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $testimonialToDeleteId = null;
    public string $testimonialToDeleteName = '';
    public string $testimonialToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new testimonial.
     */
    public function openCreateModal(): void
    {
        abort_unless(
            auth()->user()?->can('create_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to create testimonials.'
        );

        $this->resetValidation();
        $this->reset(['client_name', 'client_photo', 'existingPhoto', 'testimonialId']);
        $this->rating = 5;
        $this->message = '';
        $this->is_active = true;
        $this->sort_order = ((int) Testimonial::max('sort_order')) + 1;
        $this->isEditing = false;
        $this->showModal = true;
    }

    /**
     * Open modal to edit an existing testimonial.
     */
    public function openEditModal(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to edit testimonials.'
        );

        $this->resetValidation();
        $id = Testimonial::decodeHashid($hashid);
        $testimonial = Testimonial::findOrFail($id);

        $this->testimonialId = $testimonial->id;
        $this->client_name = $testimonial->client_name;
        $this->existingPhoto = $testimonial->client_photo;
        $this->client_photo = null;
        $this->rating = $testimonial->rating;
        $this->message = $testimonial->message;
        $this->is_active = (bool) $testimonial->is_active;
        $this->sort_order = $testimonial->sort_order;

        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Close the create/edit modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['client_name', 'client_photo', 'existingPhoto', 'testimonialId', 'message']);
        $this->rating = 5;
        $this->is_active = true;
        $this->sort_order = 0;
        $this->isEditing = false;
    }

    /**
     * Save testimonial (Create or Update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_testimonials') || auth()->user()?->can('manage_testimonials'),
                403,
                'Unauthorized. You do not have permission to edit testimonials.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_testimonials') || auth()->user()?->can('manage_testimonials'),
                403,
                'Unauthorized. You do not have permission to create testimonials.'
            );
        }

        $this->validate();

        $photoPath = $this->existingPhoto;

        if ($this->client_photo) {
            // Delete existing photo if replacing
            if ($this->existingPhoto && Storage::disk('public')->exists($this->existingPhoto)) {
                Storage::disk('public')->delete($this->existingPhoto);
            }
            $photoPath = $this->client_photo->store('testimonials', 'public');
        }

        if ($this->isEditing) {
            $testimonial = Testimonial::findOrFail($this->testimonialId);
            $testimonial->update([
                'client_name' => $this->client_name,
                'client_photo' => $photoPath,
                'rating' => $this->rating,
                'message' => $this->message,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);

            ActivityLog::record("Updated testimonial from '{$this->client_name}'", 'Testimonials', $testimonial->id);
            session()->flash('status', "Testimonial from '{$this->client_name}' was successfully updated.");
        } else {
            $testimonial = Testimonial::create([
                'client_name' => $this->client_name,
                'client_photo' => $photoPath,
                'rating' => $this->rating,
                'message' => $this->message,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);

            ActivityLog::record("Created testimonial from '{$this->client_name}'", 'Testimonials', $testimonial->id);
            session()->flash('status', "Testimonial from '{$this->client_name}' was successfully created.");
        }

        $this->closeModal();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to edit testimonials.'
        );

        $id = Testimonial::decodeHashid($hashid);
        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([
            'is_active' => !$testimonial->is_active,
        ]);

        $statusText = $testimonial->is_active ? 'active' : 'inactive';
        ActivityLog::record("Set testimonial for '{$testimonial->client_name}' to {$statusText}", 'Testimonials', $testimonial->id);

        session()->flash('status', "Testimonial for '{$testimonial->client_name}' is now {$statusText}.");
    }

    /**
     * Prompt single testimonial delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to delete testimonials.'
        );

        $id = Testimonial::decodeHashid($hashid);
        $testimonial = Testimonial::findOrFail($id);

        $this->testimonialToDeleteId = $testimonial->id;
        $this->testimonialToDeleteName = $testimonial->client_name;
        $this->testimonialToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->testimonialToDeleteId = null;
        $this->testimonialToDeleteName = '';
        $this->testimonialToDeleteHashid = '';
    }

    /**
     * Delete testimonial after confirmation.
     */
    public function deleteTestimonial(): void
    {
        abort_unless(
            auth()->user()?->can('delete_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to delete testimonials.'
        );

        if (!$this->testimonialToDeleteId) {
            return;
        }

        $testimonial = Testimonial::findOrFail($this->testimonialToDeleteId);
        $name = $testimonial->client_name;

        // Clean up client photo if present
        if ($testimonial->client_photo && Storage::disk('public')->exists($testimonial->client_photo)) {
            Storage::disk('public')->delete($testimonial->client_photo);
        }

        $testimonial->delete();

        ActivityLog::record("Deleted testimonial for '{$name}'", 'Testimonials', null);

        $this->cancelDelete();
        session()->flash('status', "Testimonial for '{$name}' was successfully removed.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_testimonials') || auth()->user()?->can('manage_testimonials'),
            403,
            'Unauthorized. You do not have permission to view testimonials.'
        );

        $testimonials = Testimonial::query()
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.manage-testimonials', [
            'testimonials' => $testimonials,
        ]);
    }
}

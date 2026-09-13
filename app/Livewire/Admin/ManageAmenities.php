<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Amenity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Manage Amenities - TISHA Real Estate')]
class ManageAmenities extends Component
{
    use WithPagination;

    // Filters & Search
    public string $search = '';
    public string $categoryFilter = '';
    public string $activeFilter = '';

    // Modal state for Create / Edit
    public bool $showAmenityModal = false;
    public bool $isEditing = false;
    public ?int $editingAmenityId = null;
    public string $editingAmenityHashid = '';

    // Form inputs
    #[Rule('required|string|min:2|max:100')]
    public string $name = '';

    #[Rule('nullable|string|max:100')]
    public string $icon = 'fa-solid fa-check';

    #[Rule('required|string|max:50')]
    public string $category = 'Interior';

    #[Rule('required|integer|min:0|max:9999')]
    public int $sort_order = 0;

    #[Rule('boolean')]
    public bool $is_active = true;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $amenityToDeleteId = null;
    public string $amenityToDeleteName = '';
    public string $amenityToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new amenity.
     */
    public function createAmenity(): void
    {
        abort_unless(
            auth()->user()?->can('create_amenities') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingAmenityId = null;
        $this->editingAmenityHashid = '';
        $this->name = '';
        $this->icon = 'fa-solid fa-check';
        $this->category = 'Interior';
        $this->sort_order = Amenity::max('sort_order') + 1;
        $this->is_active = true;

        $this->showAmenityModal = true;
    }

    /**
     * Open modal to edit an existing amenity.
     */
    public function editAmenity(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_amenities') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $id = Amenity::decodeHashid($hashid);
        $amenity = Amenity::findOrFail($id);

        $this->isEditing = true;
        $this->editingAmenityId = $amenity->id;
        $this->editingAmenityHashid = $hashid;
        $this->name = $amenity->name;
        $this->icon = $amenity->icon ?: 'fa-solid fa-check';
        $this->category = $amenity->category ?: 'Interior';
        $this->sort_order = (int) $amenity->sort_order;
        $this->is_active = (bool) $amenity->is_active;

        $this->showAmenityModal = true;
    }

    /**
     * Close modal.
     */
    public function closeAmenityModal(): void
    {
        $this->showAmenityModal = false;
        $this->editingAmenityId = null;
        $this->editingAmenityHashid = '';
    }

    /**
     * Save amenity (create or update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_amenities') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_amenities') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        }

        $this->validate();

        $nameRule = $this->isEditing
            ? ValidationRule::unique('amenities', 'name')->ignore($this->editingAmenityId)
            : ValidationRule::unique('amenities', 'name');

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', $nameRule],
        ]);

        $data = [
            'name' => $this->name,
            'icon' => $this->icon ?: 'fa-solid fa-check',
            'category' => $this->category,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->isEditing) {
            $amenity = Amenity::findOrFail($this->editingAmenityId);
            $amenity->update($data);
            $message = "Amenity '{$amenity->name}' updated successfully.";

            ActivityLog::record("Updated amenity '{$amenity->name}'", 'Properties', $amenity->id);
        } else {
            $amenity = Amenity::create($data);
            $message = "Amenity '{$amenity->name}' created successfully.";

            ActivityLog::record("Created amenity '{$amenity->name}'", 'Properties', $amenity->id);
        }

        $this->closeAmenityModal();
        session()->flash('status', $message);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_amenities') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = Amenity::decodeHashid($hashid);
        $amenity = Amenity::findOrFail($id);

        $amenity->update([
            'is_active' => !$amenity->is_active,
        ]);

        $statusLabel = $amenity->is_active ? 'Active' : 'Inactive';
        ActivityLog::record("Amenity '{$amenity->name}' status changed to {$statusLabel}", 'Properties', $amenity->id);

        session()->flash('status', "Amenity '{$amenity->name}' is now {$statusLabel}.");
    }

    /**
     * Prompt delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_amenities') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = Amenity::decodeHashid($hashid);
        $amenity = Amenity::findOrFail($id);

        $this->amenityToDeleteId = $amenity->id;
        $this->amenityToDeleteName = $amenity->name;
        $this->amenityToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion modal.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->amenityToDeleteId = null;
        $this->amenityToDeleteName = '';
        $this->amenityToDeleteHashid = '';
    }

    /**
     * Execute deletion after user confirms.
     */
    public function deleteAmenity(): void
    {
        abort_unless(
            auth()->user()?->can('delete_amenities') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        if (!$this->amenityToDeleteId) {
            return;
        }

        $amenity = Amenity::findOrFail($this->amenityToDeleteId);
        $name = $amenity->name;

        // Detach pivot associations from properties before deleting
        $amenity->properties()->detach();
        $amenity->delete();

        ActivityLog::record("Deleted amenity '{$name}'", 'Properties', null);

        $this->cancelDelete();
        session()->flash('status', "Amenity '{$name}' was successfully deleted.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_amenities') || auth()->user()?->can('view_properties'),
            403,
            'Unauthorized.'
        );

        $categories = Amenity::whereNotNull('category')->distinct()->pluck('category')->toArray();

        $amenities = Amenity::query()
            ->withCount('properties')
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($this->categoryFilter, function (Builder $query, string $category) {
                $query->where('category', $category);
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-amenities', [
            'amenities' => $amenities,
            'categories' => $categories,
        ]);
    }
}

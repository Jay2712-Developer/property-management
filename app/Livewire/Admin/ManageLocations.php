<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Manage Locations - TISHA Real Estate')]
class ManageLocations extends Component
{
    use WithPagination;

    // Filters & Search
    public string $search = '';
    public string $parentFilter = '';
    public string $activeFilter = '';

    // Modal state for Create / Edit
    public bool $showLocationModal = false;
    public bool $isEditing = false;
    public ?int $editingLocationId = null;
    public string $editingLocationHashid = '';

    // Form inputs
    #[Rule('required|string|min:2|max:100')]
    public string $name = '';

    #[Rule('nullable|exists:locations,id')]
    public ?int $parent_id = null;

    #[Rule('boolean')]
    public bool $is_active = true;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $locationToDeleteId = null;
    public string $locationToDeleteName = '';
    public string $locationToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingParentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new location.
     */
    public function createLocation(): void
    {
        abort_unless(
            auth()->user()?->can('create_locations') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingLocationId = null;
        $this->editingLocationHashid = '';
        $this->name = '';
        $this->parent_id = null;
        $this->is_active = true;

        $this->showLocationModal = true;
    }

    /**
     * Open modal to edit an existing location.
     */
    public function editLocation(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_locations') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $id = Location::decodeHashid($hashid);
        $location = Location::findOrFail($id);

        $this->isEditing = true;
        $this->editingLocationId = $location->id;
        $this->editingLocationHashid = $hashid;
        $this->name = $location->name;
        $this->parent_id = $location->parent_id;
        $this->is_active = (bool) $location->is_active;

        $this->showLocationModal = true;
    }

    /**
     * Close Create/Edit modal.
     */
    public function closeLocationModal(): void
    {
        $this->showLocationModal = false;
        $this->editingLocationId = null;
        $this->editingLocationHashid = '';
    }

    /**
     * Save location (create or update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_locations') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_locations') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        }

        $this->validate();

        // Validation: Name must be unique per parent
        $nameRule = ValidationRule::unique('locations', 'name')
            ->where(function ($query) {
                return $this->parent_id
                    ? $query->where('parent_id', $this->parent_id)
                    : $query->whereNull('parent_id');
            });

        if ($this->isEditing) {
            $nameRule->ignore($this->editingLocationId);
        }

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', $nameRule],
        ], [
            'name.unique' => 'A location with this name already exists within the selected parent.',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;
        while (
            Location::where('slug', $slug)
                ->when($this->isEditing, fn($q) => $q->where('id', '!=', $this->editingLocationId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $data = [
            'name' => $this->name,
            'slug' => $slug,
            'parent_id' => $this->parent_id ?: null,
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->isEditing) {
            $location = Location::findOrFail($this->editingLocationId);
            $location->update($data);
            $message = "Location '{$location->name}' updated successfully.";

            ActivityLog::record("Updated location '{$location->name}'", 'Properties', $location->id);
        } else {
            $location = Location::create($data);
            $message = "Location '{$location->name}' created successfully.";

            ActivityLog::record("Created location '{$location->name}'", 'Properties', $location->id);
        }

        $this->closeLocationModal();
        \Illuminate\Support\Facades\Cache::forget('locations');
        session()->flash('status', $message);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_locations') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = Location::decodeHashid($hashid);
        $location = Location::findOrFail($id);

        $location->update([
            'is_active' => !$location->is_active,
        ]);

        \Illuminate\Support\Facades\Cache::forget('locations');

        $statusLabel = $location->is_active ? 'Active' : 'Inactive';
        ActivityLog::record("Location '{$location->name}' status changed to {$statusLabel}", 'Properties', $location->id);

        session()->flash('status', "Location '{$location->name}' is now {$statusLabel}.");
    }

    /**
     * Prompt delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_locations') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = Location::decodeHashid($hashid);
        $location = Location::findOrFail($id);

        $this->locationToDeleteId = $location->id;
        $this->locationToDeleteName = $location->name;
        $this->locationToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion modal.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->locationToDeleteId = null;
        $this->locationToDeleteName = '';
        $this->locationToDeleteHashid = '';
    }

    /**
     * Execute deletion after user confirms.
     */
    public function deleteLocation(): void
    {
        abort_unless(
            auth()->user()?->can('delete_locations') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        if (!$this->locationToDeleteId) {
            return;
        }

        $location = Location::findOrFail($this->locationToDeleteId);
        $name = $location->name;

        // Check for child sub-locations
        $childrenCount = $location->children()->count();
        if ($childrenCount > 0) {
            session()->flash('error', "Cannot delete '{$name}' because it contains {$childrenCount} sub-locations. Please reassign or remove them first.");
            $this->cancelDelete();
            return;
        }

        // Check for properties in this location
        $propertiesCount = $location->properties()->count();
        if ($propertiesCount > 0) {
            session()->flash('error', "Cannot delete '{$name}' because {$propertiesCount} properties are located here.");
            $this->cancelDelete();
            return;
        }

        $location->delete();

        \Illuminate\Support\Facades\Cache::forget('locations');

        ActivityLog::record("Deleted location '{$name}'", 'Properties', null);

        $this->cancelDelete();
        session()->flash('status', "Location '{$name}' was successfully deleted.");
    }

    /**
     * Computed property: Options for parent location dropdown,
     * strictly excluding the current editing location and its descendants to avoid infinite loops.
     */
    public function getParentOptionsProperty()
    {
        $query = Location::query()->orderBy('name');

        if ($this->isEditing && $this->editingLocationId) {
            $excludeIds = [$this->editingLocationId];

            $collectDescendantIds = function (array $parentIds) use (&$collectDescendantIds, &$excludeIds) {
                $childIds = Location::whereIn('parent_id', $parentIds)->pluck('id')->toArray();
                if (!empty($childIds)) {
                    $excludeIds = array_merge($excludeIds, $childIds);
                    $collectDescendantIds($childIds);
                }
            };

            $collectDescendantIds([$this->editingLocationId]);

            $query->whereNotIn('id', array_unique($excludeIds));
        }

        return $query->get();
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_locations') || auth()->user()?->can('view_properties'),
            403,
            'Unauthorized.'
        );

        $locations = Location::query()
            ->with(['parent'])
            ->withCount(['children', 'properties'])
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($this->parentFilter === 'root', function (Builder $query) {
                $query->whereNull('parent_id');
            })
            ->when(is_numeric($this->parentFilter), function (Builder $query) {
                $query->where('parent_id', (int) $this->parentFilter);
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-locations', [
            'locations' => $locations,
            'rootLocations' => Location::root()->orderBy('name')->get(),
            'parentOptions' => $this->parentOptions,
        ]);
    }
}

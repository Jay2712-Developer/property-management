<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Property Listings - TISHA Real Estate')]
class ManageProperties extends Component
{
    use WithPagination;

    // Filters & Search
    public string $search = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public string $featuredFilter = '';
    public string $activeFilter = '';

    // Bulk selection state
    public array $selectedProperties = [];
    public bool $selectAll = false;
    public bool $showBulkModal = false;
    public string $bulkAction = ''; // 'delete' or 'toggle_active'

    // Delete single property modal state
    public bool $showDeleteModal = false;
    public ?int $propertyToDeleteId = null;
    public string $propertyToDeleteTitle = '';
    public string $propertyToDeleteHashid = '';

    /**
     * Reset pagination when filters change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFeaturedFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    /**
     * Reset all filters to default.
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->featuredFilter = '';
        $this->activeFilter = '';
        $this->resetPage();
        $this->resetSelection();
    }

    /**
     * Reset bulk selections.
     */
    public function resetSelection(): void
    {
        $this->selectedProperties = [];
        $this->selectAll = false;
    }

    /**
     * Toggle select all on the current query page.
     */
    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedProperties = $this->getPropertiesQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedProperties = [];
        }
    }

    /**
     * Toggle single property active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');

        $id = Property::decodeHashid($hashid);
        $property = Property::findOrFail($id);

        $property->update([
            'is_active' => !$property->is_active,
        ]);

        $statusText = $property->is_active ? 'activated' : 'deactivated';
        ActivityLog::record("Property '{$property->title}' was {$statusText}", 'Properties', $property->id);

        session()->flash('status', "Property '{$property->title}' is now " . ($property->is_active ? 'Active' : 'Inactive') . '.');
    }

    /**
     * Prompt single property delete confirmation.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(auth()->user()?->can('delete_properties'), 403, 'Unauthorized.');

        $id = Property::decodeHashid($hashid);
        $property = Property::findOrFail($id);

        $this->propertyToDeleteId = $property->id;
        $this->propertyToDeleteTitle = $property->title;
        $this->propertyToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel single property delete.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->propertyToDeleteId = null;
        $this->propertyToDeleteTitle = '';
        $this->propertyToDeleteHashid = '';
    }

    /**
     * Delete confirmed property and remove gallery images from storage.
     */
    public function deleteProperty(): void
    {
        abort_unless(auth()->user()?->can('delete_properties'), 403, 'Unauthorized.');

        if (!$this->propertyToDeleteId) {
            return;
        }

        $property = Property::with('images')->findOrFail($this->propertyToDeleteId);
        $title = $property->title;

        // Delete physical gallery images from disk
        foreach ($property->images as $img) {
            if (Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        // Delete property record (cascades image rows and amenities pivot)
        $property->delete();

        ActivityLog::record("Deleted property '{$title}'", 'Properties', null);

        $this->cancelDelete();
        $this->resetSelection();

        session()->flash('status', "Property '{$title}' was successfully deleted.");
    }

    /**
     * Prompt bulk action confirmation modal.
     */
    public function confirmBulkAction(string $action): void
    {
        if (empty($this->selectedProperties)) {
            session()->flash('error', 'Please select at least one property first.');
            return;
        }

        if ($action === 'delete') {
            abort_unless(auth()->user()?->can('delete_properties'), 403, 'Unauthorized.');
        } else {
            abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');
        }

        $this->bulkAction = $action;
        $this->showBulkModal = true;
    }

    /**
     * Cancel bulk action modal.
     */
    public function cancelBulkAction(): void
    {
        $this->showBulkModal = false;
        $this->bulkAction = '';
    }

    /**
     * Execute confirmed bulk action.
     */
    public function executeBulkAction(): void
    {
        if (empty($this->selectedProperties)) {
            $this->cancelBulkAction();
            return;
        }

        $count = count($this->selectedProperties);

        if ($this->bulkAction === 'delete') {
            abort_unless(auth()->user()?->can('delete_properties'), 403, 'Unauthorized.');

            $properties = Property::with('images')->whereIn('id', $this->selectedProperties)->get();

            foreach ($properties as $property) {
                foreach ($property->images as $img) {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                }
                $property->delete();
            }

            ActivityLog::record("Bulk deleted {$count} properties", 'Properties', null);
            session()->flash('status', "Successfully deleted {$count} selected properties.");

        } elseif ($this->bulkAction === 'toggle_active') {
            abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');

            $properties = Property::whereIn('id', $this->selectedProperties)->get();

            foreach ($properties as $property) {
                $property->update(['is_active' => !$property->is_active]);
            }

            ActivityLog::record("Bulk toggled active status for {$count} properties", 'Properties', null);
            session()->flash('status', "Successfully toggled active status for {$count} properties.");
        }

        $this->cancelBulkAction();
        $this->resetSelection();
    }

    /**
     * Build the filtered Eloquent query for properties.
     */
    protected function getPropertiesQuery(): Builder
    {
        return Property::query()
            ->with(['type', 'status', 'location', 'primaryImage'])
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($this->typeFilter, function (Builder $query, string $typeId) {
                $query->where('property_type_id', (int) $typeId);
            })
            ->when($this->statusFilter, function (Builder $query, string $statusId) {
                $query->where('property_status_id', (int) $statusId);
            })
            ->when($this->featuredFilter !== '', function (Builder $query) {
                $query->where('is_featured', (bool) $this->featuredFilter);
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->latest();
    }

    public function render()
    {
        abort_unless(auth()->user()?->can('view_properties'), 403, 'Unauthorized.');

        $properties = $this->getPropertiesQuery()->paginate(10);

        return view('livewire.admin.manage-properties', [
            'properties' => $properties,
            'propertyTypes' => PropertyType::active()->orderBy('name')->get(),
            'propertyStatuses' => PropertyStatus::orderBy('name')->get(),
        ]);
    }
}

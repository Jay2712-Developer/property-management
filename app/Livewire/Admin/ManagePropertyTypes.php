<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Property Types - TISHA Real Estate')]
class ManagePropertyTypes extends Component
{
    use WithPagination;

    // Filters & Search
    public string $search = '';
    public string $activeFilter = '';

    // Modal state for Create / Edit
    public bool $showTypeModal = false;
    public bool $isEditing = false;
    public ?int $editingTypeId = null;
    public string $editingTypeHashid = '';

    // Form inputs
    #[Rule('required|string|min:2|max:100')]
    public string $name = '';

    #[Rule('nullable|string|max:100')]
    public string $icon = 'fa-solid fa-house';

    #[Rule('boolean')]
    public bool $is_active = true;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $typeToDeleteId = null;
    public string $typeToDeleteName = '';
    public string $typeToDeleteHashid = '';

    /**
     * Reset pagination when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new property type.
     */
    public function createType(): void
    {
        abort_unless(
            auth()->user()?->can('create_property_types') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingTypeId = null;
        $this->editingTypeHashid = '';
        $this->name = '';
        $this->icon = 'fa-solid fa-house';
        $this->is_active = true;

        $this->showTypeModal = true;
    }

    /**
     * Open modal to edit an existing property type using Hashid.
     */
    public function editType(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_property_types') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $id = PropertyType::decodeHashid($hashid);
        $type = PropertyType::findOrFail($id);

        $this->isEditing = true;
        $this->editingTypeId = $type->id;
        $this->editingTypeHashid = $hashid;
        $this->name = $type->name;
        $this->icon = $type->icon ?: 'fa-solid fa-house';
        $this->is_active = (bool) $type->is_active;

        $this->showTypeModal = true;
    }

    /**
     * Close Create/Edit modal.
     */
    public function closeTypeModal(): void
    {
        $this->showTypeModal = false;
        $this->editingTypeId = null;
        $this->editingTypeHashid = '';
    }

    /**
     * Save property type (create or update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_property_types') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_property_types') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        }

        $this->validate();

        $nameRule = $this->isEditing
            ? ValidationRule::unique('property_types', 'name')->ignore($this->editingTypeId)
            : ValidationRule::unique('property_types', 'name');

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', $nameRule],
        ]);

        $slug = Str::slug($this->name);

        $data = [
            'name' => $this->name,
            'slug' => $slug,
            'icon' => $this->icon ?: 'fa-solid fa-house',
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->isEditing) {
            $type = PropertyType::findOrFail($this->editingTypeId);
            $type->update($data);
            $message = "Property type '{$type->name}' updated successfully.";

            ActivityLog::record("Updated property type '{$type->name}'", 'Properties', $type->id);
        } else {
            $type = PropertyType::create($data);
            $message = "Property type '{$type->name}' created successfully.";

            ActivityLog::record("Created property type '{$type->name}'", 'Properties', $type->id);
        }

        $this->closeTypeModal();
        session()->flash('status', $message);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_property_types') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = PropertyType::decodeHashid($hashid);
        $type = PropertyType::findOrFail($id);

        $type->update([
            'is_active' => !$type->is_active,
        ]);

        $statusLabel = $type->is_active ? 'Active' : 'Inactive';
        ActivityLog::record("Property type '{$type->name}' status changed to {$statusLabel}", 'Properties', $type->id);

        session()->flash('status', "Property type '{$type->name}' is now {$statusLabel}.");
    }

    /**
     * Open single delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_property_types') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = PropertyType::decodeHashid($hashid);
        $type = PropertyType::findOrFail($id);

        $this->typeToDeleteId = $type->id;
        $this->typeToDeleteName = $type->name;
        $this->typeToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion modal.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->typeToDeleteId = null;
        $this->typeToDeleteName = '';
        $this->typeToDeleteHashid = '';
    }

    /**
     * Execute deletion after user confirms.
     */
    public function deleteType(): void
    {
        abort_unless(
            auth()->user()?->can('delete_property_types') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        if (!$this->typeToDeleteId) {
            return;
        }

        $type = PropertyType::findOrFail($this->typeToDeleteId);
        $name = $type->name;

        // Check if properties exist using this type
        $propertiesCount = $type->properties()->count();
        if ($propertiesCount > 0) {
            session()->flash('error', "Cannot delete property type '{$name}' because {$propertiesCount} properties are associated with it.");
            $this->cancelDelete();
            return;
        }

        $type->delete();

        ActivityLog::record("Deleted property type '{$name}'", 'Properties', null);

        $this->cancelDelete();
        session()->flash('status', "Property type '{$name}' was successfully deleted.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_property_types') || auth()->user()?->can('view_properties'),
            403,
            'Unauthorized.'
        );

        $types = PropertyType::query()
            ->withCount('properties')
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-property-types', [
            'types' => $types,
        ]);
    }
}

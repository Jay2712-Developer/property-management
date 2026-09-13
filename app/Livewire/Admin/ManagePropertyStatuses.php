<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\PropertyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Property Statuses - TISHA Real Estate')]
class ManagePropertyStatuses extends Component
{
    use WithPagination;

    // Search filter
    public string $search = '';

    // Modal state for Create / Edit
    public bool $showStatusModal = false;
    public bool $isEditing = false;
    public ?int $editingStatusId = null;
    public string $editingStatusHashid = '';

    // Form inputs
    #[Rule('required|string|min:2|max:100')]
    public string $name = '';

    #[Rule('required|string|max:20')]
    public string $color_code = '#10B981';

    #[Rule('boolean')]
    public bool $is_system_default = false;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $statusToDeleteId = null;
    public string $statusToDeleteName = '';
    public string $statusToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new property status.
     */
    public function createStatus(): void
    {
        abort_unless(
            auth()->user()?->can('create_property_statuses') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingStatusId = null;
        $this->editingStatusHashid = '';
        $this->name = '';
        $this->color_code = '#10B981';
        $this->is_system_default = false;

        $this->showStatusModal = true;
    }

    /**
     * Open modal to edit an existing property status.
     */
    public function editStatus(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_property_statuses') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $this->resetErrorBag();
        $this->resetValidation();

        $id = PropertyStatus::decodeHashid($hashid);
        $status = PropertyStatus::findOrFail($id);

        $this->isEditing = true;
        $this->editingStatusId = $status->id;
        $this->editingStatusHashid = $hashid;
        $this->name = $status->name;
        $this->color_code = $status->color_code ?: '#10B981';
        $this->is_system_default = (bool) $status->is_system_default;

        $this->showStatusModal = true;
    }

    /**
     * Close modal.
     */
    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->editingStatusId = null;
        $this->editingStatusHashid = '';
    }

    /**
     * Save status (create or update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_property_statuses') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_property_statuses') || auth()->user()?->can('manage_properties'),
                403,
                'Unauthorized.'
            );
        }

        $this->validate();

        $nameRule = $this->isEditing
            ? ValidationRule::unique('property_statuses', 'name')->ignore($this->editingStatusId)
            : ValidationRule::unique('property_statuses', 'name');

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', $nameRule],
        ]);

        $slug = Str::slug($this->name);

        $data = [
            'name' => $this->name,
            'slug' => $slug,
            'color_code' => $this->color_code ?: '#10B981',
            'is_system_default' => (bool) $this->is_system_default,
        ];

        if ($this->isEditing) {
            $status = PropertyStatus::findOrFail($this->editingStatusId);
            $status->update($data);
            $message = "Property status '{$status->name}' updated successfully.";

            ActivityLog::record("Updated property status '{$status->name}'", 'Properties', $status->id);
        } else {
            $status = PropertyStatus::create($data);
            $message = "Property status '{$status->name}' created successfully.";

            ActivityLog::record("Created property status '{$status->name}'", 'Properties', $status->id);
        }

        $this->closeStatusModal();
        session()->flash('status', $message);
    }

    /**
     * Prompt delete confirmation modal (rejects system default statuses).
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_property_statuses') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        $id = PropertyStatus::decodeHashid($hashid);
        $status = PropertyStatus::findOrFail($id);

        if ($status->is_system_default) {
            session()->flash('error', "System default status '{$status->name}' is protected and cannot be deleted.");
            return;
        }

        $this->statusToDeleteId = $status->id;
        $this->statusToDeleteName = $status->name;
        $this->statusToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel delete modal.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->statusToDeleteId = null;
        $this->statusToDeleteName = '';
        $this->statusToDeleteHashid = '';
    }

    /**
     * Execute deletion for non-system-default statuses.
     */
    public function deleteStatus(): void
    {
        abort_unless(
            auth()->user()?->can('delete_property_statuses') || auth()->user()?->can('manage_properties'),
            403,
            'Unauthorized.'
        );

        if (!$this->statusToDeleteId) {
            return;
        }

        $status = PropertyStatus::findOrFail($this->statusToDeleteId);

        // Security check: system default cannot be deleted
        if ($status->is_system_default) {
            session()->flash('error', "Protected status '{$status->name}' cannot be deleted.");
            $this->cancelDelete();
            return;
        }

        $name = $status->name;

        // Check if properties exist using this status
        $propertiesCount = $status->properties()->count();
        if ($propertiesCount > 0) {
            session()->flash('error', "Cannot delete status '{$name}' because {$propertiesCount} properties are assigned to it.");
            $this->cancelDelete();
            return;
        }

        $status->delete();

        ActivityLog::record("Deleted property status '{$name}'", 'Properties', null);

        $this->cancelDelete();
        session()->flash('status', "Property status '{$name}' was successfully deleted.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_property_statuses') || auth()->user()?->can('view_properties'),
            403,
            'Unauthorized.'
        );

        $statuses = PropertyStatus::query()
            ->withCount('properties')
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_system_default')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-property-statuses', [
            'statuses' => $statuses,
        ]);
    }
}

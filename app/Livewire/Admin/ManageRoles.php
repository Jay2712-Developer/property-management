<?php

namespace App\Livewire\Admin;

use App\Facades\HashidsHelper;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('admin.layouts.app')]
#[Title('Role & Permission Management - TISHA Real Estate')]
class ManageRoles extends Component
{
    use WithPagination;

    // Search query for roles list
    public string $search = '';

    // Modal state for Create / Edit
    public bool $showRoleModal = false;
    public bool $isEditing = false;
    public ?int $editingRoleId = null;
    public string $editingRoleHashid = '';

    // Form inputs
    #[Rule('required|string|min:2|max:50')]
    public string $name = '';

    public array $selectedPermissions = [];

    // Delete confirmation state
    public bool $showDeleteModal = false;
    public ?int $roleToDeleteId = null;
    public string $roleToDeleteName = '';

    /**
     * Reset pagination when search query changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get permissions dynamically grouped by functional modules.
     */
    public function getGroupedPermissionsProperty(): array
    {
        $permissions = Permission::orderBy('name')->get();
        $groups = [
            'Properties' => [],
            'Agents' => [],
            'Inquiries & Visits' => [],
            'Content & Media' => [],
            'Administration & System' => [],
            'Other' => [],
        ];

        foreach ($permissions as $permission) {
            $name = strtolower($permission->name);
            if (str_contains($name, 'propert')) {
                $groups['Properties'][] = $permission;
            } elseif (str_contains($name, 'agent')) {
                $groups['Agents'][] = $permission;
            } elseif (str_contains($name, 'inquir') || str_contains($name, 'visit')) {
                $groups['Inquiries & Visits'][] = $permission;
            } elseif (str_contains($name, 'testimonial') || str_contains($name, 'page') || str_contains($name, 'media')) {
                $groups['Content & Media'][] = $permission;
            } elseif (str_contains($name, 'role') || str_contains($name, 'setting') || str_contains($name, 'log') || str_contains($name, 'user')) {
                $groups['Administration & System'][] = $permission;
            } else {
                $groups['Other'][] = $permission;
            }
        }

        return array_filter($groups, fn($group) => count($group) > 0);
    }

    /**
     * Open the modal for creating a new role.
     */
    public function createRole(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingRoleId = null;
        $this->editingRoleHashid = '';
        $this->name = '';
        $this->selectedPermissions = [];

        $this->showRoleModal = true;
    }

    /**
     * Open the modal for editing an existing role.
     */
    /**
     * Decode a role hashid to its integer primary key.
     */
    public function decodeRoleHashid(string $hashid): ?int
    {
        $decoded = HashidsHelper::forModel(Role::class)->decode($hashid);
        return !empty($decoded) ? (int) $decoded[0] : null;
    }

    /**
     * Encode an integer role ID into its Hashid string.
     */
    public function encodeRoleHashid(int $id): string
    {
        return HashidsHelper::forModel(Role::class)->encode($id);
    }

    /**
     * Open the modal for editing an existing role.
     */
    public function editRole(string $hashid): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $roleId = $this->decodeRoleHashid($hashid);
        if (!$roleId) {
            session()->flash('error', 'Invalid role identifier.');
            return;
        }

        $role = Role::with('permissions')->findOrFail($roleId);

        // Super Admin Protection: hardcoded and untouchable
        if ($role->name === 'Super Admin') {
            session()->flash('error', 'The Super Admin role is protected and cannot be edited.');
            return;
        }

        $this->isEditing = true;
        $this->editingRoleId = $role->id;
        $this->editingRoleHashid = $hashid;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        $this->showRoleModal = true;
    }

    /**
     * Save the role (create or update) and sync permissions.
     */
    public function saveRole(): void
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                $this->isEditing
                    ? ValidationRule::unique('roles', 'name')->ignore($this->editingRoleId)
                    : ValidationRule::unique('roles', 'name'),
            ],
            'selectedPermissions' => 'array',
        ];

        $this->validate($rules);

        if ($this->isEditing) {
            $role = Role::findOrFail($this->editingRoleId);

            // Super Admin Protection
            if ($role->name === 'Super Admin') {
                session()->flash('error', 'The Super Admin role is untouchable and cannot be modified.');
                $this->showRoleModal = false;
                return;
            }

            $role->update(['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);

            session()->flash('success', "Role '{$role->name}' updated successfully.");
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($this->selectedPermissions);

            session()->flash('success', "Role '{$role->name}' created successfully with " . count($this->selectedPermissions) . " permissions.");
        }

        // Reset Spatie cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showRoleModal = false;
    }

    /**
     * Toggle selection of all permissions within a specific module group.
     */
    public function toggleGroupSelect(string $group): void
    {
        $groupPermissions = collect($this->groupedPermissions[$group] ?? [])->pluck('name')->toArray();
        if (empty($groupPermissions)) {
            return;
        }

        $allSelected = count(array_intersect($groupPermissions, $this->selectedPermissions)) === count($groupPermissions);

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $groupPermissions));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $groupPermissions)));
        }
    }

    /**
     * Check if all permissions in a given group are selected.
     */
    public function isGroupFullySelected(string $group): bool
    {
        $groupPermissions = collect($this->groupedPermissions[$group] ?? [])->pluck('name')->toArray();
        if (empty($groupPermissions)) {
            return false;
        }

        return count(array_intersect($groupPermissions, $this->selectedPermissions)) === count($groupPermissions);
    }

    /**
     * Select all permissions across all modules.
     */
    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = Permission::pluck('name')->toArray();
    }

    /**
     * Deselect all permissions.
     */
    public function deselectAllPermissions(): void
    {
        $this->selectedPermissions = [];
    }

    /**
     * Show delete confirmation modal for a role.
     */
    public function confirmDelete(string $hashid): void
    {
        $roleId = $this->decodeRoleHashid($hashid);
        if (!$roleId) {
            session()->flash('error', 'Invalid role identifier.');
            return;
        }

        $role = Role::findOrFail($roleId);

        // Super Admin Protection
        if ($role->name === 'Super Admin') {
            session()->flash('error', 'The Super Admin role cannot be deleted.');
            return;
        }

        $this->roleToDeleteId = $role->id;
        $this->roleToDeleteName = $role->name;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the confirmed role.
     */
    public function deleteRole(): void
    {
        if (!$this->roleToDeleteId) {
            return;
        }

        $role = Role::findOrFail($this->roleToDeleteId);

        if ($role->name === 'Super Admin') {
            session()->flash('error', 'The Super Admin role is protected and cannot be deleted.');
            $this->showDeleteModal = false;
            return;
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showDeleteModal = false;
        $this->roleToDeleteId = null;
        $this->roleToDeleteName = '';

        session()->flash('success', "Role '{$roleName}' was deleted successfully.");
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-roles', [
            'roles' => $roles,
            'groupedPermissions' => $this->groupedPermissions,
        ]);
    }
}

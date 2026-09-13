<?php

namespace App\Livewire\Admin;

use App\Facades\HashidsHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('admin.layouts.app')]
#[Title('User Management - TISHA Real Estate')]
class ManageUsers extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';

    // Modal state for Create / Edit
    public bool $showUserModal = false;
    public bool $isEditing = false;
    public ?int $editingUserId = null;
    public string $editingUserHashid = '';

    // Form inputs
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $is_active = true;
    public array $selectedRoles = [];

    // Delete confirmation state
    public bool $showDeleteModal = false;
    public ?int $userToDeleteId = null;
    public string $userToDeleteName = '';

    /**
     * Reset pagination when filters change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Decode a user hashid to its integer primary key.
     */
    public function decodeUserHashid(string $hashid): ?int
    {
        return User::decodeHashid($hashid);
    }

    /**
     * Encode an integer user ID into its Hashid string.
     */
    public function encodeUserHashid(int $id): string
    {
        return HashidsHelper::forModel(User::class)->encode($id);
    }

    /**
     * Open modal to create a new user.
     */
    public function createUser(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditing = false;
        $this->editingUserId = null;
        $this->editingUserHashid = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = true;
        $this->selectedRoles = [];

        $this->showUserModal = true;
    }

    /**
     * Open modal to edit an existing user.
     */
    public function editUser(string $hashid): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $userId = $this->decodeUserHashid($hashid);
        if (!$userId) {
            session()->flash('error', 'Invalid user identifier.');
            return;
        }

        $user = User::with('roles')->findOrFail($userId);

        $this->isEditing = true;
        $this->editingUserId = $user->id;
        $this->editingUserHashid = $hashid;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = (bool) $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        $this->showUserModal = true;
    }

    /**
     * Save user (create or update) with role synchronization and strict security rules.
     */
    public function saveUser(): void
    {
        $rules = [
            'name' => 'required|string|min:2|max:75',
            'email' => [
                'required',
                'email',
                'max:100',
                $this->isEditing
                    ? ValidationRule::unique('users', 'email')->ignore($this->editingUserId)
                    : ValidationRule::unique('users', 'email'),
            ],
            'selectedRoles' => 'required|array|min:1',
            'is_active' => 'boolean',
        ];

        if ($this->isEditing) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        } else {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules, [
            'selectedRoles.required' => 'Please assign at least one role to this user.',
            'selectedRoles.min' => 'Please assign at least one role to this user.',
        ]);

        $currentUser = Auth::user();

        // Security Rule 3: Only a 'Super Admin' can assign the 'Super Admin' role to others
        if (in_array('Super Admin', $this->selectedRoles) && (!$currentUser || !$currentUser->hasRole('Super Admin'))) {
            $this->addError('selectedRoles', "Security Restriction: Only a Super Admin can grant the 'Super Admin' role.");
            return;
        }

        if ($this->isEditing) {
            $user = User::findOrFail($this->editingUserId);

            // Security Rule 2: Cannot remove the 'Super Admin' role from the last remaining Super Admin user
            if ($user->hasRole('Super Admin') && !in_array('Super Admin', $this->selectedRoles)) {
                $superAdminCount = User::role('Super Admin')->count();
                if ($superAdminCount <= 1) {
                    $this->addError('selectedRoles', "Security Restriction: Cannot remove the 'Super Admin' role from the last remaining Super Admin user.");
                    return;
                }
            }

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ];

            // Only update password if provided
            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);
            $user->syncRoles($this->selectedRoles);

            session()->flash('success', "User '{$user->name}' updated successfully.");
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);

            $user->syncRoles($this->selectedRoles);

            session()->flash('success', "User '{$user->name}' created successfully.");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->showUserModal = false;
    }

    /**
     * Show delete confirmation modal with strict security checks.
     */
    public function confirmDelete(string $hashid): void
    {
        $userId = $this->decodeUserHashid($hashid);
        if (!$userId) {
            session()->flash('error', 'Invalid user identifier.');
            return;
        }

        $user = User::findOrFail($userId);

        // Security Rule 1: A user cannot delete their own account
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Security Restriction: You cannot delete your own account.');
            return;
        }

        // Security Rule 2: Cannot delete the last remaining Super Admin user
        if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
            session()->flash('error', "Security Restriction: Cannot delete the last remaining Super Admin user.");
            return;
        }

        $this->userToDeleteId = $user->id;
        $this->userToDeleteName = $user->name;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the confirmed user.
     */
    public function deleteUser(): void
    {
        if (!$this->userToDeleteId) {
            return;
        }

        $user = User::findOrFail($this->userToDeleteId);

        // Security Rule 1: Cannot delete self
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Security Restriction: You cannot delete your own account.');
            $this->showDeleteModal = false;
            return;
        }

        // Security Rule 2: Cannot delete last remaining Super Admin
        if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
            session()->flash('error', 'Security Restriction: Cannot delete the last remaining Super Admin user.');
            $this->showDeleteModal = false;
            return;
        }

        $userName = $user->name;
        $user->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showDeleteModal = false;
        $this->userToDeleteId = null;
        $this->userToDeleteName = '';

        session()->flash('success', "User '{$userName}' was deleted successfully.");
    }

    /**
     * Toggle active/inactive status for a user.
     */
    public function toggleUserStatus(string $hashid): void
    {
        $userId = $this->decodeUserHashid($hashid);
        if (!$userId) {
            return;
        }

        $user = User::findOrFail($userId);

        // Disallow deactivating own account
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Security Restriction: You cannot deactivate your own account.');
            return;
        }

        // Disallow deactivating last super admin
        if ($user->hasRole('Super Admin') && $user->is_active && User::role('Super Admin')->where('is_active', true)->count() <= 1) {
            session()->flash('error', 'Security Restriction: Cannot deactivate the last active Super Admin.');
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        session()->flash('success', "Status for '{$user->name}' changed to " . ($user->is_active ? 'Active' : 'Inactive') . ".");
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->role($this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === '1');
            })
            ->latest()
            ->paginate(10);

        $availableRoles = Role::orderBy('name')->get();

        return view('livewire.admin.manage-users', [
            'users' => $users,
            'availableRoles' => $availableRoles,
        ]);
    }
}

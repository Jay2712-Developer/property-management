<div class="space-y-6">
    
    {{-- Page Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FF6B35]"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-400">
                    Security &amp; Access Control
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mt-1">
                User Management
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Manage administrative portal team members, credentials, and multi-role assignments.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Switch to Roles & Permissions Link --}}
            <a 
                href="{{ route('admin.roles') }}" 
                wire:navigate
                class="hidden md:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] text-xs sm:text-sm font-semibold transition"
            >
                <i class="fa-solid fa-users-gear text-xs"></i>
                <span>Manage Roles</span>
            </a>

            {{-- Create New User Button --}}
            @can('manage_roles')
                <button 
                    wire:click="createUser" 
                    type="button" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e05622] text-white font-bold text-xs sm:text-sm shadow-lg shadow-[#FF6B35]/25 transition-all duration-200 active:scale-95"
                >
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Create New User</span>
                </button>
            @endcan
        </div>
    </div>

    {{-- Notification Alerts --}}
    @if(session()->has('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)"
            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-xs sm:text-sm"
        >
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 6000)"
            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between text-xs sm:text-sm"
        >
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-rose-400 hover:text-rose-600 dark:hover:text-rose-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Users Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl shadow-xs overflow-hidden">
        
        {{-- Card Controls: Search & Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/40 dark:bg-black/10">
            
            {{-- Quick Search --}}
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Search by name or email..." 
                    class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                >
            </div>

            {{-- Role & Status Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Role Filter --}}
                <select 
                    wire:model.live="roleFilter"
                    class="px-3 py-2 bg-white dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                >
                    <option value="">All Roles</option>
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select 
                    wire:model.live="statusFilter"
                    class="px-3 py-2 bg-white dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                >
                    <option value="">All Statuses</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive Only</option>
                </select>
            </div>
        </div>

        {{-- Table View --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800/80 bg-gray-50/70 dark:bg-black/20 text-gray-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3.5 px-6">User</th>
                        <th class="py-3.5 px-6">Assigned Roles</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">2FA Security</th>
                        <th class="py-3.5 px-6">Created At</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                    @forelse($users as $user)
                        @php
                            $isCurrentUser = ($user->id === auth()->id());
                            $userHashid = $user->hashid;
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors">
                            
                            {{-- User Name & Email --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#1A1A1A] to-gray-700 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 dark:text-white truncate block">
                                                {{ $user->name }}
                                            </span>
                                            @if($isCurrentUser)
                                                <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/20 shrink-0">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400 truncate block">
                                            {{ $user->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Roles Badges (Multi-role support with curated theme colors) --}}
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @forelse($user->roles as $role)
                                        @php
                                            $badgeClasses = match($role->name) {
                                                'Super Admin' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                                'Admin' => 'bg-[#FF6B35]/10 text-[#FF6B35] border-[#FF6B35]/20',
                                                'Manager' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                                'Agent' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                                'Viewer' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                                default => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                                            };

                                            $roleIcon = match($role->name) {
                                                'Super Admin' => 'fa-crown',
                                                'Admin' => 'fa-shield-halved',
                                                'Manager' => 'fa-briefcase',
                                                'Agent' => 'fa-user-tie',
                                                'Viewer' => 'fa-eye',
                                                default => 'fa-id-badge',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeClasses }}">
                                            <i class="fa-solid {{ $roleIcon }} text-[10px]"></i>
                                            <span>{{ $role->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No roles assigned</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status Column with Toggle Action --}}
                            <td class="py-4 px-6">
                                <button 
                                    wire:click="toggleUserStatus('{{ $userHashid }}')"
                                    type="button" 
                                    title="{{ $isCurrentUser ? 'Cannot toggle own status' : 'Click to toggle status' }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-200' }}"
                                    {{ $isCurrentUser ? 'disabled' : '' }}
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <span>{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </td>

                            {{-- 2FA Security --}}
                            <td class="py-4 px-6">
                                @if($user->hasTwoFactorEnabled())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                        <i class="fa-solid fa-shield-check text-emerald-500"></i>
                                        <span>Enabled</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                                        <i class="fa-regular fa-circle-xmark text-gray-400"></i>
                                        <span>Off</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Created Date --}}
                            <td class="py-4 px-6 text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Edit User Button (Enforced with @can('manage_roles')) --}}
                                    @can('manage_roles')
                                        <button 
                                            wire:click="editUser('{{ $userHashid }}')" 
                                            type="button" 
                                            title="Edit User"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 text-xs font-semibold shadow-xs transition"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                            <span>Edit</span>
                                        </button>
                                    @endcan

                                    {{-- Delete User Button (Disabled for Self, Enforced with @can('manage_roles')) --}}
                                    @can('manage_roles')
                                        @if($isCurrentUser)
                                            <span 
                                                title="You cannot delete your own account"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-100 dark:border-gray-800 text-gray-300 dark:text-gray-700 text-xs font-semibold cursor-not-allowed"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                                <span>Delete</span>
                                            </span>
                                        @else
                                            <button 
                                                wire:click="confirmDelete('{{ $userHashid }}')" 
                                                type="button" 
                                                title="Delete User"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-xs font-semibold shadow-xs transition"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                                <span>Delete</span>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-users-slash text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold">No users found matching your search or filters.</p>
                                <p class="text-xs text-gray-400 mt-1">Try resetting the search terms or create a new user.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800/80">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ===============================================
         CREATE / EDIT USER MODAL
         =============================================== --}}
    <div 
        x-data
        x-show="$wire.showUserModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div 
            x-show="$wire.showUserModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$wire.showUserModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        {{-- Modal Container --}}
        <div class="min-h-screen px-4 py-8 flex items-center justify-center">
            <div 
                x-show="$wire.showUserModal" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-3xl shadow-2xl overflow-hidden my-6 z-10 flex flex-col max-h-[90vh]"
            >
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800/80 flex items-center justify-between bg-gray-50/50 dark:bg-black/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center font-bold">
                            <i class="{{ $isEditing ? 'fa-solid fa-user-pen' : 'fa-solid fa-user-plus' }} text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">
                                {{ $isEditing ? 'Edit User: ' . $name : 'Create New Team Member' }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Specify account details, login credentials, and assign administrative roles.
                            </p>
                        </div>
                    </div>

                    <button 
                        @click="$wire.showUserModal = false" 
                        type="button" 
                        class="p-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        aria-label="Close modal"
                    >
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Modal Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-5">
                    
                    {{-- Full Name --}}
                    <div>
                        <label for="user-name-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            wire:model="name" 
                            id="user-name-input"
                            type="text" 
                            placeholder="e.g., Victoria Sterling" 
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs sm:text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <label for="user-email-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            wire:model="email" 
                            id="user-email-input"
                            type="email" 
                            placeholder="e.g., victoria@tisharealty.com" 
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs sm:text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Passwords Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Password --}}
                        <div>
                            <label for="user-password-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                Password {{ $isEditing ? '(Leave blank to keep)' : '*' }}
                            </label>
                            <input 
                                wire:model="password" 
                                id="user-password-input"
                                type="password" 
                                placeholder="••••••••" 
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs sm:text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                            >
                            @error('password')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="user-password-confirm-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                Confirm Password
                            </label>
                            <input 
                                wire:model="password_confirmation" 
                                id="user-password-confirm-input"
                                type="password" 
                                placeholder="••••••••" 
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs sm:text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                            >
                        </div>
                    </div>

                    {{-- Status Toggle --}}
                    <div class="p-3.5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-black/10 flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">Active Account</p>
                            <p class="text-xs text-gray-400">Allow this user to sign into the admin portal</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#FF6B35]"></div>
                        </label>
                    </div>

                    {{-- Role Assignment Section (Multi-Role Support) --}}
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Role Assignment <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Select one or more roles to grant permissions to this user.
                                </p>
                            </div>
                        </div>

                        @error('selectedRoles')
                            <p class="mb-3 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                        @enderror

                        @php
                            $isCurrentAuthSuperAdmin = auth()->user() && auth()->user()->hasRole('Super Admin');
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($availableRoles as $role)
                                @php
                                    $isRoleSuperAdmin = ($role->name === 'Super Admin');
                                    $canAssignThisRole = !$isRoleSuperAdmin || $isCurrentAuthSuperAdmin;
                                    $isRoleChecked = in_array($role->name, $selectedRoles);
                                @endphp

                                <label 
                                    class="relative flex items-center justify-between p-3.5 rounded-2xl border transition select-none {{ $canAssignThisRole ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }} {{ $isRoleChecked ? 'border-[#FF6B35] bg-[#FF6B35]/10 text-gray-900 dark:text-white font-bold' : 'border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40 text-gray-700 dark:text-gray-300' }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="selectedRoles" 
                                            value="{{ $role->name }}" 
                                            class="w-4 h-4 rounded text-[#FF6B35] focus:ring-[#FF6B35] border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 accent-[#FF6B35] transition"
                                            {{ $canAssignThisRole ? '' : 'disabled' }}
                                        >
                                        <div>
                                            <span class="text-xs sm:text-sm block">
                                                {{ $role->name }}
                                            </span>
                                            @if($isRoleSuperAdmin)
                                                <span class="text-[10px] text-amber-500 font-semibold block">
                                                    Highest privilege
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isRoleSuperAdmin && !$isCurrentAuthSuperAdmin)
                                        <span class="text-[10px] text-gray-400" title="Only Super Admins can assign this role">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:p-6 border-t border-gray-100 dark:border-gray-800/80 flex items-center justify-end gap-3 bg-gray-50/50 dark:bg-black/20">
                    <button 
                        @click="$wire.showUserModal = false" 
                        type="button" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 text-xs sm:text-sm font-semibold transition"
                    >
                        Cancel
                    </button>

                    <button 
                        wire:click="saveUser" 
                        wire:loading.attr="disabled"
                        type="button" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e05622] text-white text-xs sm:text-sm font-bold shadow-lg shadow-[#FF6B35]/25 transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="saveUser">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>{{ $isEditing ? 'Update User' : 'Save User' }}</span>
                        </span>
                        <span wire:loading wire:target="saveUser" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span>Saving User...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================================
         DELETE USER CONFIRMATION MODAL
         =============================================== --}}
    <div 
        x-data
        x-show="$wire.showDeleteModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div 
            x-show="$wire.showDeleteModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$wire.showDeleteModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        {{-- Modal Dialog --}}
        <div class="min-h-screen px-4 py-8 flex items-center justify-center">
            <div 
                x-show="$wire.showDeleteModal" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-3xl shadow-2xl p-6 z-10 text-center"
            >
                <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-4 text-xl">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>

                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Delete User Account
                </h3>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete the user account for <span class="font-bold text-gray-900 dark:text-white">"{{ $userToDeleteName }}"</span>? This will revoke all active access immediately.
                </p>

                <div class="flex items-center justify-center gap-3 mt-6">
                    <button 
                        @click="$wire.showDeleteModal = false" 
                        type="button" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 text-xs font-semibold transition"
                    >
                        Cancel
                    </button>

                    <button 
                        wire:click="deleteUser" 
                        wire:loading.attr="disabled"
                        type="button" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/25 transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="deleteUser">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                            <span>Confirm Delete</span>
                        </span>
                        <span wire:loading wire:target="deleteUser" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span>Deleting...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

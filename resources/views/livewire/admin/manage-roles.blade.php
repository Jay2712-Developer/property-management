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
                Roles &amp; Permissions
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Define dynamic administrative roles and assign module-level security permissions.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Switch to User Directory Link --}}
            <a 
                href="{{ route('admin.users') }}" 
                wire:navigate
                class="hidden md:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] text-xs sm:text-sm font-semibold transition"
            >
                <i class="fa-solid fa-users text-xs"></i>
                <span>Manage Users</span>
            </a>

            <button 
                wire:click="createRole" 
                type="button" 
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e05622] text-white font-bold text-xs sm:text-sm shadow-lg shadow-[#FF6B35]/25 transition-all duration-200 active:scale-95"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Create New Role</span>
            </button>
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
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-rose-400 hover:text-rose-600 dark:hover:text-rose-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Roles Data Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl shadow-xs overflow-hidden">
        
        {{-- Card Header with Filter / Search --}}
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/40 dark:bg-black/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Active System Roles</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total of {{ $roles->total() }} roles configured</p>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Search roles by name..." 
                    class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                >
            </div>
        </div>

        {{-- Table View --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800/80 bg-gray-50/70 dark:bg-black/20 text-gray-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3.5 px-6">Role Name</th>
                        <th class="py-3.5 px-6">Assigned Users</th>
                        <th class="py-3.5 px-6">Permissions Count</th>
                        <th class="py-3.5 px-6">Created At</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                    @forelse($roles as $role)
                        @php
                            $roleHashid = \App\Facades\HashidsHelper::forModel(\Spatie\Permission\Models\Role::class)->encode($role->id);
                            $isSuperAdmin = ($role->name === 'Super Admin');
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors">
                            
                            {{-- Role Name --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs {{ $isSuperAdmin ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/20' }}">
                                        <i class="{{ $isSuperAdmin ? 'fa-solid fa-crown' : 'fa-solid fa-user-shield' }}"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 dark:text-white block">
                                            {{ $role->name }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">
                                            Guard: {{ $role->guard_name }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Assigned Users --}}
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-users text-[10px] text-gray-400"></i>
                                    <span>{{ $role->users_count }} {{ Str::plural('member', $role->users_count) }}</span>
                                </span>
                            </td>

                            {{-- Permissions Count --}}
                            <td class="py-4 px-6">
                                @if($isSuperAdmin)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <i class="fa-solid fa-infinity text-[10px]"></i>
                                        <span>Full Access (All Permissions)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/20">
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                        <span>{{ $role->permissions_count }} {{ Str::plural('permission', $role->permissions_count) }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Created At --}}
                            <td class="py-4 px-6 text-xs text-gray-500 dark:text-gray-400">
                                {{ $role->created_at ? $role->created_at->format('M d, Y') : 'System Default' }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 text-right">
                                @if($isSuperAdmin)
                                    {{-- Super Admin Protection: Hardcoded, Untouchable, No Edit/Delete Buttons --}}
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="Super Admin is protected and cannot be edited or deleted">
                                        <i class="fa-solid fa-lock text-[10px]"></i>
                                        <span>System Protected</span>
                                    </span>
                                @else
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Edit Button (Uses Hashids) --}}
                                        <button 
                                            wire:click="editRole('{{ $roleHashid }}')" 
                                            type="button" 
                                            title="Edit Role"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 text-xs font-semibold shadow-xs transition"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                            <span>Edit</span>
                                        </button>

                                        {{-- Delete Button (Uses Hashids) --}}
                                        <button 
                                            wire:click="confirmDelete('{{ $roleHashid }}')" 
                                            type="button" 
                                            title="Delete Role"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-xs font-semibold shadow-xs transition"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-users-slash text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold">No roles found matching your query.</p>
                                <p class="text-xs text-gray-400 mt-1">Try refining your search term or create a new role.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($roles->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800/80">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

    {{-- ===============================================
         CREATE / EDIT ROLE MODAL & PERMISSION MATRIX
         =============================================== --}}
    <div 
        x-data
        x-show="$wire.showRoleModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div 
            x-show="$wire.showRoleModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$wire.showRoleModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        {{-- Modal Container --}}
        <div class="min-h-screen px-4 py-8 flex items-center justify-center">
            <div 
                x-show="$wire.showRoleModal" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-4xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-3xl shadow-2xl overflow-hidden my-6 z-10 flex flex-col max-h-[90vh]"
            >
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800/80 flex items-center justify-between bg-gray-50/50 dark:bg-black/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center font-bold">
                            <i class="{{ $isEditing ? 'fa-solid fa-user-pen' : 'fa-solid fa-shield-plus' }} text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">
                                {{ $isEditing ? 'Edit Role: ' . $name : 'Create New Administrative Role' }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Configure the role title and select specific module-level permissions below.
                            </p>
                        </div>
                    </div>

                    <button 
                        @click="$wire.showRoleModal = false" 
                        type="button" 
                        class="p-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        aria-label="Close modal"
                    >
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Modal Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
                    
                    {{-- Role Name Input Field --}}
                    <div>
                        <label for="role-name-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            Role Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            wire:model="name" 
                            id="role-name-input"
                            type="text" 
                            placeholder="e.g., Property Auditor, Senior Realtor, Listing Approver" 
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs sm:text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dynamic Permission Matrix Header & Quick Toggles --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-table-cells text-[#FF6B35]"></i>
                                    <span>Dynamic Permission Matrix</span>
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Currently granted: <span class="font-bold text-[#FF6B35]">{{ count($selectedPermissions) }}</span> permissions
                                </p>
                            </div>

                            {{-- Global Matrix Actions --}}
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="selectAllPermissions" 
                                    type="button" 
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] hover:border-[#FF6B35]/40 transition"
                                >
                                    <i class="fa-solid fa-check-double text-[10px] mr-1"></i>
                                    <span>Select All</span>
                                </button>
                                <button 
                                    wire:click="deselectAllPermissions" 
                                    type="button" 
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:text-rose-500 hover:border-rose-500/40 transition"
                                >
                                    <i class="fa-solid fa-eraser text-[10px] mr-1"></i>
                                    <span>Clear All</span>
                                </button>
                            </div>
                        </div>

                        {{-- Permission Modules Grid --}}
                        <div class="space-y-4">
                            @foreach($groupedPermissions as $groupName => $permissions)
                                @php
                                    $isGroupAllSelected = $this->isGroupFullySelected($groupName);
                                    
                                    // Pick iconic icons for each module
                                    $groupIcon = match($groupName) {
                                        'Properties' => 'fa-building',
                                        'Agents' => 'fa-user-tie',
                                        'Inquiries & Visits' => 'fa-comments',
                                        'Content & Media' => 'fa-newspaper',
                                        'Administration & System' => 'fa-sliders',
                                        default => 'fa-folder-tree',
                                    };
                                @endphp

                                <div class="border border-gray-200 dark:border-gray-800 rounded-2xl p-4 bg-gray-50/50 dark:bg-black/10 transition hover:border-gray-300 dark:hover:border-gray-700">
                                    
                                    {{-- Module Header with Select All in Group --}}
                                    <div class="flex items-center justify-between pb-3 border-b border-gray-200/80 dark:border-gray-800 mb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-xs">
                                                <i class="fa-solid {{ $groupIcon }}"></i>
                                            </div>
                                            <span class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white">
                                                {{ $groupName }}
                                            </span>
                                        </div>

                                        {{-- Group Select All Checkbox Button --}}
                                        <button 
                                            wire:click="toggleGroupSelect('{{ $groupName }}')" 
                                            type="button" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $isGroupAllSelected ? 'bg-[#FF6B35] text-white shadow-xs' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-[#FF6B35]' }}"
                                        >
                                            <i class="{{ $isGroupAllSelected ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle' }} text-[10px]"></i>
                                            <span>{{ $isGroupAllSelected ? 'All Selected' : 'Select Group' }}</span>
                                        </button>
                                    </div>

                                    {{-- Group Permissions Checkbox Grid --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                        @foreach($permissions as $permission)
                                            @php
                                                $isChecked = in_array($permission->name, $selectedPermissions);
                                                
                                                // Format friendly label
                                                $cleanLabel = ucwords(str_replace('_', ' ', $permission->name));
                                            @endphp

                                            <label 
                                                class="relative flex items-center gap-3 p-2.5 rounded-xl border cursor-pointer select-none transition {{ $isChecked ? 'border-[#FF6B35] bg-[#FF6B35]/10 text-gray-900 dark:text-white font-semibold' : 'border-gray-200 dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800/40 text-gray-600 dark:text-gray-300' }}"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    wire:model.live="selectedPermissions" 
                                                    value="{{ $permission->name }}" 
                                                    class="w-4 h-4 rounded text-[#FF6B35] focus:ring-[#FF6B35] border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 accent-[#FF6B35] transition cursor-pointer"
                                                >
                                                <span class="text-xs leading-tight">
                                                    {{ $cleanLabel }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:p-6 border-t border-gray-100 dark:border-gray-800/80 flex items-center justify-end gap-3 bg-gray-50/50 dark:bg-black/20">
                    <button 
                        @click="$wire.showRoleModal = false" 
                        type="button" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 text-xs sm:text-sm font-semibold transition"
                    >
                        Cancel
                    </button>

                    <button 
                        wire:click="saveRole" 
                        wire:loading.attr="disabled"
                        type="button" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e05622] text-white text-xs sm:text-sm font-bold shadow-lg shadow-[#FF6B35]/25 transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="saveRole">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>{{ $isEditing ? 'Update Role' : 'Save New Role' }}</span>
                        </span>
                        <span wire:loading wire:target="saveRole" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span>Saving Role...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================================
         DELETE CONFIRMATION MODAL
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
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Delete Role Confirmation
                </h3>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete the role <span class="font-bold text-gray-900 dark:text-white">"{{ $roleToDeleteName }}"</span>? Any users currently assigned to this role will lose these permissions.
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
                        wire:click="deleteRole" 
                        wire:loading.attr="disabled"
                        type="button" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/25 transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="deleteRole">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                            <span>Confirm Delete</span>
                        </span>
                        <span wire:loading wire:target="deleteRole" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span>Deleting...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

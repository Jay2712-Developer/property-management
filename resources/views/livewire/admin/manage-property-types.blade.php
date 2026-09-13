@canany(['view_property_types', 'view_properties'])
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.properties.index') }}" wire:navigate class="hover:text-[#FF6B35] transition">Properties</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Property Types</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Property Types
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Categorize listings (e.g. Villas, Apartments, Penthouses, Townhouses) across the portal.
            </p>
        </div>

        @canany(['create_property_types', 'manage_properties'])
            <div class="flex items-center gap-3">
                <button type="button" wire:click="createType"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Property Type</span>
                </button>
            </div>
        @endcanany
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search property types by name or slug..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="activeFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Property Types Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4 w-16 text-center">Icon</th>
                        <th class="py-3.5 px-4">Name</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4 text-center">Properties</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($types as $type)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Icon Preview --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B35] flex items-center justify-center text-base shadow-sm">
                                    <i class="{{ $type->icon ?: 'fa-solid fa-house' }}"></i>
                                </div>
                            </td>

                            {{-- Name --}}
                            <td class="py-3.5 px-4 font-bold text-gray-900 dark:text-white">
                                {{ $type->name }}
                            </td>

                            {{-- Slug --}}
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 dark:text-gray-400">
                                {{ $type->slug }}
                            </td>

                            {{-- Count --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                    {{ $type->properties_count }} {{ Str::plural('listing', $type->properties_count) }}
                                </span>
                            </td>

                            {{-- Active Toggle --}}
                            <td class="py-3.5 px-4 text-center">
                                @canany(['edit_property_types', 'manage_properties'])
                                    <button type="button" wire:click="toggleActive('{{ $type->hashid }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $type->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 border border-gray-200 dark:border-gray-700' }}"
                                            title="Click to toggle status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $type->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $type->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $type->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $type->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcanany
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @canany(['edit_property_types', 'manage_properties'])
                                        <button type="button" wire:click="editType('{{ $type->hashid }}')"
                                                class="p-2 text-xs font-semibold rounded-lg text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                                                title="Edit Type">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endcanany

                                    @canany(['delete_property_types', 'manage_properties'])
                                        <button type="button" wire:click="confirmDelete('{{ $type->hashid }}')"
                                                class="p-2 text-xs font-semibold rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                title="Delete Type">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400">
                                <i class="fa-solid fa-shapes text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No property types found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($types->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $types->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal (Alpine.js / Livewire) --}}
    <div x-data="{ show: @entangle('showTypeModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-5"
             @click.outside="$wire.closeTypeModal()">
            
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-[#FF6B35] flex items-center justify-center text-sm">
                        <i class="fa-solid fa-shapes"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $isEditing ? 'Edit Property Type' : 'Add Property Type' }}
                    </h3>
                </div>
                <button type="button" wire:click="closeTypeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                {{-- Name --}}
                <div>
                    <label for="type_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Type Name <span class="text-[#FF6B35]">*</span>
                    </label>
                    <input type="text" id="type_name" wire:model="name"
                           placeholder="e.g. Luxury Villa, Penthouse, Duplex"
                           class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- FontAwesome Icon Class & Preview --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="type_icon" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            FontAwesome Icon Class
                        </label>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            Preview: <i class="{{ $icon ?: 'fa-solid fa-house' }} text-[#FF6B35] text-sm"></i>
                        </span>
                    </div>
                    <div class="relative">
                        <input type="text" id="type_icon" wire:model.live.debounce.250ms="icon"
                               placeholder="fa-solid fa-house"
                               class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    </div>
                    {{-- Common presets for quick pick --}}
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        <span class="text-[10px] text-gray-400">Presets:</span>
                        @foreach(['fa-solid fa-house', 'fa-solid fa-building', 'fa-solid fa-hotel', 'fa-solid fa-warehouse', 'fa-solid fa-city', 'fa-solid fa-tree', 'fa-solid fa-door-open'] as $preset)
                            <button type="button" wire:click="$set('icon', '{{ $preset }}')"
                                    class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-800 hover:bg-[#FF6B35] hover:text-white transition">
                                <i class="{{ $preset }}"></i>
                            </button>
                        @endforeach
                    </div>
                    @error('icon') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Is Active Toggle --}}
                <div class="flex items-center justify-between pt-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">Active Status</span>
                        <span class="text-[11px] text-gray-400">Allow assigning this type to properties</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF6B35]"></div>
                    </label>
                </div>

                {{-- Submit CTA --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeTypeModal"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Save Changes' : 'Create Type' }}</span>
                        <span wire:loading wire:target="save"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal (Alpine.js / Livewire) --}}
    <div x-data="{ show: @entangle('showDeleteModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-5"
             @click.outside="$wire.cancelDelete()">
            
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Property Type</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to remove this property type?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">{{ $typeToDeleteName }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $typeToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteType" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteType">Yes, Delete</span>
                    <span wire:loading wire:target="deleteType"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center bg-white dark:bg-[#1A1A1A] rounded-2xl border border-rose-200 dark:border-rose-900">
    <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-xl mb-3">
        <i class="fa-solid fa-shield-xmark"></i>
    </div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white">Access Denied</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to view property types.</p>
</div>
@endcanany

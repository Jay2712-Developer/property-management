@can('view_properties')
<div class="space-y-6">
    {{-- Header & Primary Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Properties</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Property Portfolio
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Manage luxury listings, pricing, statuses, and digital catalog assets.
            </p>
        </div>

        @can('create_properties')
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.properties.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add New Property</span>
                </a>
            </div>
        @endcan
    </div>

    {{-- Notification Messages --}}
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

    {{-- Filters & Search Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-5 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            {{-- Search Bar --}}
            <div class="lg:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.350ms="search"
                       placeholder="Search by title or slug..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                @if(!empty($search))
                    <button type="button" wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                @endif
            </div>

            {{-- Type Filter --}}
            <div>
                <select wire:model.live="typeFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Property Types</option>
                    @foreach($propertyTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    @foreach($propertyStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Combined Active/Featured Filter or Reset --}}
            <div class="flex items-center gap-2">
                <select wire:model.live="featuredFilter"
                        class="w-1/2 px-2.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">Featured</option>
                    <option value="1">Featured Only</option>
                    <option value="0">Standard Only</option>
                </select>

                <select wire:model.live="activeFilter"
                        class="w-1/2 px-2.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">Active</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive</option>
                </select>

                @if($search || $typeFilter || $statusFilter || $featuredFilter !== '' || $activeFilter !== '')
                    <button type="button" wire:click="resetFilters" title="Reset Filters"
                            class="p-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-rose-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </button>
                @endif
            </div>

        </div>

        {{-- Bulk Actions Active Ribbon --}}
        @if(count($selectedProperties) > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-orange-50/80 dark:bg-orange-950/30 border border-[#FF6B35]/20 animate-fadeIn">
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800 dark:text-gray-200">
                    <span class="w-2 h-2 rounded-full bg-[#FF6B35] animate-pulse"></span>
                    <span>{{ count($selectedProperties) }} {{ Str::plural('property', count($selectedProperties)) }} selected</span>
                </div>

                <div class="flex items-center gap-2">
                    @can('edit_properties')
                        <button type="button" wire:click="confirmBulkAction('toggle_active')"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <i class="fa-solid fa-power-off mr-1 text-[#FF6B35]"></i> Toggle Active
                        </button>
                    @endcan

                    @can('delete_properties')
                        <button type="button" wire:click="confirmBulkAction('delete')"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white transition">
                            <i class="fa-solid fa-trash-can mr-1"></i> Delete Selected
                        </button>
                    @endcan

                    <button type="button" wire:click="resetSelection" class="px-2.5 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        Clear
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Main Properties Data Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                   class="w-4 h-4 rounded text-[#FF6B35] focus:ring-[#FF6B35] border-gray-300 dark:border-gray-700 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4 w-20">Photo</th>
                        <th class="py-3.5 px-4">Title &amp; Location</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Price</th>
                        <th class="py-3.5 px-4 text-center">Featured</th>
                        <th class="py-3.5 px-4 text-center">Active</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($properties as $property)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors {{ in_array($property->id, $selectedProperties) ? 'bg-orange-50/40 dark:bg-orange-950/20' : '' }}">
                            {{-- Checkbox --}}
                            <td class="py-3.5 px-4">
                                <input type="checkbox" value="{{ $property->id }}" wire:model.live="selectedProperties"
                                       class="w-4 h-4 rounded text-[#FF6B35] focus:ring-[#FF6B35] border-gray-300 dark:border-gray-700 cursor-pointer">
                            </td>

                            {{-- Image Thumbnail --}}
                            <td class="py-3.5 px-4">
                                @php
                                    $thumbnail = $property->primaryImage ?? $property->images->first();
                                @endphp
                                @if($thumbnail && $thumbnail->image_path)
                                    <div class="w-14 h-11 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 shadow-sm flex-shrink-0">
                                        <img src="{{ asset('storage/' . $thumbnail->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-14 h-11 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 flex items-center justify-center text-gray-400">
                                        <i class="fa-regular fa-image text-sm"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- Title & Details --}}
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-bold text-gray-900 dark:text-white leading-tight hover:text-[#FF6B35] transition">
                                    @can('edit_properties')
                                        <a href="{{ route('admin.properties.edit', ['propertyId' => $property->hashid]) }}" wire:navigate>
                                            {{ $property->title }}
                                        </a>
                                    @else
                                        {{ $property->title }}
                                    @endcan
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-400 dark:text-gray-500">
                                    @if($property->location)
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-[10px] text-[#FF6B35]"></i>
                                            {{ $property->location->name }}
                                        </span>
                                    @endif
                                    <span>&bull;</span>
                                    <span>{{ $property->bedrooms }} beds</span>
                                    <span>&bull;</span>
                                    <span>{{ $property->bathrooms }} baths</span>
                                </div>
                            </td>

                            {{-- Property Type --}}
                            <td class="py-3.5 px-4 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800">
                                    {{ $property->type->name ?? 'Unassigned' }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-xs">
                                @php
                                    $statusColor = $property->status->color_code ?? '#FF6B35';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                      style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}40;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $statusColor }};"></span>
                                    {{ $property->status->name ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Price --}}
                            <td class="py-3.5 px-4 text-sm font-extrabold text-gray-900 dark:text-white">
                                ${{ number_format($property->price) }}
                                @if($property->price_label)
                                    <span class="block text-[10px] font-normal text-gray-400">
                                        {{ $property->price_label }}
                                    </span>
                                @endif
                            </td>

                            {{-- Featured Badge --}}
                            <td class="py-3.5 px-4 text-center">
                                @if($property->is_featured)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-500 shadow-sm" title="Featured Property">
                                        <i class="fa-solid fa-star text-xs"></i>
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600" title="Standard Listing">
                                        <i class="fa-regular fa-star text-xs"></i>
                                    </span>
                                @endif
                            </td>

                            {{-- Active Toggle / Badge --}}
                            <td class="py-3.5 px-4 text-center">
                                @can('edit_properties')
                                    <button type="button" wire:click="toggleActive('{{ $property->hashid }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $property->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 border border-gray-200 dark:border-gray-700' }}"
                                            title="Click to toggle active listing status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $property->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $property->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $property->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $property->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('edit_properties')
                                        <a href="{{ route('admin.properties.edit', ['propertyId' => $property->hashid]) }}" wire:navigate
                                           class="p-2 text-xs font-semibold rounded-lg text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                                           title="Edit Property">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcan

                                    @can('delete_properties')
                                        <button type="button" wire:click="confirmDelete('{{ $property->hashid }}')"
                                                class="p-2 text-xs font-semibold rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                title="Delete Property">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-orange-50 dark:bg-orange-950/30 text-[#FF6B35] flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-building-circle-exclamation"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">No properties found</h3>
                                    <p class="text-xs text-gray-400">Try adjusting your filters or search keywords.</p>
                                    @if($search || $typeFilter || $statusFilter || $featuredFilter !== '' || $activeFilter !== '')
                                        <button type="button" wire:click="resetFilters" class="text-xs font-semibold text-[#FF6B35] hover:underline pt-2 inline-block">
                                            Reset all filters
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($properties->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $properties->links() }}
            </div>
        @endif
    </div>

    {{-- Single Property Delete Confirmation Modal (Alpine.js / Livewire) --}}
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Property</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">This action will permanently purge the property and its gallery images.</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block truncate mb-0.5">{{ $propertyToDeleteTitle }}</span>
                <span>Encrypted Hashid: <code class="font-mono text-[11px] text-[#FF6B35]">{{ $propertyToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteProperty" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteProperty">Yes, Delete Property</span>
                    <span wire:loading wire:target="deleteProperty"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Bulk Action Confirmation Modal (Alpine.js / Livewire) --}}
    <div x-data="{ show: @entangle('showBulkModal') }"
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
             @click.outside="$wire.cancelBulkAction()">
            
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl {{ $bulkAction === 'delete' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600' : 'bg-orange-50 dark:bg-orange-950/40 text-[#FF6B35]' }} flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid {{ $bulkAction === 'delete' ? 'fa-triangle-exclamation' : 'fa-bolt' }}"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">
                        {{ $bulkAction === 'delete' ? 'Confirm Bulk Deletion' : 'Confirm Bulk Status Toggle' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        You have selected <span class="font-bold text-gray-900 dark:text-white">{{ count($selectedProperties) }}</span> properties for this batch operation.
                    </p>
                </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-400">
                @if($bulkAction === 'delete')
                    Are you sure you want to permanently delete all selected properties and their associated gallery images? This cannot be undone.
                @else
                    This will toggle the active listing visibility for all selected properties simultaneously.
                @endif
            </p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelBulkAction"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="executeBulkAction" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl {{ $bulkAction === 'delete' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#FF6B35] hover:bg-[#e05a2b]' }} text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="executeBulkAction">Execute Batch Action</span>
                    <span wire:loading wire:target="executeBulkAction"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Processing...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have the required permission to view property listings.</p>
</div>
@endcan

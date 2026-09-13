@canany(['view_testimonials', 'manage_testimonials'])
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Testimonials</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Client Testimonials &amp; Reviews
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Curate ratings, reviews, and client recommendations displayed across public property pages.
            </p>
        </div>

        @canany(['create_testimonials', 'manage_testimonials'])
            <div class="flex items-center gap-3">
                <button type="button" wire:click="openCreateModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Testimonial</span>
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

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search testimonials by client name or review content..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="activeFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Testimonials</option>
                    <option value="1">Active Only</option>
                    <option value="0">Hidden / Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Testimonials Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4 w-16 text-center">Photo</th>
                        <th class="py-3.5 px-4">Client Name</th>
                        <th class="py-3.5 px-4">Rating</th>
                        <th class="py-3.5 px-4">Review Message</th>
                        <th class="py-3.5 px-4 text-center">Sort Order</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($testimonials as $testimonial)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Photo Thumbnail --}}
                            <td class="py-3.5 px-4 text-center">
                                @if($testimonial->client_photo)
                                    <div class="w-10 h-10 mx-auto rounded-full overflow-hidden border border-[#FF6B35]/20 shadow-sm flex-shrink-0">
                                        <img src="{{ asset('storage/' . $testimonial->client_photo) }}" alt="{{ $testimonial->client_name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-10 h-10 mx-auto rounded-full bg-gradient-to-tr from-[#FF6B35] to-[#ff946d] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($testimonial->client_name, 0, 2)) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Client Name --}}
                            <td class="py-3.5 px-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $testimonial->client_name }}
                            </td>

                            {{-- Rating Stars --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $testimonial->rating)
                                            <i class="fa-solid fa-star text-xs"></i>
                                        @else
                                            <i class="fa-regular fa-star text-xs text-gray-300 dark:text-gray-600"></i>
                                        @endif
                                    @endfor
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 ml-1.5">({{ $testimonial->rating }}/5)</span>
                                </div>
                            </td>

                            {{-- Review Message --}}
                            <td class="py-3.5 px-4 text-xs text-gray-600 dark:text-gray-300 max-w-sm">
                                <span class="italic block line-clamp-2">"{{ $testimonial->message }}"</span>
                            </td>

                            {{-- Sort Order --}}
                            <td class="py-3.5 px-4 text-center text-xs font-mono text-gray-500 dark:text-gray-400">
                                {{ $testimonial->sort_order }}
                            </td>

                            {{-- Active Status --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @canany(['edit_testimonials', 'manage_testimonials'])
                                    <button type="button" wire:click="toggleActive('{{ $testimonial->hashid }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $testimonial->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border border-gray-200 dark:border-gray-700 hover:bg-gray-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $testimonial->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $testimonial->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border border-gray-200 dark:border-gray-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $testimonial->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</span>
                                    </span>
                                @endcanany
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Edit Button --}}
                                    @canany(['edit_testimonials', 'manage_testimonials'])
                                        <button type="button"
                                                wire:click="openEditModal('{{ $testimonial->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-500 hover:text-[#FF6B35] hover:bg-[#FF6B35]/10 dark:hover:bg-[#FF6B35]/20 transition"
                                                title="Edit Testimonial">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endcanany

                                    {{-- Delete Button --}}
                                    @canany(['delete_testimonials', 'manage_testimonials'])
                                        <button type="button"
                                                wire:click="confirmDelete('{{ $testimonial->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                                title="Delete Testimonial">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fa-regular fa-star text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No testimonials found</p>
                                <p class="text-xs text-gray-400 mt-0.5">Add client testimonials to enhance credibility on the website.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($testimonials->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $testimonials->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Testimonial Modal --}}
    <div x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-lg w-full p-6 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-5"
             @click.outside="$wire.closeModal()">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-quote-left"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $isEditing ? 'Edit Testimonial' : 'Create New Testimonial' }}
                        </h3>
                        <p class="text-[11px] text-gray-400">Share what satisfied clients say about your services.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4 text-xs">
                {{-- Client Name --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Client Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="client_name"
                           placeholder="e.g. Marcus Sterling"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    @error('client_name') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Photo Upload with Preview --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Client Photo <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        {{-- Photo Preview --}}
                        <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-700 flex-shrink-0 bg-gray-100 dark:bg-[#141414] flex items-center justify-center">
                            @if ($client_photo)
                                <img src="{{ $client_photo->temporaryUrl() }}" alt="New Photo Preview" class="w-full h-full object-cover">
                            @elseif ($existingPhoto)
                                <img src="{{ asset('storage/' . $existingPhoto) }}" alt="Existing Photo" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-user text-gray-400 text-lg"></i>
                            @endif
                        </div>

                        <div class="flex-1">
                            <input type="file" wire:model="client_photo" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FF6B35]/10 file:text-[#FF6B35] hover:file:bg-[#FF6B35]/20 cursor-pointer">
                            <span class="text-[10px] text-gray-400 mt-1 block">JPG, PNG, WEBP up to 2MB.</span>
                        </div>
                    </div>
                    @error('client_photo') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Rating Stars Selection --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Rating <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})"
                                    class="p-1 text-xl transition transform hover:scale-110 {{ $rating >= $i ? 'text-amber-400' : 'text-gray-300 dark:text-gray-700' }}">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        @endfor
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 ml-2">{{ $rating }} / 5 Stars</span>
                    </div>
                    @error('rating') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Review Message --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Testimonial Message <span class="text-rose-500">*</span>
                    </label>
                    <textarea wire:model="message" rows="4"
                              placeholder="Write client statement or review..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition"></textarea>
                    @error('message') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Sort Order & Active Toggle in 2 cols --}}
                <div class="grid grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Sort Order <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="0" wire:model="sort_order"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                        @error('sort_order') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <label class="flex items-center gap-2.5 mt-2.5 cursor-pointer">
                            <input type="checkbox" wire:model="is_active"
                                   class="w-4 h-4 text-[#FF6B35] rounded border-gray-300 dark:border-gray-700 focus:ring-[#FF6B35]">
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Active / Published</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="save, client_photo">{{ $isEditing ? 'Update Testimonial' : 'Save Testimonial' }}</span>
                        <span wire:loading wire:target="save, client_photo"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...</span>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Testimonial</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to permanently delete this testimonial?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">{{ $testimonialToDeleteName }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $testimonialToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteTestimonial" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteTestimonial">Yes, Delete Testimonial</span>
                    <span wire:loading wire:target="deleteTestimonial"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to view testimonials.</p>
</div>
@endcanany

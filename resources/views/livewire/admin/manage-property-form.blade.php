@can($isEditing ? 'edit_properties' : 'create_properties')
<div class="space-y-6">
    {{-- Top Action Bar & Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300">Properties</span>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">{{ $isEditing ? 'Edit Property' : 'Create Property' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ $isEditing ? 'Edit: ' . $title : 'Create New Property' }}
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" wire:navigate
               class="px-4 py-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Cancel
            </a>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                <span wire:loading wire:target="save">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span>{{ $isEditing ? 'Update Property' : 'Publish Property' }}</span>
            </button>
        </div>
    </div>

    {{-- Status Flash Message --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="text-sm font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Form Container --}}
    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- MAIN COLUMN (2 Spans) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Basic Information Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                            <i class="fa-solid fa-circle-info text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Basic Information</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Title, slug URL, and detailed property overview</p>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Property Title <span class="text-[#FF6B35]">*</span>
                        </label>
                        <input type="text" id="title" wire:model.live.debounce.400ms="title" wire:blur="generateSlug"
                               placeholder="e.g. Modern Luxury Villa with Panoramic Ocean Views"
                               class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                        @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                URL Slug <span class="text-[#FF6B35]">*</span>
                            </label>
                            <button type="button" wire:click="generateSlug" class="text-xs text-[#FF6B35] hover:underline font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Auto-Generate
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-xs text-gray-400">/properties/</span>
                            <input type="text" id="slug" wire:model="slug"
                                   placeholder="modern-luxury-villa-ocean-views"
                                   class="w-full pl-24 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                        </div>
                        @error('slug') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Detailed Description
                        </label>
                        <textarea id="description" rows="5" wire:model="description"
                                  placeholder="Describe the property highlights, neighborhood, architectural finishings, and lifestyle features..."
                                  class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition"></textarea>
                        @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 2. Property Specifications & Pricing --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                            <i class="fa-solid fa-calculator text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Pricing &amp; Key Specifications</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Dimensions, capacity, and financial terms</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Price --}}
                        <div>
                            <label for="price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Price (₹) <span class="text-[#FF6B35]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-gray-400 font-bold">₹</span>
                                <input type="number" step="0.01" id="price" wire:model="price"
                                       placeholder="24500000"
                                       class="w-full pl-8 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            </div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                                Enter amount in Rupees &mdash; e.g. <strong class="text-gray-600 dark:text-gray-300">24500000</strong> for ₹2.45 Cr &nbsp;|&nbsp; <strong class="text-gray-600 dark:text-gray-300">4500000</strong> for ₹45.00 L
                            </p>
                            @error('price') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>


                        {{-- Price Label --}}
                        <div>
                            <label for="price_label" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Price Label / Frequency
                            </label>
                            <input type="text" id="price_label" wire:model="price_label"
                                   placeholder="e.g. /month, Guide Price, or Negotiable"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            @error('price_label') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 pt-2">
                        {{-- Bedrooms --}}
                        <div>
                            <label for="bedrooms" class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-solid fa-bed mr-1 text-[#FF6B35]"></i> Beds <span class="text-[#FF6B35]">*</span>
                            </label>
                            <input type="number" min="0" max="99" id="bedrooms" wire:model="bedrooms"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
                            @error('bedrooms') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Bathrooms --}}
                        <div>
                            <label for="bathrooms" class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-solid fa-bath mr-1 text-[#FF6B35]"></i> Baths <span class="text-[#FF6B35]">*</span>
                            </label>
                            <input type="number" min="0" max="99" id="bathrooms" wire:model="bathrooms"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
                            @error('bathrooms') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Sqft --}}
                        <div>
                            <label for="sqft" class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-solid fa-ruler-combined mr-1 text-[#FF6B35]"></i> Sq Ft
                            </label>
                            <input type="number" min="0" id="sqft" wire:model="sqft" placeholder="e.g. 2400"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
                            @error('sqft') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Garage --}}
                        <div>
                            <label for="garage" class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-solid fa-warehouse mr-1 text-[#FF6B35]"></i> Garage
                            </label>
                            <input type="number" min="0" max="50" id="garage" wire:model="garage"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
                            @error('garage') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Year Built --}}
                        <div>
                            <label for="year_built" class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-regular fa-calendar mr-1 text-[#FF6B35]"></i> Year Built
                            </label>
                            <input type="number" min="1800" max="2099" id="year_built" wire:model="year_built" placeholder="2025"
                                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
                            @error('year_built') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 3. Multi-Image Upload & Gallery Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                                <i class="fa-solid fa-images text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900 dark:text-white">Property Media &amp; Gallery</h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Upload multiple photos. The first image is highlighted as primary.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Dropzone --}}
                    <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-[#FF6B35] dark:hover:border-[#FF6B35] rounded-2xl p-6 text-center transition-colors bg-gray-50/50 dark:bg-[#141414]">
                        <input type="file" wire:model="newImages" multiple accept="image/*" id="images-upload"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-full bg-orange-50 dark:bg-orange-950/40 text-[#FF6B35] flex items-center justify-center">
                                <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Click or drag &amp; drop images here
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    PNG, JPG, WEBP up to 5MB each. Multi-selection supported.
                                </p>
                            </div>
                        </div>

                        {{-- Upload Progress Indicator --}}
                        <div wire:loading wire:target="newImages" class="mt-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#FF6B35]/10 text-[#FF6B35] text-xs font-semibold">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Uploading images to temporary buffer...</span>
                            </div>
                        </div>
                    </div>
                    @error('newImages.*') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror

                    {{-- Newly Uploaded Previews --}}
                    @if (!empty($newImages))
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center justify-between">
                                <span>Pending Uploads ({{ count($newImages) }})</span>
                                <span class="text-[#FF6B35] text-[11px] lowercase">ready to save</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($newImages as $index => $file)
                                    <div class="relative group rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-800 aspect-video shadow-sm">
                                        <img src="{{ $file->temporaryUrl() }}" alt="New Preview" class="w-full h-full object-cover">
                                        @if($index === 0 && empty($existingImages))
                                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-[#FF6B35] text-white shadow">
                                                Primary
                                            </span>
                                        @endif
                                        <button type="button" wire:click="removeNewImage({{ $index }})"
                                                class="absolute top-2 right-2 w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shadow hover:bg-rose-700 transition"
                                                title="Remove this photo">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Existing Saved Images (Edit Mode) --}}
                    @if ($isEditing && !empty($existingImages))
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                                Persisted Gallery ({{ count($existingImages) }})
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($existingImages as $img)
                                    <div class="relative group rounded-xl overflow-hidden border {{ $img['is_primary'] ? 'border-[#FF6B35] ring-2 ring-[#FF6B35]/20' : 'border-gray-200 dark:border-gray-800' }} bg-gray-100 dark:bg-gray-800 aspect-video shadow-sm">
                                        <img src="{{ asset('storage/' . $img['image_path']) }}" alt="Property Image" class="w-full h-full object-cover">
                                        
                                        @if($img['is_primary'])
                                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-[#FF6B35] text-white shadow">
                                                Primary
                                            </span>
                                        @else
                                            <button type="button" wire:click="setAsPrimary({{ $img['id'] }})"
                                                    class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-medium bg-black/60 text-white hover:bg-[#FF6B35] transition shadow">
                                                Set Primary
                                            </button>
                                        @endif

                                        <button type="button" wire:click="removeExistingImage({{ $img['id'] }})"
                                                wire:confirm="Are you sure you want to permanently delete this photo?"
                                                class="absolute top-2 right-2 w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shadow hover:bg-rose-700 transition"
                                                title="Delete photo">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. Amenities Matrix Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                            <i class="fa-solid fa-spa text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Amenities &amp; Features</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Select interior, exterior, and community perks</p>
                        </div>
                    </div>

                    @forelse($amenitiesByCategory as $category => $categoryAmenities)
                        <div class="space-y-3 pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#FF6B35]">
                                {{ $category ?? 'General' }} Features
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($categoryAmenities as $amenity)
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] hover:border-[#FF6B35]/50 transition cursor-pointer">
                                        <input type="checkbox" value="{{ $amenity->id }}" wire:model="selectedAmenities"
                                               class="w-4 h-4 rounded text-[#FF6B35] focus:ring-[#FF6B35] border-gray-300 dark:border-gray-700">
                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">
                                            {{ $amenity->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No amenities configured yet.</p>
                    @endforelse
                </div>

            </div>

            {{-- SIDEBAR COLUMN (1 Span) --}}
            <div class="space-y-6">

                {{-- 1. Publishing & Visibility Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <span>Publishing Status</span>
                        <span class="w-2.5 h-2.5 rounded-full {{ $is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    </h2>

                    {{-- Property Status Dropdown --}}
                    <div>
                        <label for="property_status_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Status <span class="text-[#FF6B35]">*</span>
                        </label>
                        <select id="property_status_id" wire:model="property_status_id"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            <option value="">Select Status</option>
                            @foreach($propertyStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('property_status_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Active Toggle Switch --}}
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 block">Active Listing</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Visible to public search &amp; portals</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF6B35]"></div>
                        </label>
                    </div>

                    {{-- Featured Toggle Switch --}}
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 block">Featured Listing</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Spotlighted on homepage &amp; banners</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_featured" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF6B35]"></div>
                        </label>
                    </div>

                    {{-- Submit CTA --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-3 px-4 rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white font-bold text-sm shadow-md shadow-[#FF6B35]/25 transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading wire:target="save">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </span>
                            <span>{{ $isEditing ? 'Save Changes' : 'Create &amp; Publish' }}</span>
                        </button>
                    </div>
                </div>

                {{-- 2. Classification & Assignment Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-800">
                        Classification &amp; Assignment
                    </h2>

                    {{-- Property Type --}}
                    <div>
                        <label for="property_type_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Property Type <span class="text-[#FF6B35]">*</span>
                        </label>
                        <select id="property_type_id" wire:model="property_type_id"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            <option value="">Select Property Type</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('property_type_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label for="location_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Location / Area <span class="text-[#FF6B35]">*</span>
                        </label>
                        <select id="location_id" wire:model="location_id"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            <option value="">Select Location</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Assigned Agent --}}
                    <div>
                        <label for="agent_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Assigned Agent (Optional)
                        </label>
                        <select id="agent_id" wire:model="agent_id"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 focus:outline-none transition">
                            <option value="">No Agent Assigned</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->designation ?? 'Agent' }})</option>
                            @endforeach
                        </select>
                        @error('agent_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 3. Audit Meta (Edit Mode) --}}
                @if ($isEditing && $property)
                    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-5 border border-gray-200/80 dark:border-gray-800 shadow-sm text-xs space-y-2.5 text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-between">
                            <span>Hashid ID:</span>
                            <span class="font-mono text-gray-900 dark:text-gray-200 font-semibold">{{ $property->hashid }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Created At:</span>
                            <span class="text-gray-800 dark:text-gray-300">{{ $property->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Last Updated:</span>
                            <span class="text-gray-800 dark:text-gray-300">{{ $property->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </form>
</div>
@else
<div class="p-8 text-center bg-white dark:bg-[#1A1A1A] rounded-2xl border border-rose-200 dark:border-rose-900">
    <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-xl mb-3">
        <i class="fa-solid fa-shield-xmark"></i>
    </div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white">Access Denied</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have the required permission to manage properties.</p>
</div>
@endcan

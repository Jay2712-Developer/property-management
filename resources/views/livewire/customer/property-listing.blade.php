<div>
    {{-- ====================================================================== --}}
    {{-- 1. ADVANCED FILTER BAR                                                 --}}
    {{-- ====================================================================== --}}
    <div class="mb-10 p-5 sm:p-6 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Search Keyword --}}
            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                <label for="filter-search" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                    Search Title / Keyword
                </label>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-[#FF6B35] text-xs"></i>
                    <input 
                        type="text" 
                        id="filter-search"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Penthouse, Marina..." 
                        class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                    >
                    @if($search)
                        <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Location Filter --}}
            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                <label for="filter-location" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                    Location / District
                </label>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-[#FF6B35] text-xs"></i>
                    <select 
                        id="filter-location"
                        wire:model.live="location_id"
                        class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white focus:outline-none cursor-pointer"
                    >
                        <option value="" class="dark:bg-[#1A1A1A]">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" class="dark:bg-[#1A1A1A]">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Property Type Filter --}}
            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                <label for="filter-type" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                    Property Type
                </label>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-building text-[#FF6B35] text-xs"></i>
                    <select 
                        id="filter-type"
                        wire:model.live="type_id"
                        class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white focus:outline-none cursor-pointer"
                    >
                        <option value="" class="dark:bg-[#1A1A1A]">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" class="dark:bg-[#1A1A1A]">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bedrooms Filter --}}
            <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                <label for="filter-bedrooms" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                    Bedrooms
                </label>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bed text-[#FF6B35] text-xs"></i>
                    <select 
                        id="filter-bedrooms"
                        wire:model.live="bedrooms"
                        class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white focus:outline-none cursor-pointer"
                    >
                        <option value="" class="dark:bg-[#1A1A1A]">Any Bedrooms</option>
                        <option value="1" class="dark:bg-[#1A1A1A]">1 Bedroom</option>
                        <option value="2" class="dark:bg-[#1A1A1A]">2 Bedrooms</option>
                        <option value="3" class="dark:bg-[#1A1A1A]">3 Bedrooms</option>
                        <option value="4" class="dark:bg-[#1A1A1A]">4 Bedrooms</option>
                        <option value="5+" class="dark:bg-[#1A1A1A]">5+ Bedrooms</option>
                    </select>
                </div>
            </div>

        </div>

        {{-- Row 2: Price Range & Sort Bar --}}
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800/80 flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Price Inputs (raw INR integers; display uses ₹ symbol) --}}
            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 w-full sm:w-44">
                    <span class="text-[#FF6B35] font-bold shrink-0">₹</span>
                    <input 
                        type="number" 
                        wire:model.live.debounce.400ms="min_price" 
                        placeholder="Min (e.g. 5000000)"
                        class="w-full bg-transparent text-xs focus:outline-none"
                    >
                </div>
                <span class="text-gray-400 text-xs font-bold">–</span>
                <div class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 w-full sm:w-44">
                    <span class="text-[#FF6B35] font-bold shrink-0">₹</span>
                    <input 
                        type="number" 
                        wire:model.live.debounce.400ms="max_price" 
                        placeholder="Max (e.g. 50000000)"
                        class="w-full bg-transparent text-xs focus:outline-none"
                    >
                </div>
            </div>


            {{-- Right: Sort Order & Reset Button --}}
            <div class="flex items-center justify-between sm:justify-end gap-3 w-full md:w-auto">
                
                {{-- Sort Dropdown --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-arrow-down-wide-short text-[#FF6B35]"></i>
                    <select wire:model.live="sort" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer">
                        <option value="latest" class="dark:bg-[#1A1A1A]">Featured & Latest</option>
                        <option value="price_low" class="dark:bg-[#1A1A1A]">Price: Low to High</option>
                        <option value="price_high" class="dark:bg-[#1A1A1A]">Price: High to Low</option>
                        <option value="oldest" class="dark:bg-[#1A1A1A]">Oldest Listings</option>
                    </select>
                </div>

                {{-- Reset Button --}}
                @if($search || $location_id || $type_id || $bedrooms || $min_price || $max_price || $sort !== 'latest')
                    <button 
                        wire:click="resetFilters" 
                        type="button" 
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] transition flex items-center gap-1.5 shadow-sm shadow-[#FF6B35]/30"
                    >
                        <i class="fa-solid fa-rotate-left text-[11px]"></i>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </div>

        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- 2. LISTING HEADER & COUNT BAR                                          --}}
    {{-- ====================================================================== --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-gray-900 dark:text-white">
                Showing {{ $properties->firstItem() ?? 0 }} - {{ $properties->lastItem() ?? 0 }} of {{ $totalCount }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Residences</span>
        </div>

        {{-- Active Status Pill --}}
        @if($status)
            <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ stripos($status, 'rent') !== false ? 'bg-[#1A1A1A] text-white border border-gray-700' : 'bg-[#FF6B35] text-white shadow-sm shadow-[#FF6B35]/30' }}">
                {{ stripos($status, 'rent') !== false ? 'For Rent' : 'For Sale' }}
            </span>
        @endif
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading.flex class="items-center justify-center py-16">
        <div class="flex items-center gap-3 text-sm font-semibold text-gray-600 dark:text-gray-300">
            <i class="fa-solid fa-spinner fa-spin text-[#FF6B35] text-xl"></i>
            <span>Filtering luxury portfolio...</span>
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- 3. PROPERTY CARDS GRID                                                 --}}
    {{-- ====================================================================== --}}
    <div wire:loading.remove>
        @if($properties->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    <x-customer.property-card :property="$property" :key="'listing-prop-'.$property->id" />
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-12">
                {{ $properties->links() }}
            </div>
        @else
            {{-- Empty Results --}}
            <div class="p-16 text-center rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-house-chimney-crack"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                    No Matching Properties Found
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6 leading-relaxed">
                    We could not find any residences matching your specific filter criteria. Try adjusting your price range, location, or bedrooms.
                </p>
                <button 
                    wire:click="resetFilters" 
                    type="button" 
                    class="px-6 py-3 rounded-2xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-[#FF6B35]/30"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>
</div>

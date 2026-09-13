<div>
    {{-- Type Filter Tabs --}}
    @if($types->isNotEmpty())
        <div class="flex items-center justify-center flex-wrap gap-2 mb-10">
            <button 
                wire:click="filterByType('all')" 
                type="button"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $typeFilter === 'all' ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/30' : 'bg-gray-100 dark:bg-[#262626] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#333333]' }}"
            >
                All Categories
            </button>

            @foreach($types as $type)
                <button 
                    wire:click="filterByType('{{ $type->slug }}')" 
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $typeFilter === $type->slug ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/30' : 'bg-gray-100 dark:bg-[#262626] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#333333]' }}"
                >
                    {{ $type->name }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex class="items-center justify-center py-12">
        <div class="flex items-center gap-3 text-sm font-semibold text-gray-500 dark:text-gray-400">
            <i class="fa-solid fa-spinner fa-spin text-[#FF6B35] text-lg"></i>
            <span>Updating exclusive portfolio...</span>
        </div>
    </div>

    {{-- Grid of Featured Properties --}}
    <div wire:loading.remove>
        @if($properties->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    <x-customer.property-card :property="$property" :key="'featured-prop-' . $property->id" />
                @endforeach
            </div>
        @else
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-[#262626] text-gray-400 dark:text-gray-500 flex items-center justify-center mx-auto mb-4 text-xl">
                    <i class="fa-regular fa-building"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                    No Featured Properties Available
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-4">
                    Our curators are currently preparing our next release of exclusive estates.
                </p>
                <button 
                    wire:click="filterByType('all')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] transition"
                >
                    Reset Filter
                </button>
            </div>
        @endif
    </div>
</div>

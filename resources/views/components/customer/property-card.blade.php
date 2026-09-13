@props(['property'])

@php
    // 1. Primary Image Resolution
    $imageUrl = null;
    if (method_exists($property, 'getFirstMediaUrl') && $property->getFirstMediaUrl('images')) {
        $imageUrl = $property->getFirstMediaUrl('images');
    } elseif (!empty($property->primary_image_url)) {
        $imageUrl = $property->primary_image_url;
    } elseif ($property->relationLoaded('primaryImage') && $property->primaryImage?->image_path) {
        $imageUrl = asset('storage/' . $property->primaryImage->image_path);
    } elseif ($property->relationLoaded('images') && $property->images->isNotEmpty()) {
        $imageUrl = asset('storage/' . $property->images->first()->image_path);
    }

    // High quality architectural fallback if no media uploaded yet
    if (!$imageUrl) {
        $fallbackSeed = ($property->id % 5) + 1;
        $fallbacks = [
            1 => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
            2 => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
            3 => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
            4 => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80',
            5 => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
        ];
        $imageUrl = $fallbacks[$fallbackSeed] ?? $fallbacks[1];
    }

    // 2. Status Badge Color (For Sale = Orange #FF6B35, For Rent = Black #1A1A1A)
    $statusName = $property->status?->name ?? 'For Sale';
    $isForRent = (bool) preg_match('/rent/i', $statusName);
    $statusBg = $isForRent 
        ? 'bg-[#1A1A1A] text-white border border-gray-700' 
        : 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/30';

    $formattedPrice = $property->formatted_price;
    $detailUrl = Route::has('property.show') 
        ? route('property.show', $property->id) 
        : url('/properties/' . $property->id);
@endphp

{{-- Wrapped entirely in an <a> tag linking to the property detail page --}}
<a 
    href="{{ $detailUrl }}"
    class="group block rounded-3xl overflow-hidden bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 text-gray-600 dark:text-gray-300 shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 focus:outline-none"
>
    {{-- Image Container with Hover Zoom Effect --}}
    <div class="relative h-64 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $property->title }}" 
            loading="lazy"
            class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500 ease-out"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

        {{-- Badges: Top Left (Status) & Top Right (Type) --}}
        <div class="absolute top-4 inset-x-4 flex items-center justify-between gap-2 pointer-events-none">
            
            {{-- Top Left: Status (For Sale = Orange, For Rent = Black) --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider {{ $statusBg }}">
                {{ $statusName }}
            </span>

            {{-- Top Right: Property Type --}}
            @if($property->type)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/95 dark:bg-black/80 backdrop-blur-md text-gray-900 dark:text-white border border-white/20 shadow-xs">
                    {{ $property->type->name }}
                </span>
            @endif
        </div>

        {{-- Bottom Right: Price Badge On Image --}}
        <div class="absolute bottom-3 right-3 px-3 py-1.5 rounded-xl bg-black/80 backdrop-blur-md text-white border border-white/10 shadow-lg flex items-baseline gap-1 pointer-events-none">
            <span class="text-sm font-extrabold tracking-tight text-white">{{ $formattedPrice }}</span>
            @if($property->price_label)
                <span class="text-[10px] font-medium text-gray-300">/ {{ $property->price_label }}</span>
            @endif
        </div>
    </div>

    {{-- Content: Title, Location & Specs Strip --}}
    <div class="p-6">
        {{-- Title --}}
        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white group-hover:text-[#FF6B35] dark:group-hover:text-[#FF6B35] transition-colors duration-200 line-clamp-1 mb-1.5">
            {{ $property->title }}
        </h3>

        {{-- Location (with icon) --}}
        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-5">
            <i class="fa-solid fa-location-dot text-[#FF6B35] text-xs shrink-0"></i>
            <span class="truncate">{{ $property->location?->name ?? 'Dubai, United Arab Emirates' }}</span>
        </div>

        {{-- Bottom Row: Beds, Baths, Sqft with Icons --}}
        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
            
            {{-- Beds --}}
            <div class="flex items-center gap-1.5" title="Bedrooms">
                <i class="fa-solid fa-bed text-[#FF6B35] text-xs"></i>
                <span><strong class="font-bold text-gray-900 dark:text-white">{{ $property->bedrooms ?? '-' }}</strong> Beds</span>
            </div>

            {{-- Baths --}}
            <div class="flex items-center gap-1.5" title="Bathrooms">
                <i class="fa-solid fa-bath text-[#FF6B35] text-xs"></i>
                <span><strong class="font-bold text-gray-900 dark:text-white">{{ $property->bathrooms ?? '-' }}</strong> Baths</span>
            </div>

            {{-- Sqft --}}
            <div class="flex items-center gap-1.5" title="Square Feet">
                <i class="fa-solid fa-vector-square text-[#FF6B35] text-xs"></i>
                <span><strong class="font-bold text-gray-900 dark:text-white">{{ $property->sqft ? number_format($property->sqft) : '-' }}</strong> Sqft</span>
            </div>

        </div>
    </div>
</a>

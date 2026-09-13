@props(['property'])

@php
    $currency = class_exists(\App\Models\SiteSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('site_settings')
        ? \App\Models\SiteSetting::getValue('currency_symbol', '$')
        : '$';

    $imageUrl = null;
    if ($property->primaryImage && $property->primaryImage->image_path) {
        $imageUrl = asset('storage/' . $property->primaryImage->image_path);
    } elseif ($property->relationLoaded('images') && $property->images->isNotEmpty()) {
        $imageUrl = asset('storage/' . $property->images->first()->image_path);
    } elseif (method_exists($property, 'getFirstMediaUrl') && $property->getFirstMediaUrl('properties')) {
        $imageUrl = $property->getFirstMediaUrl('properties');
    }

    // High quality architectural fallbacks
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

    $formattedPrice = number_format((float) $property->price, 0);
@endphp

<div class="group relative flex flex-col rounded-3xl overflow-hidden bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 shadow-xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    
    {{-- Image & Badges Container --}}
    <div class="relative h-64 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $property->title }}" 
            loading="lazy"
            class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

        {{-- Top Badges --}}
        <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 flex-wrap">
                @if($property->is_featured)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/30">
                        <i class="fa-solid fa-star text-[9px]"></i>
                        <span>Featured</span>
                    </span>
                @endif

                @if($property->status)
                    <span 
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white shadow-sm backdrop-blur-md"
                        style="background-color: {{ $property->status->color_code ?? '#10B981' }}cc;"
                    >
                        {{ $property->status->name }}
                    </span>
                @endif
            </div>

            @if($property->type)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-black/60 backdrop-blur-md text-white border border-white/10 uppercase tracking-wider">
                    {{ $property->type->name }}
                </span>
            @endif
        </div>

        {{-- Bottom Image Info: Price --}}
        <div class="absolute bottom-3.5 left-4 right-4 flex items-end justify-between text-white">
            <div>
                <span class="text-[10px] uppercase font-semibold text-gray-300 block tracking-wider">
                    Price
                </span>
                <span class="text-xl font-extrabold tracking-tight drop-shadow-sm text-white flex items-baseline gap-1">
                    <span class="text-[#FF6B35] font-bold text-base">{{ $currency }}</span>{{ $formattedPrice }}
                    @if($property->price_label)
                        <span class="text-[11px] font-normal text-gray-300">/ {{ $property->price_label }}</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            {{-- Location --}}
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <i class="fa-solid fa-location-dot text-[#FF6B35] text-[11px]"></i>
                <span class="truncate">{{ $property->location?->name ?? 'Prime Location' }}</span>
            </div>

            {{-- Title --}}
            <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-[#FF6B35] dark:group-hover:text-[#FF6B35] transition-colors duration-200 line-clamp-1">
                {{ $property->title }}
            </h3>

            {{-- Brief Description --}}
            @if($property->description)
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-1.5 leading-relaxed">
                    {{ strip_tags($property->description) }}
                </p>
            @endif
        </div>

        {{-- Specifications Strip --}}
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800/80">
            <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600 dark:text-gray-300">
                
                {{-- Bedrooms --}}
                <div class="flex items-center justify-center gap-1.5 bg-gray-50 dark:bg-[#222222] py-2 rounded-xl">
                    <i class="fa-solid fa-bed text-[#FF6B35] text-xs"></i>
                    <span class="font-semibold">{{ $property->bedrooms ?? '-' }}</span>
                    <span class="text-[10px] text-gray-400">Beds</span>
                </div>

                {{-- Bathrooms --}}
                <div class="flex items-center justify-center gap-1.5 bg-gray-50 dark:bg-[#222222] py-2 rounded-xl">
                    <i class="fa-solid fa-bath text-[#FF6B35] text-xs"></i>
                    <span class="font-semibold">{{ $property->bathrooms ?? '-' }}</span>
                    <span class="text-[10px] text-gray-400">Baths</span>
                </div>

                {{-- Sqft --}}
                <div class="flex items-center justify-center gap-1.5 bg-gray-50 dark:bg-[#222222] py-2 rounded-xl">
                    <i class="fa-solid fa-vector-square text-[#FF6B35] text-xs"></i>
                    <span class="font-semibold">{{ $property->sqft ? number_format($property->sqft) : '-' }}</span>
                    <span class="text-[10px] text-gray-400">SqFt</span>
                </div>

            </div>

            {{-- Card Footer Action --}}
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500">
                    ID: #{{ str_pad((string)$property->id, 5, '0', STR_PAD_LEFT) }}
                </span>

                <a 
                    href="{{ url('/#contact') }}" 
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[#FF6B35] group-hover:translate-x-0.5 transition-transform"
                >
                    <span>Inquire Now</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>

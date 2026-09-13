@php
    // 1. Resolve Gallery Images
    $galleryImages = collect();

    if ($property->relationLoaded('images') && $property->images->isNotEmpty()) {
        foreach ($property->images as $img) {
            $galleryImages->push(asset('storage/' . $img->image_path));
        }
    } elseif ($property->images()->exists()) {
        foreach ($property->images()->orderBy('sort_order')->get() as $img) {
            $galleryImages->push(asset('storage/' . $img->image_path));
        }
    }

    if ($galleryImages->isEmpty() && method_exists($property, 'getMedia')) {
        $media = $property->getMedia('properties');
        if ($media->isNotEmpty()) {
            foreach ($media as $item) {
                $galleryImages->push($item->getUrl());
            }
        }
    }

    if ($galleryImages->isEmpty()) {
        $galleryImages->push('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80');
        $galleryImages->push('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80');
        $galleryImages->push('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80');
    }

    $initialImage = $galleryImages->first();

    // 2. Status & Colors
    $statusName = $property->status?->name ?? 'For Sale';
    $isForRent = (bool) preg_match('/rent/i', $statusName);
    $statusBg = $isForRent ? 'bg-[#1A1A1A] text-white border border-gray-700' : 'bg-[#FF6B35] text-white';

    // 3. Agent Fallback
    $agent = $property->agent;
    $agentName = $agent?->name ?? 'Alexander Vance';
    $agentDesignation = $agent?->designation ?? 'Senior Partner & Luxury Advisory';
    $agentPhone = $agent?->phone ?? ($settings['phone'] ?? '+91 98765 43210');
    $agentEmail = $agent?->email ?? ($settings['email'] ?? 'concierge@tisharealty.com');
    $agentPhoto = $agent?->photo_path ? asset('storage/' . $agent->photo_path) : null;

    // 4. WhatsApp deeplink for the agent
    $waAgentNumber = formatWhatsAppNumber($agentPhone);
    $waMessage     = 'Hello, I am interested in the property: '
        . $property->title
        . ' (ID: ' . $property->hashid . '). Please share more details.';
    $waAgentUrl    = $waAgentNumber
        ? 'https://wa.me/' . $waAgentNumber . '?text=' . rawurlencode($waMessage)
        : null;
@endphp

@section('meta_title', $property->title . ' | TISHA Real Estate')
@section('meta_description', substr(strip_tags($property->description ?? 'Exclusive luxury property with TISHA Real Estate.'), 0, 155))
@section('og_image', $initialImage)
@section('og_type', 'article')

<x-layouts.customer 
    :title="$property->title . ' | TISHA Real Estate'"
    :metaDescription="substr(strip_tags($property->description ?? 'Exclusive luxury property with TISHA Real Estate.'), 0, 155)"
>
    <div 
        x-data="{ 
            showModal: false,
            activeImage: '{{ $initialImage }}',
            activeImageIndex: 0,
            gallery: {{ Js::from($galleryImages->values()->all()) }}
        }"
        @keydown.escape.window="showModal = false"
        class="py-10 bg-gray-50/60 dark:bg-[#0F0F0F] min-h-screen text-gray-800 dark:text-gray-200"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs Bar --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-6 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Home</a>
                <span>/</span>
                <a href="{{ $isForRent ? route('rentals') : route('sales') }}" class="hover:text-[#FF6B35] transition">
                    {{ $isForRent ? 'For Rent' : 'For Sale' }}
                </a>
                <span>/</span>
                @if($property->location)
                    <span class="text-gray-600 dark:text-gray-300">{{ $property->location->name }}</span>
                    <span>/</span>
                @endif
                <span class="text-[#FF6B35] font-semibold truncate max-w-xs">{{ $property->title }}</span>
            </nav>

            {{-- Header Row: Title, Location, Badges & Price --}}
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-8 border-b border-gray-200 dark:border-gray-800">
                <div>
                    {{-- Status and Type Badges --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $statusBg }}">
                            {{ $statusName }}
                        </span>

                        @if($property->type)
                            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white">
                                {{ $property->type->name }}
                            </span>
                        @endif

                        @if($property->is_featured)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-[#FF6B35]/20 text-[#FF6B35] border border-[#FF6B35]/30">
                                <i class="fa-solid fa-star text-[10px]"></i>
                                <span>Featured Residence</span>
                            </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-950 dark:text-white tracking-tight leading-tight">
                        {{ $property->title }}
                    </h1>

                    {{-- Location --}}
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-location-dot text-[#FF6B35]"></i>
                        <span>{{ $property->location?->name ?? 'Prime Location, Dubai, UAE' }}</span>
                    </div>
                </div>

                {{-- Price Block --}}
                <div class="lg:text-right">
                    <span class="text-xs uppercase font-bold tracking-wider text-gray-400 block">
                        Offering Price
                    </span>
                    <div class="text-3xl sm:text-4xl font-black text-[#FF6B35] flex items-baseline lg:justify-end gap-2 mt-1">
                        <span>{{ $property->formatted_price }}</span>
                        @if($property->price_label)
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">/ {{ $property->price_label }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- IMAGE GALLERY SECTION                                          --}}
            {{-- ============================================================== --}}
            <div class="mt-8">
                {{-- Main Feature Image Viewport --}}
                <div class="relative w-full h-[400px] sm:h-[550px] lg:h-[620px] rounded-3xl overflow-hidden bg-gray-950 shadow-2xl border border-gray-200 dark:border-gray-800">
                    <img 
                        :src="activeImage" 
                        alt="{{ $property->title }}" 
                        loading="lazy"
                        class="w-full h-full object-cover object-center transition-all duration-300"
                    >
                    
                    {{-- Image Counter Pill --}}
                    <div class="absolute bottom-4 right-4 px-3.5 py-1.5 rounded-xl bg-black/75 backdrop-blur-md text-white text-xs font-bold border border-white/10 flex items-center gap-2">
                        <i class="fa-regular fa-image text-[#FF6B35]"></i>
                        <span x-text="(activeImageIndex + 1) + ' / ' + gallery.length + ' Photos'"></span>
                    </div>

                    {{-- Action Button: Schedule Visit Overlay --}}
                    <div class="absolute bottom-4 left-4">
                        <button 
                            type="button" 
                            @click="showModal = true"
                            class="px-5 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-[#FF6B35]/40 transition flex items-center gap-2"
                        >
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Schedule a Visit</span>
                        </button>
                    </div>
                </div>

                {{-- Thumbnails Row --}}
                @if($galleryImages->count() > 1)
                    <div class="mt-4 flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                        @foreach($galleryImages as $idx => $imgUrl)
                            <button 
                                type="button"
                                @click="activeImage = '{{ $imgUrl }}'; activeImageIndex = {{ $idx }}"
                                class="relative w-24 sm:w-28 h-20 rounded-2xl overflow-hidden shrink-0 border-2 transition-all duration-200"
                                :class="activeImageIndex === {{ $idx }} ? 'border-[#FF6B35] ring-2 ring-[#FF6B35]/30' : 'border-transparent opacity-70 hover:opacity-100'"
                            >
                                <img src="{{ $imgUrl }}" alt="Thumbnail {{ $idx + 1 }}" loading="lazy" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============================================================== --}}
            {{-- MAIN DETAILS & SIDEBAR LAYOUT                                  --}}
            {{-- ============================================================== --}}
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                {{-- Left Content Area (Span 8) --}}
                <div class="lg:col-span-8 space-y-10">
                    
                    {{-- Key Specifications Bar --}}
                    <div class="p-6 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-sm">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#FF6B35] mb-5">
                            Key Specifications
                        </h2>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            {{-- Beds --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#262626] flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-bed"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 block font-medium">Bedrooms</span>
                                    <span class="text-lg font-black text-gray-900 dark:text-white">{{ $property->bedrooms ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Baths --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#262626] flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-bath"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 block font-medium">Bathrooms</span>
                                    <span class="text-lg font-black text-gray-900 dark:text-white">{{ $property->bathrooms ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Sqft --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#262626] flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-vector-square"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 block font-medium">Built-up Area</span>
                                    <span class="text-lg font-black text-gray-900 dark:text-white">
                                        {{ $property->sqft ? number_format($property->sqft) : '-' }} <span class="text-xs font-normal">SqFt</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Garage / Year --}}
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#262626] flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-warehouse"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 block font-medium">Garage / Year</span>
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ $property->garage ?? '0' }} Cars &bull; {{ $property->year_built ?? 'Modern' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description Section --}}
                    <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-sm">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-[#FF6B35] text-sm"></i>
                            <span>Property Overview & Details</span>
                        </h2>
                        
                        <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-300 leading-relaxed space-y-4">
                            @if($property->description)
                                {!! nl2br(e($property->description)) !!}
                            @else
                                <p>
                                    Welcome to this exceptional residential masterpiece situated within one of Dubai's most prestigious enclaves. Designed with extraordinary attention to architectural form, privacy, and refined finishes, this estate provides an incomparable living experience.
                                </p>
                                <p>
                                    Featuring floor-to-ceiling panoramic glass, sprawling outdoor entertainment terraces, and high-specification smart home integration, every facet of this residence has been tailored for effortless luxury.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Amenities Section --}}
                    @if($property->amenities->isNotEmpty())
                        <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-sm">
                            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                <i class="fa-solid fa-sparkles text-[#FF6B35] text-sm"></i>
                                <span>Features & Luxury Amenities</span>
                            </h2>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($property->amenities as $amenity)
                                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                                        <div class="w-8 h-8 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-xs shrink-0">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-gray-900 dark:text-white truncate block">
                                                {{ $amenity->name }}
                                            </span>
                                            @if($amenity->category)
                                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block">
                                                    {{ $amenity->category }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Right Sidebar (Span 4) --}}
                <div class="lg:col-span-4 space-y-8">
                    
                    {{-- Assigned Agent / Broker Card --}}
                    <div class="p-7 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-xl sticky top-28">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FF6B35] block mb-4">
                            Listing Advisory Team
                        </span>

                        {{-- Agent Avatar / Profile --}}
                        <div class="flex items-center gap-4 mb-6">
                            @if($agentPhoto)
                                <img 
                                    src="{{ $agentPhoto }}" 
                                    alt="{{ $agentName }}" 
                                    loading="lazy"
                                    class="w-16 h-16 rounded-2xl object-cover border-2 border-[#FF6B35]/40 shadow-md"
                                >
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#1A1A1A] to-[#333333] border border-[#FF6B35]/40 flex items-center justify-center text-white text-lg font-black shadow-md">
                                    {{ substr($agentName, 0, 2) }}
                                </div>
                            @endif

                            <div>
                                <h3 class="text-base font-bold text-gray-950 dark:text-white leading-snug">
                                    {{ $agentName }}
                                </h3>
                                <p class="text-xs text-[#FF6B35] font-semibold mt-0.5">
                                    {{ $agentDesignation }}
                                </p>
                                <span class="text-[10px] text-gray-400 block mt-0.5">
                                    TISHA Certified Specialist
                                </span>
                            </div>
                        </div>

                        {{-- Direct Contact Links --}}
                        <div class="space-y-2.5 mb-6 text-xs">
                            <a 
                                href="tel:{{ preg_replace('/[^0-9+]/', '', $agentPhone) }}" 
                                class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800 hover:border-[#FF6B35]/40 transition text-gray-700 dark:text-gray-300 font-semibold"
                            >
                                <i class="fa-solid fa-phone text-[#FF6B35] text-xs shrink-0"></i>
                                <span>{{ $agentPhone }}</span>
                            </a>

                            <a 
                                href="mailto:{{ $agentEmail }}" 
                                class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800 hover:border-[#FF6B35]/40 transition text-gray-700 dark:text-gray-300 font-semibold truncate"
                            >
                                <i class="fa-solid fa-envelope text-[#FF6B35] text-xs shrink-0"></i>
                                <span class="truncate">{{ $agentEmail }}</span>
                            </a>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-3">
                            {{-- Schedule Visit Button --}}
                            <button 
                                type="button" 
                                @click="showModal = true"
                                class="w-full py-3.5 rounded-2xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-[#FF6B35]/30 flex items-center justify-center gap-2 active:scale-95"
                            >
                                <i class="fa-regular fa-calendar-check text-sm"></i>
                                <span>Schedule a Visit</span>
                            </button>

                            {{-- Chat on WhatsApp Button --}}
                            @if($waAgentUrl)
                                <a
                                    href="{{ $waAgentUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-full py-3.5 rounded-2xl bg-green-500 hover:bg-green-600 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-green-500/30 flex items-center justify-center gap-2"
                                    aria-label="Chat with agent on WhatsApp"
                                >
                                    <i class="fab fa-whatsapp text-base"></i>
                                    <span>Chat on WhatsApp</span>
                                </a>
                            @else
                                <a
                                    href="mailto:{{ $agentEmail }}"
                                    class="w-full py-3.5 rounded-2xl bg-gray-100 dark:bg-[#262626] hover:bg-gray-200 dark:hover:bg-[#333333] text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2"
                                >
                                    <i class="fa-regular fa-envelope text-sm text-[#FF6B35]"></i>
                                    <span>Contact Agent</span>
                                </a>
                            @endif
                        </div>

                        {{-- Security badge --}}
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-[11px] text-center text-gray-400 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-[#FF6B35]"></i>
                            <span>Verified & Compliant Listing #{{ $property->id }}</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- ============================================================== --}}
        {{-- ALPINE.JS SCHEDULE VISIT MODAL                                 --}}
        {{-- ============================================================== --}}
        <div 
            x-show="showModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-md flex items-center justify-center p-4"
        >
            <div 
                @click.away="showModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="w-full max-w-lg bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-3xl shadow-2xl overflow-hidden"
            >
                {{-- Livewire Schedule Visit Form --}}
                <livewire:customer.schedule-visit-form :property-id="$property->id" />
            </div>
        </div>

    </div>
</x-layouts.customer>

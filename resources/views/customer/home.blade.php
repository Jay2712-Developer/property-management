@php
    $types = \App\Services\CacheService::getPropertyTypes();
    $statuses = \App\Services\CacheService::getPropertyStatuses();
    $locations = \App\Services\CacheService::getLocations()->take(10);

    $hasProperties = class_exists(\App\Models\Property::class) && \Illuminate\Support\Facades\Schema::hasTable('properties');
    $totalProperties = $hasProperties 
        ? \App\Models\Property::where('is_active', true)->count() 
        : 140;

    $totalLocations = $hasLocations 
        ? \App\Models\Location::where('is_active', true)->count() 
        : 18;

    $hasAgents = class_exists(\App\Models\Agent::class) && \Illuminate\Support\Facades\Schema::hasTable('agents');
    $totalAgents = $hasAgents 
        ? \App\Models\Agent::where('is_active', true)->count() 
        : 24;

    $hasTestimonials = class_exists(\App\Models\Testimonial::class) && \Illuminate\Support\Facades\Schema::hasTable('testimonials');
    $testimonials = $hasTestimonials 
        ? \App\Models\Testimonial::where('is_active', true)->ordered()->take(3)->get() 
        : collect();
@endphp

<x-layouts.customer 
    title="TISHA Real Estate - Luxury Homes, Penthouses & Waterfront Estates"
    metaDescription="Discover exclusive luxury properties, modern penthouses, and waterfront estates with TISHA Real Estate in Dubai."
>

    {{-- ====================================================================== --}}
    {{-- 1. HERO SECTION                                                        --}}
    {{-- ====================================================================== --}}
    <section class="relative min-h-[90vh] flex items-center justify-center pt-24 pb-20 overflow-hidden bg-gradient-to-b from-gray-100 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414]">
        
        {{-- Background Glow & Ambience --}}
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-tr from-[#FF6B35]/20 to-[#FF8C61]/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full text-center">
            
            {{-- Top Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-[#1A1A1A]/80 backdrop-blur-md text-xs font-semibold text-gray-700 dark:text-gray-300 shadow-xs mb-8">
                <span class="w-2 h-2 rounded-full bg-[#FF6B35] animate-pulse"></span>
                <span>The Premier Brokerage for Luxury Properties</span>
            </div>

            {{-- Main Headline --}}
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-gray-950 dark:text-white max-w-5xl mx-auto leading-[1.1]">
                Curated Luxury Living for the 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] via-[#FF8C61] to-[#E55A2B]">
                    Exceptional
                </span>
            </h1>

            {{-- Subheading --}}
            <p class="mt-6 text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Explore an elite portfolio of bespoke villas, sky penthouses, and private coastal estates tailored for discerning buyers and private investors.
            </p>

            {{-- Functional Search Filter Bar --}}
            <div class="mt-10 max-w-5xl mx-auto">
                <form 
                    action="{{ url('/properties') }}" 
                    method="GET"
                    class="p-3 sm:p-4 rounded-3xl bg-white/95 dark:bg-[#1A1A1A]/95 backdrop-blur-xl border border-gray-200/90 dark:border-gray-800 shadow-2xl text-left"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        
                        {{-- Keyword / Location Input --}}
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                            <label for="search-location" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                                Location / Keyword
                            </label>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-[#FF6B35] text-xs"></i>
                                <input 
                                    type="text" 
                                    id="search-location"
                                    name="keyword" 
                                    placeholder="Dubai Marina, Downtown..." 
                                    class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                                >
                            </div>
                        </div>

                        {{-- Property Type Dropdown --}}
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                            <label for="search-type" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                                Property Type
                            </label>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-hotel text-[#FF6B35] text-xs"></i>
                                <select 
                                    id="search-type"
                                    name="type" 
                                    class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white focus:outline-none cursor-pointer"
                                >
                                    <option value="" class="dark:bg-[#1A1A1A]">All Property Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->slug }}" class="dark:bg-[#1A1A1A]">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Property Status Dropdown --}}
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#262626] border border-gray-100 dark:border-gray-800/80">
                            <label for="search-status" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                                Acquisition
                            </label>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-tag text-[#FF6B35] text-xs"></i>
                                <select 
                                    id="search-status"
                                    name="status" 
                                    class="w-full bg-transparent text-xs font-semibold text-gray-900 dark:text-white focus:outline-none cursor-pointer"
                                >
                                    <option value="" class="dark:bg-[#1A1A1A]">For Sale & Rent</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->slug ?? strtolower(str_replace(' ', '-', $status->name)) }}" class="dark:bg-[#1A1A1A]">
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center">
                            <button 
                                type="submit" 
                                class="w-full h-full min-h-[52px] rounded-2xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-[#FF6B35]/30 hover:shadow-[#FF6B35]/40 transition flex items-center justify-center gap-2 active:scale-95"
                            >
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Search Properties</span>
                            </button>
                        </div>

                    </div>
                </form>

                {{-- Popular Quick Search Pills --}}
                <div class="mt-4 flex items-center justify-center gap-2 flex-wrap text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-400">Trending Areas:</span>
                    <a href="{{ url('/properties?keyword=Palm+Jumeirah') }}" class="px-2.5 py-1 rounded-lg bg-white/60 dark:bg-[#1A1A1A]/60 border border-gray-200 dark:border-gray-800 hover:text-[#FF6B35] transition">Palm Jumeirah</a>
                    <a href="{{ url('/properties?keyword=Downtown+Dubai') }}" class="px-2.5 py-1 rounded-lg bg-white/60 dark:bg-[#1A1A1A]/60 border border-gray-200 dark:border-gray-800 hover:text-[#FF6B35] transition">Downtown Dubai</a>
                    <a href="{{ url('/properties?keyword=Dubai+Marina') }}" class="px-2.5 py-1 rounded-lg bg-white/60 dark:bg-[#1A1A1A]/60 border border-gray-200 dark:border-gray-800 hover:text-[#FF6B35] transition">Dubai Marina</a>
                    <a href="{{ url('/properties?keyword=Emirates+Hills') }}" class="px-2.5 py-1 rounded-lg bg-white/60 dark:bg-[#1A1A1A]/60 border border-gray-200 dark:border-gray-800 hover:text-[#FF6B35] transition">Emirates Hills</a>
                </div>
            </div>

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 2. STATS SECTION                                                       --}}
    {{-- ====================================================================== --}}
    <section class="py-12 border-y border-gray-200/80 dark:border-gray-800 bg-white dark:bg-[#141414]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 text-center">
                
                {{-- Stat 1 --}}
                <div class="p-6 rounded-3xl bg-gray-50/70 dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800/80">
                    <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] to-[#FF8C61] block">
                        {{ $totalProperties > 0 ? $totalProperties : 120 }}+
                    </span>
                    <span class="text-xs uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 mt-1 block">
                        Exclusive Properties
                    </span>
                </div>

                {{-- Stat 2 --}}
                <div class="p-6 rounded-3xl bg-gray-50/70 dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800/80">
                    <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] to-[#FF8C61] block">
                        $2.4B+
                    </span>
                    <span class="text-xs uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 mt-1 block">
                        Portfolio Transacted
                    </span>
                </div>

                {{-- Stat 3 --}}
                <div class="p-6 rounded-3xl bg-gray-50/70 dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800/80">
                    <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] to-[#FF8C61] block">
                        {{ $totalLocations > 0 ? $totalLocations : 15 }}+
                    </span>
                    <span class="text-xs uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 mt-1 block">
                        Prime Districts
                    </span>
                </div>

                {{-- Stat 4 --}}
                <div class="p-6 rounded-3xl bg-gray-50/70 dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800/80">
                    <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] to-[#FF8C61] block">
                        99.4%
                    </span>
                    <span class="text-xs uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 mt-1 block">
                        Client Satisfaction
                    </span>
                </div>

            </div>
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 3. SERVICES SECTION (Buy, Rent, Sell)                                  --}}
    {{-- ====================================================================== --}}
    <section id="services" class="py-20 bg-gray-50/50 dark:bg-[#0F0F0F]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF6B35]">
                    Comprehensive Real Estate
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-950 dark:text-white mt-2">
                    Distinguished Advisory Services
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 leading-relaxed">
                    Whether acquiring your next residence, leasing a signature penthouse, or optimizing an investment portfolio, our senior consultants deliver bespoke guidance.
                </p>
            </div>

            {{-- 3 Column Services Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Service 1: Buy --}}
                <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 shadow-xs hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 dark:text-white mb-2">
                        Buy Luxury Homes
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        Access discreet off-market mansions, waterfront architectural estates, and penthouses with complete transactional representation.
                    </p>
                    <a href="{{ url('/properties?status=for-sale') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#FF6B35] hover:gap-3 transition-all">
                        <span>Explore Homes For Sale</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                {{-- Service 2: Rent --}}
                <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 shadow-xs hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 dark:text-white mb-2">
                        Rent Signature Properties
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        Curated long-term luxury residences, fully serviced turnkey villas, and sky suites across the most desirable neighborhoods.
                    </p>
                    <a href="{{ url('/properties?status=for-rent') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#FF6B35] hover:gap-3 transition-all">
                        <span>View Premier Rentals</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                {{-- Service 3: Sell --}}
                <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 shadow-xs hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 dark:text-white mb-2">
                        Sell & Asset Marketing
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        Global high-net-worth marketing campaigns, professional cinematic property media, and strategic pricing to maximize seller equity.
                    </p>
                    <a href="{{ url('/#contact') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#FF6B35] hover:gap-3 transition-all">
                        <span>Request Property Appraisal</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

            </div>

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 4. FEATURED PROPERTIES SECTION (Livewire Component)                    --}}
    {{-- ====================================================================== --}}
    <section id="properties" class="py-20 bg-white dark:bg-[#141414] border-t border-gray-200/80 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF6B35]">
                        Curated Collection
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-950 dark:text-white mt-1">
                        Featured Properties
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Exclusive residential gems verified for immediate acquisition.
                    </p>
                </div>

                <a 
                    href="{{ url('/properties') }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:border-[#FF6B35] dark:hover:border-[#FF6B35] hover:text-[#FF6B35] transition self-start sm:self-auto"
                >
                    <span>View All Listings</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            {{-- Livewire Featured Properties Grid Component --}}
            <livewire:customer.featured-properties />

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 5. TESTIMONIALS SECTION                                                --}}
    {{-- ====================================================================== --}}
    <section class="py-20 bg-gray-50/60 dark:bg-[#0F0F0F] border-t border-gray-200/80 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF6B35]">
                    Client Perspectives
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-950 dark:text-white mt-2">
                    Trusted by Global Investors
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Read the experiences of homeowners and investors who partnered with TISHA Real Estate.
                </p>
            </div>

            {{-- Testimonial Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                    <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col justify-between">
                        <div>
                            {{-- Star Rating --}}
                            <div class="flex items-center gap-1 text-[#FF6B35] text-xs mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= ($testimonial->rating ?? 5) ? 'text-[#FF6B35]' : 'text-gray-300 dark:text-gray-700' }}"></i>
                                @endfor
                            </div>

                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic mb-6">
                                &ldquo;{{ $testimonial->message }}&rdquo;
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            @if($testimonial->client_photo)
                                <img 
                                    src="{{ asset('storage/' . $testimonial->client_photo) }}" 
                                    alt="{{ $testimonial->client_name }}" 
                                    class="w-10 h-10 rounded-full object-cover border border-[#FF6B35]/40"
                                >
                            @else
                                <div class="w-10 h-10 rounded-full bg-[#FF6B35]/10 text-[#FF6B35] font-bold text-xs flex items-center justify-center">
                                    {{ substr($testimonial->client_name, 0, 2) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="text-xs font-bold text-gray-950 dark:text-white">
                                    {{ $testimonial->client_name }}
                                </h4>
                                <span class="text-[10px] text-gray-400">Verified Client</span>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Default Curated Testimonials if none in DB --}}
                    <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1 text-[#FF6B35] text-xs mb-4">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic mb-6">
                                &ldquo;TISHA Real Estate navigated our Palm Jumeirah villa acquisition with absolute discretion and market mastery. The premier service is unmatched.&rdquo;
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-full bg-[#FF6B35]/10 text-[#FF6B35] font-bold text-xs flex items-center justify-center">
                                SA
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-950 dark:text-white">Sheikh Al-Maktoum Partner</h4>
                                <span class="text-[10px] text-gray-400">Palm Jumeirah Buyer</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1 text-[#FF6B35] text-xs mb-4">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic mb-6">
                                &ldquo;From private helicopter tours to final contract closing on our Downtown penthouse, TISHA exceeded our family's expectations.&rdquo;
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-full bg-[#FF6B35]/10 text-[#FF6B35] font-bold text-xs flex items-center justify-center">
                                HR
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-950 dark:text-white">Helena Rostova</h4>
                                <span class="text-[10px] text-gray-400">International Investor</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1 text-[#FF6B35] text-xs mb-4">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic mb-6">
                                &ldquo;Their private asset marketing delivered a record price on our Emirates Hills mansion within 28 days. Extraordinary professionalism.&rdquo;
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-full bg-[#FF6B35]/10 text-[#FF6B35] font-bold text-xs flex items-center justify-center">
                                DW
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-950 dark:text-white">David West</h4>
                                <span class="text-[10px] text-gray-400">Estate Seller</span>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 6. CTA SECTION                                                         --}}
    {{-- ====================================================================== --}}
    <section id="contact" class="relative py-20 overflow-hidden bg-gradient-to-tr from-[#1A1A1A] via-[#141414] to-black text-white border-t border-gray-800">
        
        {{-- Orange Ambience Glow --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#FF6B35]/20 blur-[120px] pointer-events-none"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[#FF6B35]/20 text-[#FF6B35] border border-[#FF6B35]/30 mb-6">
                <i class="fa-solid fa-gem text-[10px]"></i>
                <span>Private Advisory</span>
            </span>

            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                Ready to Acquire or List Your 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF6B35] to-[#FF8C61]">
                    Next Masterpiece?
                </span>
            </h2>

            <p class="mt-4 text-sm sm:text-base text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Connect with our senior partners for confidential private portfolio consultations, off-market briefings, and immediate property scheduling.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a 
                    href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['phone'] ?? '+97141234567') }}" 
                    class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-extrabold uppercase tracking-wider shadow-lg shadow-[#FF6B35]/30 hover:shadow-[#FF6B35]/50 transition transform active:scale-95 flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-phone"></i>
                    <span>Speak with Concierge</span>
                </a>

                <a 
                    href="{{ url('/properties') }}" 
                    class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-[#262626] hover:bg-[#333333] border border-gray-700 text-white text-xs font-extrabold uppercase tracking-wider transition flex items-center justify-center gap-2"
                >
                    <i class="fa-regular fa-compass"></i>
                    <span>Browse All Residences</span>
                </a>
            </div>

        </div>
    </section>

</x-layouts.customer>

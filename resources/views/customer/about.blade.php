@php
    // Fetch about page content safely from pages table
    $hasPages = class_exists(\App\Models\Page::class) && \Illuminate\Support\Facades\Schema::hasTable('pages');
    $page = $hasPages 
        ? \App\Models\Page::active()
            ->where(function ($query) {
                $query->where('slug', 'about')
                      ->orWhere('slug', 'about-us');
            })
            ->first()
        : null;

    // Fetch active real estate agents
    $hasAgents = class_exists(\App\Models\Agent::class) && \Illuminate\Support\Facades\Schema::hasTable('agents');
    $agents = $hasAgents 
        ? \App\Models\Agent::active()->get() 
        : collect();

    $baseTitle = $page?->meta_title ?: ($page?->title ?: 'About Us');
    $pageTitle = str_contains($baseTitle, 'TISHA') ? $baseTitle : $baseTitle . ' | TISHA Real Estate';
    $metaDescription = $page?->meta_description ?? 'Learn about TISHA Real Estate, our legacy of luxury brokerage, client discretion, and our premier property specialists.';
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', $metaDescription)

<x-layouts.customer 
    :title="$pageTitle"
    :metaDescription="$metaDescription"
>
    {{-- ====================================================================== --}}
    {{-- 1. HERO HEADER WITH BREADCRUMB                                         --}}
    {{-- ====================================================================== --}}
    <section class="relative py-16 sm:py-24 overflow-hidden bg-gradient-to-b from-gray-100 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414] border-b border-gray-200 dark:border-gray-800/80">
        
        {{-- Ambient orange glow --}}
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[650px] h-[320px] bg-gradient-to-tr from-[#FF6B35]/20 to-orange-400/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- Breadcrumb --}}
            <nav class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-[#1A1A1A]/80 backdrop-blur-md text-xs font-semibold text-gray-500 dark:text-gray-400 shadow-xs mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Home</a>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-900 dark:text-white font-bold">About Us</span>
            </nav>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-950 dark:text-white max-w-4xl mx-auto leading-[1.15]">
                {{ $page?->title ?? 'About TISHA Real Estate' }}
            </h1>

            <p class="mt-4 text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                A legacy of uncompromising architectural discernment, discreet private brokerage, and exceptional client outcomes.
            </p>

            {{-- Stat counter row --}}
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="p-5 rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 shadow-sm">
                    <span class="block text-2xl sm:text-3xl font-black text-[#FF6B35]">$1.8B+</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mt-1 block">Career Transactions</span>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 shadow-sm">
                    <span class="block text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">12+</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mt-1 block">Years of Mastery</span>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 shadow-sm">
                    <span class="block text-2xl sm:text-3xl font-black text-[#FF6B35]">98.7%</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mt-1 block">Client Satisfaction</span>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 shadow-sm">
                    <span class="block text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">200+</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mt-1 block">Prime Properties</span>
                </div>
            </div>

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 2. ABOUT CONTENT & PHILOSOPHY                                          --}}
    {{-- ====================================================================== --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-[#0F0F0F]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                {{-- Left: Dynamic Content from Database (Span 7) --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-[#FF6B35]">
                        <span class="w-2 h-0.5 bg-[#FF6B35]"></span>
                        <span>Our Heritage & Vision</span>
                    </div>

                    {{-- Dynamic HTML Content from Pages table --}}
                    @if($page && !empty($page->content))
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed [&>h2]:text-2xl [&>h2]:font-black [&>h2]:text-gray-900 dark:[&>h2]:text-white [&>h2]:mt-6 [&>h2]:mb-3 [&>p]:text-sm sm:[&>p]:text-base [&>p]:leading-relaxed [&>p]:mb-4 [&>ul]:list-disc [&>ul]:pl-5 [&>ul>li]:mb-2">
                            {!! $page->content !!}
                        </div>
                    @else
                        {{-- Polished Fallback when CMS page is not seeded --}}
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-4">
                                Redefining Luxury Real Estate with Unrivaled Passion
                            </h2>
                            <p class="text-sm sm:text-base leading-relaxed text-gray-600 dark:text-gray-400 mb-4">
                                Founded on principles of absolute transparency, discreet representation, and an eye for exceptional architecture, <strong>TISHA Real Estate</strong> is Dubai’s vanguard brokerage for iconic waterfront estates, modern penthouses, and bespoke private retreats.
                            </p>
                            <p class="text-sm sm:text-base leading-relaxed text-gray-600 dark:text-gray-400 mb-4">
                                Our elite partners orchestrate high-value acquisitions with an analytical edge and global marketing reach. We do not simply transact residences; we curate legacy assets for visionary buyers and family offices worldwide.
                            </p>
                        </div>
                    @endif

                    {{-- Core Pillars Grid --}}
                    <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-shield-halved text-lg"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Confidential Advisory</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Complete non-disclosure protocol protecting high-profile clientele and family offices.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center mb-3">
                                <i class="fa-solid fa-chart-line text-lg"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Market Forefront</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Proprietary data, off-market inventory access, and rigorous predictive valuation models.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right: Visual Feature Card & Image (Span 5) --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="relative rounded-3xl overflow-hidden border border-gray-200/80 dark:border-gray-800 shadow-2xl group">
                        <img 
                            src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80" 
                            alt="TISHA Luxury Property Architecture" 
                            loading="lazy"
                            class="w-full h-[460px] object-cover group-hover:scale-105 transition-transform duration-700"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent flex flex-col justify-end p-8 text-white">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FF6B35] text-[11px] font-black uppercase tracking-wider w-max mb-3">
                                <i class="fa-solid fa-certificate text-[10px]"></i>
                                Distinction in Architecture
                            </span>
                            <h3 class="text-xl font-bold leading-snug">
                                "Luxury is not merely a price point; it is a standard of craft and service."
                            </h3>
                            <p class="text-xs text-gray-300 mt-2">
                                — The Executive Council, TISHA Real Estate
                            </p>
                        </div>
                    </div>

                    {{-- Quick Consultation Banner --}}
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-[#1A1A1A] to-[#262626] border border-[#FF6B35]/30 text-white flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-bold">Have a unique property in mind?</h4>
                            <p class="text-xs text-gray-400 mt-0.5">Schedule a private portfolio consultation today.</p>
                        </div>
                        <a 
                            href="{{ route('contact') }}" 
                            class="px-5 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider shrink-0 transition shadow-md shadow-[#FF6B35]/30"
                        >
                            Connect
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 3. MEET THE EXPERTS SECTION                                            --}}
    {{-- ====================================================================== --}}
    <section class="py-16 sm:py-24 bg-gray-50 dark:bg-[#141414] border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] text-xs font-bold uppercase tracking-widest text-[#FF6B35] mb-4">
                    <i class="fa-solid fa-users text-[11px]"></i>
                    <span>Meet The Experts</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-gray-950 dark:text-white">
                    Our Senior Advisory Partners
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-500 dark:text-gray-400">
                    Trusted brokers with unmatched regional mastery, high-net-worth discretion, and proven negotiating acumen.
                </p>
            </div>

            @if($agents->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($agents as $agent)
                        @php
                            $agentPhoto = $agent->getFirstMediaUrl('avatar') ?: ($agent->photo_path ? asset('storage/' . $agent->photo_path) : null);
                            $social = is_array($agent->social_links) ? $agent->social_links : [];
                        @endphp
                        <div class="group rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 p-6 shadow-sm hover:shadow-xl hover:border-[#FF6B35]/50 transition duration-300 flex flex-col justify-between">
                            
                            <div>
                                {{-- Agent Image / Avatar --}}
                                <div class="relative w-full aspect-square rounded-2xl overflow-hidden mb-5 bg-gray-100 dark:bg-[#262626]">
                                    @if($agentPhoto)
                                        <img 
                                            src="{{ $agentPhoto }}" 
                                            alt="{{ $agent->name }}" 
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#1A1A1A] to-[#2B2B2B] text-white">
                                            <span class="text-3xl font-black text-[#FF6B35]">{{ substr($agent->name, 0, 2) }}</span>
                                            <span class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">TISHA Specialist</span>
                                        </div>
                                    @endif

                                    @if($agent->experience_years)
                                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/75 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider border border-white/10">
                                            {{ $agent->experience_years }}+ Yrs Exp
                                        </span>
                                    @endif
                                </div>

                                {{-- Name & Designation --}}
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#FF6B35] transition">
                                    {{ $agent->name }}
                                </h3>
                                
                                <p class="text-xs text-[#FF6B35] font-semibold mt-0.5">
                                    {{ $agent->designation ?? 'Senior Real Estate Advisor' }}
                                </p>

                                @if($agent->bio)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 line-clamp-3 leading-relaxed">
                                        {{ $agent->bio }}
                                    </p>
                                @endif
                            </div>

                            {{-- Contact Links & Actions --}}
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800/80 space-y-2.5">
                                @if($agent->phone)
                                    <a 
                                        href="tel:{{ preg_replace('/[^0-9+]/', '', $agent->phone) }}" 
                                        class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition truncate"
                                    >
                                        <i class="fa-solid fa-phone text-[#FF6B35] text-xs shrink-0"></i>
                                        <span class="truncate">{{ $agent->phone }}</span>
                                    </a>
                                @endif

                                @if($agent->email)
                                    <a 
                                        href="mailto:{{ $agent->email }}" 
                                        class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition truncate"
                                    >
                                        <i class="fa-solid fa-envelope text-[#FF6B35] text-xs shrink-0"></i>
                                        <span class="truncate">{{ $agent->email }}</span>
                                    </a>
                                @endif

                                <div class="pt-2">
                                    <a 
                                        href="{{ route('contact') }}?agent={{ urlencode($agent->name) }}" 
                                        class="w-full py-2.5 rounded-xl bg-gray-100 dark:bg-[#262626] hover:bg-[#FF6B35] hover:text-white text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider text-center block transition duration-200"
                                    >
                                        Inquire With {{ Str::words($agent->name, 1, '') }}
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- Fallback Card Showcase if database agents table is empty --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    
                    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 p-6 shadow-sm">
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden mb-5">
                            <img 
                                src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80" 
                                alt="Alexander Vance" 
                                loading="lazy"
                                class="w-full h-full object-cover"
                            >
                            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/75 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider">
                                14+ Yrs Exp
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Alexander Vance</h3>
                        <p class="text-xs text-[#FF6B35] font-semibold mt-0.5">Managing Partner - Waterfront Villas</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Specializing in super-prime beachfront estates across Palm Jumeirah and Emirates Hills.</p>
                        <a href="{{ route('contact') }}" class="mt-4 w-full py-2.5 rounded-xl bg-gray-100 dark:bg-[#262626] hover:bg-[#FF6B35] hover:text-white text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider text-center block transition">
                            Schedule Consultation
                        </a>
                    </div>

                    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 p-6 shadow-sm">
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden mb-5">
                            <img 
                                src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80" 
                                alt="Elena Rostova" 
                                loading="lazy"
                                class="w-full h-full object-cover"
                            >
                            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/75 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider">
                                10+ Yrs Exp
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Elena Rostova</h3>
                        <p class="text-xs text-[#FF6B35] font-semibold mt-0.5">Director - Urban Penthouses</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Leading luxury penthouse transactions and high-yield portfolio advisory for international investors.</p>
                        <a href="{{ route('contact') }}" class="mt-4 w-full py-2.5 rounded-xl bg-gray-100 dark:bg-[#262626] hover:bg-[#FF6B35] hover:text-white text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider text-center block transition">
                            Schedule Consultation
                        </a>
                    </div>

                    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 p-6 shadow-sm">
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden mb-5">
                            <img 
                                src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80" 
                                alt="Tariq Mansoor" 
                                loading="lazy"
                                class="w-full h-full object-cover"
                            >
                            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/75 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider">
                                8+ Yrs Exp
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Tariq Mansoor</h3>
                        <p class="text-xs text-[#FF6B35] font-semibold mt-0.5">Senior Commercial & Land Specialist</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Advising private equity and developers on flagship developments and sovereign assets.</p>
                        <a href="{{ route('contact') }}" class="mt-4 w-full py-2.5 rounded-xl bg-gray-100 dark:bg-[#262626] hover:bg-[#FF6B35] hover:text-white text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider text-center block transition">
                            Schedule Consultation
                        </a>
                    </div>

                </div>
            @endif

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 4. BOTTOM BANNER CALL-TO-ACTION                                        --}}
    {{-- ====================================================================== --}}
    <section class="py-16 bg-white dark:bg-[#0F0F0F] border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
                Begin Your Journey With TISHA
            </h3>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                Whether you seek an extraordinary home or wish to list a masterwork, our advisory team awaits your instruction.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a 
                    href="{{ route('contact') }}" 
                    class="px-8 py-3.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-[#FF6B35]/30 transition transform active:scale-95"
                >
                    Contact Advisory Team
                </a>
                <a 
                    href="{{ route('sales') }}" 
                    class="px-8 py-3.5 rounded-xl bg-gray-100 dark:bg-[#1A1A1A] hover:bg-gray-200 dark:hover:bg-[#262626] text-gray-900 dark:text-white text-xs font-bold uppercase tracking-wider border border-gray-200 dark:border-gray-800 transition"
                >
                    Explore Properties
                </a>
            </div>
        </div>
    </section>

</x-layouts.customer>

@php
    $phone = $settings['phone'] ?? $settings['contact_phone'] ?? '+971 4 123 4567';
    $email = $settings['email'] ?? $settings['contact_email'] ?? 'info@tisharealty.com';
    $address = $settings['address'] ?? $settings['contact_address'] ?? 'Suite 1402, Marina Plaza, Dubai Marina, Dubai, UAE';
    $map = $settings['map'] ?? $settings['contact_map_iframe'] ?? $settings['google_map_embed'] ?? null;
    $siteName = $settings['site_name'] ?? 'TISHA Real Estate';
@endphp

@section('meta_title', 'Contact Our Concierge & Advisory Team | TISHA Real Estate')
@section('meta_description', 'Get in touch with TISHA Real Estate for private luxury home acquisitions, villa listings, and confidential property advisory in Dubai.')

<x-layouts.customer 
    title="Contact Our Concierge & Advisory Team | TISHA Real Estate"
    metaDescription="Get in touch with TISHA Real Estate for private luxury home acquisitions, villa listings, and confidential property advisory in Dubai."
>
    {{-- ====================================================================== --}}
    {{-- 1. HERO HEADER                                                         --}}
    {{-- ====================================================================== --}}
    <section class="relative py-16 sm:py-20 overflow-hidden bg-gradient-to-b from-gray-100 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414] border-b border-gray-200 dark:border-gray-800/80">
        
        {{-- Orange ambient glow --}}
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gradient-to-tr from-[#FF6B35]/20 to-orange-400/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- Breadcrumb --}}
            <nav class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-[#1A1A1A]/80 backdrop-blur-md text-xs font-semibold text-gray-500 dark:text-gray-400 shadow-xs mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Home</a>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-900 dark:text-white font-bold">Contact</span>
            </nav>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-950 dark:text-white max-w-4xl mx-auto leading-[1.15]">
                Let's Start a Conversation
            </h1>

            <p class="mt-4 text-base sm:text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                Connect with our senior partners for private property viewings, asset acquisitions, and market consultations.
            </p>

        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 2. CONTACT INFO CARDS & LIVEWIRE FORM                                  --}}
    {{-- ====================================================================== --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-[#0F0F0F]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                
                {{-- Left: Contact Details & Office Channels (Span 5) --}}
                <div class="lg:col-span-5 space-y-6">
                    
                    <div>
                        <span class="text-xs font-black uppercase tracking-widest text-[#FF6B35] block mb-2">
                            Direct Concierge
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-gray-950 dark:text-white leading-tight">
                            We Are At Your Full Service
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                            Whether seeking confidential portfolio advice or inquiring on a specific residence, our advisory team ensures immediate discretion.
                        </p>
                    </div>

                    {{-- Contact Channels Cards --}}
                    <div class="space-y-4 pt-2">
                        
                        {{-- Office Address --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center shrink-0 text-lg">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Headquarters</h3>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1 leading-snug">
                                    {{ $address }}
                                </p>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block">Client Parking & Valet Available</span>
                            </div>
                        </div>

                        {{-- Phone Support --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center shrink-0 text-lg">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Telephone Line</h3>
                                <a 
                                    href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" 
                                    class="text-sm font-bold text-gray-900 dark:text-white mt-1 block hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition"
                                >
                                    {{ $phone }}
                                </a>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block">Mon - Sat: 9:00 AM - 8:00 PM GST</span>
                            </div>
                        </div>

                        {{-- Email Support --}}
                        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200/80 dark:border-gray-800 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center shrink-0 text-lg">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Electronic Mail</h3>
                                <a 
                                    href="mailto:{{ $email }}" 
                                    class="text-sm font-bold text-gray-900 dark:text-white mt-1 block hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition truncate"
                                >
                                    {{ $email }}
                                </a>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block">Confidential response within 2 hours</span>
                            </div>
                        </div>

                    </div>

                    {{-- Working Hours & Discretion Assurance --}}
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-[#1A1A1A] to-[#262626] border border-[#FF6B35]/30 text-white space-y-2">
                        <div class="flex items-center gap-2 text-[#FF6B35] text-xs font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-clock"></i>
                            <span>Private Appointments</span>
                        </div>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Private viewings and after-hours consultations can be scheduled in advance with a dedicated partner.
                        </p>
                    </div>

                </div>

                {{-- Right: Livewire Contact Form (Span 7) --}}
                <div class="lg:col-span-7">
                    <livewire:customer.contact-form />
                </div>

            </div>
        </div>
    </section>

    {{-- ====================================================================== --}}
    {{-- 3. INTERACTIVE MAP SECTION                                             --}}
    {{-- ====================================================================== --}}
    <section class="py-12 bg-gray-50 dark:bg-[#141414] border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#FF6B35] block mb-1">
                        Location & Directions
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
                        Visit Our Private Gallery
                    </h2>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fa-solid fa-location-arrow text-[#FF6B35] mr-1"></i>
                    {{ $address }}
                </p>
            </div>

            {{-- Map Container with Dark/Light Styling --}}
            <div class="w-full h-[450px] rounded-3xl overflow-hidden border border-gray-200/90 dark:border-gray-800 shadow-xl relative bg-gray-200 dark:bg-[#1A1A1A]">
                @if($map)
                    @if(str_contains($map, '<iframe'))
                        <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0 [&>iframe]:rounded-3xl">
                            {!! $map !!}
                        </div>
                    @else
                        <iframe 
                            src="{{ $map }}" 
                            class="w-full h-full border-0 rounded-3xl" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    @endif
                @else
                    {{-- Default Embedded Google Map for Dubai Marina Plaza --}}
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14451.782875148675!2d55.1328005871582!3d25.076384599999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f134db2faec09%3A0x6b77ff0a7ef2049e!2sMarina%20Plaza%20-%20Dubai%20Marina%20-%20Dubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2sae!4v1700000000000!5m2!1sen!2sae" 
                        class="w-full h-full border-0 rounded-3xl" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                @endif
            </div>

        </div>
    </section>

</x-layouts.customer>

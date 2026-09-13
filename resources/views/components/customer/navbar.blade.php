@php
    $siteName = $settings['site_name'] ?? 'TISHA Real Estate';
    $sitePhone = $settings['phone'] ?? $settings['contact_phone'] ?? '+971 4 123 4567';
    $siteLogo = $settings['logo'] ?? $settings['site_logo'] ?? null;
@endphp

<header 
    x-data="{ 
        mobileMenuOpen: false, 
        scrolled: false 
    }" 
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    class="sticky top-0 z-50 w-full transition-all duration-200 border-b"
    :class="scrolled 
        ? 'bg-white/90 dark:bg-[#1A1A1A]/95 backdrop-blur-md shadow-md py-3 border-gray-200/80 dark:border-gray-800' 
        : 'bg-white/90 dark:bg-[#1A1A1A]/95 backdrop-blur-md py-4 border-gray-100 dark:border-gray-800/80'"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                @if($siteLogo && file_exists(public_path('uploads/settings/' . $siteLogo)))
                    <img 
                        src="{{ asset('uploads/settings/' . $siteLogo) }}" 
                        alt="{{ $siteName }}" 
                        class="h-10 w-auto object-contain"
                    >
                @else
                    {{-- Styled Orange / Black Brand Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-[#1A1A1A] dark:bg-black border border-[#FF6B35]/30 flex items-center justify-center text-[#FF6B35] shadow-md shadow-[#FF6B35]/20 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-building-columns text-lg"></i>
                    </div>
                @endif
                <div>
                    <span class="text-xl font-black tracking-tight text-gray-950 dark:text-white uppercase leading-none block">
                        {{ $siteName }}
                    </span>
                    <span class="text-[10px] font-bold tracking-[0.25em] text-[#FF6B35] uppercase block mt-0.5">
                        Luxury Properties
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-700 dark:text-gray-200">
                <a href="{{ url('/') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition py-1">
                    Home
                </a>
                <a href="{{ url('/#for-sale') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition py-1">
                    For Sale
                </a>
                <a href="{{ url('/#for-rent') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition py-1">
                    For Rent
                </a>
                <a href="{{ url('/#about') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition py-1">
                    About
                </a>
                <a href="{{ url('/#contact') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition py-1">
                    Contact
                </a>
            </nav>

            {{-- Right Actions: Phone Number, Book Consultation Button & Theme Toggle --}}
            <div class="flex items-center gap-3">
                
                {{-- Phone Number Link (from settings) --}}
                <a 
                    href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" 
                    class="hidden lg:flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-[#262626]/80 hover:border-[#FF6B35]/40 transition"
                    title="Call Our Concierge"
                >
                    <i class="fa-solid fa-phone text-[#FF6B35] text-xs"></i>
                    <span>{{ $sitePhone }}</span>
                </a>

                {{-- Book Consultation Button (Orange Accent) --}}
                <a 
                    href="{{ url('/#contact') }}" 
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] shadow-sm shadow-[#FF6B35]/30 hover:shadow-[#FF6B35]/40 transition transform active:scale-95"
                >
                    <i class="fa-regular fa-calendar-check text-xs"></i>
                    <span>Book Consultation</span>
                </a>

                {{-- Sun/Moon Theme Toggle Button --}}
                <button 
                    @click="darkMode = !darkMode" 
                    type="button" 
                    title="Toggle Dark / Light Mode"
                    aria-label="Toggle Theme"
                    class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#262626] text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 transition flex items-center justify-center shadow-xs"
                >
                    <i x-show="!darkMode" class="fa-solid fa-moon text-sm"></i>
                    <i x-show="darkMode" x-cloak class="fa-solid fa-sun text-sm text-[#FF6B35]"></i>
                </button>

                {{-- Mobile Hamburger Menu Button --}}
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="md:hidden w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#262626] text-gray-700 dark:text-gray-200 flex items-center justify-center focus:outline-none"
                    aria-label="Open Navigation Menu"
                >
                    <i x-show="!mobileMenuOpen" class="fa-solid fa-bars text-sm"></i>
                    <i x-show="mobileMenuOpen" x-cloak class="fa-solid fa-xmark text-sm text-[#FF6B35]"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Responsive Dropdown Menu --}}
        <div 
            x-show="mobileMenuOpen" 
            x-cloak 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-1 pb-3"
        >
            <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#262626]">
                Home
            </a>
            <a href="{{ url('/#for-sale') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#262626]">
                For Sale
            </a>
            <a href="{{ url('/#for-rent') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#262626]">
                For Rent
            </a>
            <a href="{{ url('/#about') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#262626]">
                About
            </a>
            <a href="{{ url('/#contact') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#262626]">
                Contact
            </a>

            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-2">
                <a 
                    href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" 
                    class="flex items-center justify-center gap-2 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300"
                >
                    <i class="fa-solid fa-phone text-[#FF6B35]"></i>
                    <span>{{ $sitePhone }}</span>
                </a>

                <a 
                    href="{{ url('/#contact') }}" 
                    @click="mobileMenuOpen = false"
                    class="w-full text-center py-2.5 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] shadow-sm"
                >
                    Book Consultation
                </a>
            </div>
        </div>
    </div>
</header>

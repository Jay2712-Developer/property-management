@php
    $hasSettings = class_exists(\App\Models\SiteSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('site_settings');
    $siteName = $hasSettings 
        ? \App\Models\SiteSetting::getValue('site_name', 'TISHA Real Estate') 
        : 'TISHA Real Estate';

    $sitePhone = $hasSettings 
        ? \App\Models\SiteSetting::getValue('contact_phone', '+971 4 123 4567') 
        : '+971 4 123 4567';
@endphp

<header 
    x-data="{ 
        mobileMenuOpen: false, 
        scrolled: false 
    }" 
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    class="sticky top-0 z-50 w-full transition-all duration-200"
    :class="scrolled 
        ? 'bg-white/95 dark:bg-[#141414]/95 backdrop-blur-md shadow-md py-3 border-b border-gray-200/80 dark:border-gray-800/80' 
        : 'bg-white dark:bg-[#0F0F0F] py-4 border-b border-gray-100 dark:border-gray-800/60'"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#FF8C61] flex items-center justify-center text-white shadow-md shadow-[#FF6B35]/20 group-hover:scale-105 transition-transform duration-200">
                    <i class="fa-solid fa-building text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-black tracking-tight text-gray-950 dark:text-white uppercase leading-none block">
                        TISHA
                    </span>
                    <span class="text-[10px] font-bold tracking-[0.25em] text-[#FF6B35] uppercase block mt-0.5">
                        Real Estate
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-600 dark:text-gray-300">
                <a href="{{ url('/') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    Home
                </a>
                <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    Properties
                </a>
                <a href="{{ url('/#featured') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    Featured
                </a>
                <a href="{{ url('/#agents') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    Agents
                </a>
                <a href="{{ url('/#about') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    About
                </a>
                <a href="{{ url('/#contact') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                    Contact
                </a>
            </nav>

            {{-- Right Actions: Theme Toggle, Phone, CTA --}}
            <div class="flex items-center gap-3">
                
                {{-- Dark Mode Toggle --}}
                <button 
                    @click="darkMode = !darkMode" 
                    type="button" 
                    title="Toggle Theme"
                    aria-label="Toggle Dark Mode"
                    class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#1A1A1A] text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 transition flex items-center justify-center shadow-xs"
                >
                    <i x-show="!darkMode" class="fa-solid fa-moon text-sm"></i>
                    <i x-show="darkMode" x-cloak class="fa-solid fa-sun text-sm text-[#FF6B35]"></i>
                </button>

                {{-- Direct Phone Quick Link (Desktop) --}}
                <a 
                    href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" 
                    class="hidden lg:flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:border-[#FF6B35]/40 transition"
                >
                    <i class="fa-solid fa-phone text-[#FF6B35] text-xs"></i>
                    <span>{{ $sitePhone }}</span>
                </a>

                {{-- Primary CTA Button --}}
                <a 
                    href="{{ url('/#contact') }}" 
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] shadow-sm shadow-[#FF6B35]/30 hover:shadow-[#FF6B35]/40 transition transform active:scale-95"
                >
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>Schedule Tour</span>
                </a>

                {{-- Mobile Menu Hamburger Button --}}
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="md:hidden w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-200 flex items-center justify-center focus:outline-none"
                    aria-label="Open Navigation Menu"
                >
                    <i x-show="!mobileMenuOpen" class="fa-solid fa-bars text-sm"></i>
                    <i x-show="mobileMenuOpen" x-cloak class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div 
            x-show="mobileMenuOpen" 
            x-cloak 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2 pb-3"
        >
            <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                Home
            </a>
            <a href="{{ url('/#properties') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                Properties
            </a>
            <a href="{{ url('/#featured') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                Featured
            </a>
            <a href="{{ url('/#agents') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                Agents
            </a>
            <a href="{{ url('/#about') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                About
            </a>
            <a href="{{ url('/#contact') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#1A1A1A]">
                Contact
            </a>

            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-2">
                <a 
                    href="{{ url('/#contact') }}" 
                    @click="mobileMenuOpen = false"
                    class="w-full text-center py-2.5 rounded-xl text-xs font-bold text-white bg-[#FF6B35] hover:bg-[#E55A2B] shadow-sm"
                >
                    Schedule a Visit
                </a>
            </div>
        </div>
    </div>
</header>

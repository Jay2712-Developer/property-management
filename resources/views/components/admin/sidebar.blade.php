<!-- Sidebar Component -->
<aside 
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#1A1A1A] border-r border-gray-200 dark:border-gray-800 flex flex-col transition-all duration-300 transform md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Brand Logo -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100 dark:border-gray-800/80">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#ff8c5f] flex items-center justify-center text-white shadow-lg shadow-[#FF6B35]/25 group-hover:scale-105 transition-transform duration-200">
                <i class="fa-solid fa-building-user text-lg"></i>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-gray-900 dark:text-white leading-none block">
                    TISHA <span class="text-[#FF6B35]">REALTY</span>
                </span>
                <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 tracking-wider uppercase">
                    Admin Portal
                </span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button 
            @click="sidebarOpen = false" 
            type="button" 
            class="md:hidden p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
        >
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-800">
        <!-- Main Section -->
        <p class="px-3 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            Overview
        </p>

        <a 
            href="{{ route('admin.dashboard') }}" 
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
        >
            <i class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-[#FF6B35]' }}"></i>
            <span>Dashboard</span>
        </a>

        <!-- Property Management Section -->
        <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            Property Catalog
        </p>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-city w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Properties</span>
            <span class="ml-auto text-[11px] font-semibold bg-[#FF6B35]/10 text-[#FF6B35] dark:bg-[#FF6B35]/20 px-2 py-0.5 rounded-full">New</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-shapes w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Property Types</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-tag w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Property Statuses</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-location-dot w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Locations</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-spa w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Amenities</span>
        </a>

        <!-- Operations Section -->
        <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            Operations & Inquiries
        </p>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-user-tie w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Agents</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-regular fa-calendar-check w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Visit Requests</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-regular fa-envelope w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Inquiries</span>
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-regular fa-star w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Testimonials</span>
        </a>

        <!-- Security & System -->
        <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            System & Security
        </p>

        <a 
            href="{{ route('admin.profile.2fa') }}" 
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.profile.2fa') ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
        >
            <i class="fa-solid fa-shield-halved w-5 text-center {{ request()->routeIs('admin.profile.2fa') ? 'text-white' : 'text-gray-400 group-hover:text-[#FF6B35]' }}"></i>
            <span>2FA Security</span>
            @if(auth()->check() && auth()->user()->hasTwoFactorEnabled())
                <span class="ml-auto w-2 h-2 rounded-full bg-emerald-500"></span>
            @endif
        </a>

        <a 
            href="#" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
        >
            <i class="fa-solid fa-sliders w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
            <span>Site Settings</span>
        </a>
    </nav>

    <!-- User Mini Profile Footer -->
    @auth
        <div class="p-4 border-t border-gray-100 dark:border-gray-800/80 bg-gray-50/50 dark:bg-black/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#1A1A1A] to-gray-700 text-white flex items-center justify-center font-bold text-sm shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ auth()->user()->roles->first()->name ?? 'Administrator' }}
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button 
                        type="submit" 
                        title="Sign Out"
                        class="p-2 text-gray-400 hover:text-rose-500 dark:hover:text-rose-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>

<!-- Backdrop overlay for mobile drawer -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false" 
    x-cloak 
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"
></div>

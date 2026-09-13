{{-- Admin Sidebar Component with Dynamic Spatie Permissions & Alpine.js --}}
<aside 
    x-data="{
        propertiesOpen: {{ request()->is('admin/properties*') || request()->is('admin/property-*') ? 'true' : 'false' }},
        locationsOpen: {{ request()->is('admin/locations*') || request()->is('admin/amenities*') ? 'true' : 'false' }},
        inquiriesOpen: {{ request()->is('admin/inquiries*') || request()->is('admin/visits*') ? 'true' : 'false' }},
        contentOpen: {{ request()->is('admin/pages*') || request()->is('admin/media*') ? 'true' : 'false' }}
    }"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#1A1A1A] border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'"
>
    {{-- Brand Logo Area (TISHA Orange / Black) --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100 dark:border-gray-800/80 bg-white dark:bg-[#1A1A1A]">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#ff8c5f] flex items-center justify-center text-white shadow-lg shadow-[#FF6B35]/25 group-hover:scale-105 transition-transform duration-200">
                <i class="fa-solid fa-building-user text-lg"></i>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-[#1A1A1A] dark:text-white leading-none block">
                    TISHA <span class="text-[#FF6B35]">REALTY</span>
                </span>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-400 tracking-wider uppercase">
                    Admin Portal
                </span>
            </div>
        </a>

        {{-- Close Button (Mobile Only) --}}
        <button 
            @click="sidebarOpen = false" 
            type="button" 
            class="md:hidden p-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            aria-label="Close sidebar"
        >
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- Scrollable Navigation Items --}}
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-800">
        
        {{-- MAIN SECTION --}}
        <p class="px-3 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            Main
        </p>

        {{-- 1. Dashboard (Accessible to all admin roles) --}}
        <a 
            href="{{ route('admin.dashboard') }}" 
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
        >
            <i class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-[#FF6B35]' }}"></i>
            <span>Dashboard</span>
        </a>

        {{-- PROPERTY MANAGEMENT SECTION --}}
        @canany(['view_properties', 'manage_properties', 'create_properties'])
            <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
                Property Portfolio
            </p>

            {{-- 2. Properties (Dropdown: All Properties, Add New, Types, Statuses) --}}
            <div>
                <button 
                    @click="propertiesOpen = !propertiesOpen" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->is('admin/properties*') || request()->is('admin/property-*') ? 'text-[#FF6B35] bg-[#FF6B35]/10 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-building w-5 text-center text-gray-400 group-hover:text-[#FF6B35]"></i>
                        <span>Properties</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="propertiesOpen ? 'rotate-180 text-[#FF6B35]' : ''"></i>
                </button>

                {{-- Properties Submenu --}}
                <div 
                    x-show="propertiesOpen" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="mt-1 pl-11 pr-2 space-y-1"
                >
                    {{-- All Properties --}}
                    @can('view_properties')
                        <a 
                            href="{{ url('/admin/properties') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-solid fa-list-ul text-[10px]"></i>
                            <span>All Properties</span>
                        </a>
                    @endcan

                    {{-- Add New Property --}}
                    @can('create_properties')
                        <a 
                            href="{{ url('/admin/properties/create') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-solid fa-plus-circle text-[10px]"></i>
                            <span>Add New</span>
                        </a>
                    @endcan

                    {{-- Property Types & Statuses --}}
                    @canany(['view_properties', 'manage_properties'])
                        <a 
                            href="{{ url('/admin/property-types') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-solid fa-shapes text-[10px]"></i>
                            <span>Property Types</span>
                        </a>

                        <a 
                            href="{{ url('/admin/property-statuses') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-solid fa-tag text-[10px]"></i>
                            <span>Property Statuses</span>
                        </a>
                    @endcanany
                </div>
            </div>

            {{-- 3. Locations & Amenities (Dropdown) --}}
            <div>
                <button 
                    @click="locationsOpen = !locationsOpen" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->is('admin/locations*') || request()->is('admin/amenities*') ? 'text-[#FF6B35] bg-[#FF6B35]/10 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-map-location-dot w-5 text-center text-gray-400 group-hover:text-[#FF6B35]"></i>
                        <span>Locations &amp; Amenities</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="locationsOpen ? 'rotate-180 text-[#FF6B35]' : ''"></i>
                </button>

                <div 
                    x-show="locationsOpen" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="mt-1 pl-11 pr-2 space-y-1"
                >
                    <a 
                        href="{{ url('/admin/locations') }}" 
                        class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                    >
                        <i class="fa-solid fa-location-dot text-[10px]"></i>
                        <span>Locations</span>
                    </a>
                    <a 
                        href="{{ url('/admin/amenities') }}" 
                        class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                    >
                        <i class="fa-solid fa-spa text-[10px]"></i>
                        <span>Amenities</span>
                    </a>
                </div>
            </div>
        @endcanany

        {{-- TEAM & OPERATIONS --}}
        {{-- 4. Agents / Team Management (Permission: manage_agents) --}}
        @can('manage_agents')
            <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
                Team Management
            </p>

            <a 
                href="{{ url('/admin/agents') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
            >
                <i class="fa-solid fa-user-tie w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
                <span>Agents &amp; Team</span>
            </a>
        @endcan

        {{-- 5. Inquiries & Visit Requests --}}
        @canany(['view_inquiries', 'view_visits'])
            <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
                Client Interactions
            </p>

            <div>
                <button 
                    @click="inquiriesOpen = !inquiriesOpen" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->is('admin/inquiries*') || request()->is('admin/visits*') ? 'text-[#FF6B35] bg-[#FF6B35]/10 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-comments w-5 text-center text-gray-400 group-hover:text-[#FF6B35]"></i>
                        <span>Inquiries &amp; Visits</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="inquiriesOpen ? 'rotate-180 text-[#FF6B35]' : ''"></i>
                </button>

                <div 
                    x-show="inquiriesOpen" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="mt-1 pl-11 pr-2 space-y-1"
                >
                    @can('view_inquiries')
                        <a 
                            href="{{ url('/admin/inquiries') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-regular fa-envelope text-[10px]"></i>
                            <span>Contact Inquiries</span>
                        </a>
                    @endcan

                    @can('view_visits')
                        <a 
                            href="{{ url('/admin/visits') }}" 
                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                        >
                            <i class="fa-regular fa-calendar-check text-[10px]"></i>
                            <span>Visit Requests</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany

        {{-- 6. Testimonials --}}
        @canany(['view_testimonials', 'manage_testimonials'])
            <a 
                href="{{ url('/admin/testimonials') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white transition group"
            >
                <i class="fa-regular fa-star w-5 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
                <span>Testimonials</span>
            </a>
        @endcanany

        {{-- CONTENT & MEDIA --}}
        {{-- 7. Pages & Media (Permission: manage_pages) --}}
        @canany(['view_pages', 'manage_pages'])
            <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
                Content &amp; CMS
            </p>

            <div>
                <button 
                    @click="contentOpen = !contentOpen" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->is('admin/pages*') || request()->is('admin/media*') ? 'text-[#FF6B35] bg-[#FF6B35]/10 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-newspaper w-5 text-center text-gray-400 group-hover:text-[#FF6B35]"></i>
                        <span>Pages &amp; Media</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="contentOpen ? 'rotate-180 text-[#FF6B35]' : ''"></i>
                </button>

                <div 
                    x-show="contentOpen" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="mt-1 pl-11 pr-2 space-y-1"
                >
                    <a 
                        href="{{ route('admin.pages.index') }}" 
                        wire:navigate
                        class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                    >
                        <i class="fa-regular fa-file-lines text-[10px]"></i>
                        <span>Custom Pages</span>
                    </a>
                    <a 
                        href="{{ route('admin.media.index') }}" 
                        wire:navigate
                        class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition"
                    >
                        <i class="fa-regular fa-images text-[10px]"></i>
                        <span>Media Library</span>
                    </a>
                </div>
            </div>
        @endcan

        {{-- ADMINISTRATION & ROLES --}}
        @canany(['manage_roles', 'manage_settings'])
            <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
                System Administration
            </p>

            {{-- 8. User & Role Management (Permission: manage_roles) --}}
            @can('manage_roles')
                <a 
                    href="{{ route('admin.roles') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.roles*') ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white group' }}"
                >
                    <i class="fa-solid fa-users-gear w-5 text-center {{ request()->routeIs('admin.roles*') ? 'text-white' : 'text-gray-400 group-hover:text-[#FF6B35]' }} transition-colors"></i>
                    <span>Users &amp; Roles</span>
                </a>
            @endcan

            {{-- 9. Site Settings (Permission: manage_settings) --}}
            @can('manage_settings')
                <a 
                    href="{{ route('admin.settings') }}" 
                    wire:navigate
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.settings*') ? 'bg-[#FF6B35] text-white shadow-md shadow-[#FF6B35]/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white group' }}"
                >
                    <i class="fa-solid fa-sliders w-5 text-center {{ request()->routeIs('admin.settings*') ? 'text-white' : 'text-gray-400 group-hover:text-[#FF6B35]' }} transition-colors"></i>
                    <span>Site Settings</span>
                </a>
            @endcan
        @endcanany

        {{-- Security (Always available for logged-in profile) --}}
        <p class="px-3 pt-5 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2">
            My Account
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
    </nav>

    {{-- Sidebar Footer: Current User Mini-Card & Logout --}}
    @auth
        <div class="p-4 border-t border-gray-100 dark:border-gray-800/80 bg-gray-50/70 dark:bg-black/20">
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

{{-- Backdrop overlay for mobile drawer --}}
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

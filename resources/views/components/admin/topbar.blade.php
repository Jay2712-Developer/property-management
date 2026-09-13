{{-- Admin Topbar Component with Theme Toggle, Activity Notifications & User Profile --}}
<header class="sticky top-0 z-30 h-20 bg-white dark:bg-[#1A1A1A] border-b border-gray-200 dark:border-gray-800 transition-colors duration-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs">
    
    {{-- Left Section: Mobile Sidebar Button & Search Input --}}
    <div class="flex items-center gap-4">
        {{-- Mobile Sidebar Toggle Button --}}
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            type="button" 
            class="md:hidden p-2.5 rounded-xl text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/80 transition"
            aria-label="Open sidebar"
        >
            <i class="fa-solid fa-bars-staggered text-lg"></i>
        </button>

        {{-- Quick Search Bar --}}
        <div class="hidden sm:flex items-center relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input 
                type="text" 
                placeholder="Search properties, agents, inquiries..." 
                class="w-64 lg:w-80 pl-9 pr-12 py-2 bg-gray-100/90 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition duration-200"
            >
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[10px] font-mono text-gray-400">
                <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700">⌘K</kbd>
            </span>
        </div>
    </div>

    {{-- Right Section: Theme Toggle, Notifications Bell & User Profile Dropdown --}}
    <div class="flex items-center gap-2.5 sm:gap-3">
        
        {{-- 1. Live Public Site Shortcut --}}
        <a 
            href="{{ url('/') }}" 
            target="_blank" 
            title="View Public Website"
            class="hidden md:flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-100 dark:hover:bg-gray-800/80 transition"
        >
            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-400 group-hover:text-[#FF6B35]"></i>
            <span>Live Site</span>
        </a>

        {{-- 2. Theme Toggle Button (Switches between White & Dark Mode with Alpine.js & localStorage) --}}
        <button 
            @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
            type="button" 
            title="Toggle theme (Light / Dark Mode)"
            class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#0F0F0F] text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 flex items-center justify-center transition-all duration-200 shadow-xs"
            aria-label="Toggle light or dark theme"
        >
            {{-- Sun Icon (Visible in Dark mode) --}}
            <i x-show="darkMode" class="fa-solid fa-sun text-sm text-amber-400 transition-transform duration-200 hover:rotate-45" style="display: none;"></i>
            {{-- Moon Icon (Visible in White mode) --}}
            <i x-show="!darkMode" class="fa-solid fa-moon text-sm text-gray-700 transition-transform duration-200 hover:-rotate-12"></i>
        </button>

        {{-- 3. Notifications Bell Dropdown (Inquiries, Visits & Recent Activity) --}}
        @php
            $newInquiriesCount = class_exists(\App\Models\ContactInquiry::class)
                ? \App\Models\ContactInquiry::where('status', 'new')->count()
                : 0;
            $pendingVisitsCount = class_exists(\App\Models\VisitRequest::class)
                ? \App\Models\VisitRequest::where('status', 'pending')->count()
                : 0;
            $totalNotificationsCount = $newInquiriesCount + $pendingVisitsCount;

            $recentActivityLogs = class_exists(\App\Models\ActivityLog::class)
                ? \App\Models\ActivityLog::with('user')->latest()->take(5)->get()
                : collect();
        @endphp

        <div x-data="{ notifOpen: false }" class="relative">
            <button 
                @click="notifOpen = !notifOpen" 
                @click.away="notifOpen = false" 
                type="button" 
                title="Notifications & Activity ({{ $totalNotificationsCount }} unread)"
                class="relative w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#0F0F0F] text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:border-[#FF6B35]/40 dark:hover:border-[#FF6B35]/40 flex items-center justify-center transition-all duration-200 shadow-xs"
                :class="notifOpen ? 'text-[#FF6B35] border-[#FF6B35]/40 ring-2 ring-[#FF6B35]/20' : ''"
                aria-label="Notifications"
            >
                <i class="fa-regular fa-bell text-sm"></i>

                {{-- Notification Badge Count --}}
                @if($totalNotificationsCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#FF6B35] text-[10px] font-extrabold text-white ring-2 ring-white dark:ring-[#1A1A1A]">
                        {{ $totalNotificationsCount > 99 ? '99+' : $totalNotificationsCount }}
                    </span>
                @else
                    {{-- Subtle inactive dot --}}
                    <span class="absolute top-2 right-2 flex h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                @endif
            </button>

            {{-- Notifications Dropdown Menu --}}
            <div 
                x-show="notifOpen" 
                x-cloak 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl z-50 overflow-hidden"
            >
                {{-- Dropdown Header --}}
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/60 dark:bg-black/20">
                    <div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bell text-[#FF6B35] text-xs"></i>
                            <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Notifications & Activity Feed</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5">Live updates & alerts</p>
                    </div>
                    @if($totalNotificationsCount > 0)
                        <span class="text-[10px] font-bold text-white bg-[#FF6B35] px-2 py-0.5 rounded-full">
                            {{ $totalNotificationsCount }} Pending
                        </span>
                    @else
                        <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">
                            All caught up
                        </span>
                    @endif
                </div>

                {{-- Actionable Notification Summary Cards --}}
                <div class="p-3 grid grid-cols-2 gap-2 bg-gray-50/40 dark:bg-black/10 border-b border-gray-100 dark:border-gray-800">
                    {{-- New Inquiries --}}
                    <a href="{{ route('admin.inquiries.index') }}" 
                       class="p-2.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#141414] hover:border-[#FF6B35]/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-gray-400 group-hover:text-[#FF6B35] transition">Inquiries</span>
                            <span class="text-xs font-bold {{ $newInquiriesCount > 0 ? 'text-amber-500' : 'text-gray-400' }}">{{ $newInquiriesCount }}</span>
                        </div>
                        <p class="text-[11px] font-semibold text-gray-800 dark:text-gray-200 mt-1">
                            New Messages
                        </p>
                    </a>

                    {{-- Pending Visits --}}
                    <a href="{{ route('admin.visits.index') }}" 
                       class="p-2.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#141414] hover:border-[#FF6B35]/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-gray-400 group-hover:text-[#FF6B35] transition">Visits</span>
                            <span class="text-xs font-bold {{ $pendingVisitsCount > 0 ? 'text-[#FF6B35]' : 'text-gray-400' }}">{{ $pendingVisitsCount }}</span>
                        </div>
                        <p class="text-[11px] font-semibold text-gray-800 dark:text-gray-200 mt-1">
                            Pending Tours
                        </p>
                    </a>
                </div>

                {{-- Activity Logs Feed --}}
                <div class="divide-y divide-gray-100 dark:divide-gray-800/60 max-h-64 overflow-y-auto">
                    <div class="px-4 py-2 bg-gray-50/30 dark:bg-black/10 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                        Recent Audit Actions
                    </div>

                    @forelse($recentActivityLogs as $log)
                        <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition flex items-start gap-2.5 text-xs">
                            <div class="w-6 h-6 rounded-md bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center shrink-0 mt-0.5 text-[10px]">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 dark:text-gray-200 leading-snug truncate">
                                    {{ $log->action }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-gray-400">
                                    <span class="font-medium text-[#FF6B35]">{{ ucfirst($log->module) }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-400">
                            No recent activity logs recorded.
                        </div>
                    @endforelse
                </div>

                {{-- Dropdown Footer --}}
                <div class="p-2.5 border-t border-gray-100 dark:border-gray-800 text-center bg-gray-50/50 dark:bg-black/20">
                    <a 
                        href="{{ route('admin.activity-logs.index') }}" 
                        class="text-[11px] font-semibold text-[#FF6B35] hover:underline"
                    >
                        View Complete Audit Logs &rarr;
                    </a>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="h-6 w-px bg-gray-200 dark:bg-gray-800 mx-0.5 sm:mx-1"></div>

        {{-- 4. User Profile Dropdown --}}
        @auth
            <div x-data="{ profileOpen: false }" class="relative">
                {{-- Profile Trigger Button --}}
                <button 
                    @click="profileOpen = !profileOpen" 
                    @click.away="profileOpen = false" 
                    type="button" 
                    class="flex items-center gap-2.5 p-1 sm:px-2 sm:py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-150 group"
                    :class="profileOpen ? 'bg-gray-100 dark:bg-gray-800' : ''"
                    aria-label="User profile menu"
                >
                    {{-- User Initial Avatar Badge --}}
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#ff8c5f] text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-[#FF6B35]/20 group-hover:scale-105 transition-transform duration-200">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    {{-- Admin Name & Role --}}
                    <div class="hidden lg:block text-left">
                        <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[10px] font-semibold text-[#FF6B35]">
                            {{ auth()->user()->roles->first()->name ?? 'Administrator' }}
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 hidden lg:block transition-transform duration-200" :class="profileOpen ? 'rotate-180 text-[#FF6B35]' : ''"></i>
                </button>

                {{-- User Profile Dropdown Menu --}}
                <div 
                    x-show="profileOpen" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    class="absolute right-0 mt-2 w-60 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl py-2 z-50 divide-y divide-gray-100 dark:divide-gray-800"
                >
                    {{-- Profile Header with Name, Email & Role Badge --}}
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <span class="text-[9px] font-bold text-[#FF6B35] bg-[#FF6B35]/10 px-2 py-0.5 rounded-full shrink-0">
                                {{ auth()->user()->roles->first()->name ?? 'Admin' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>

                    {{-- Navigation Options --}}
                    <div class="py-1.5">
                        {{-- My Profile Option --}}
                        <a 
                            href="{{ route('admin.profile.2fa') }}" 
                            wire:navigate
                            class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition group"
                        >
                            <i class="fa-regular fa-user w-4 text-center text-gray-400 group-hover:text-[#FF6B35] transition-colors"></i>
                            <span>My Profile</span>
                        </a>

                        {{-- 2FA Settings Option --}}
                        <a 
                            href="{{ route('admin.profile.2fa') }}" 
                            wire:navigate
                            class="flex items-center justify-between px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800/80 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition group"
                        >
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-shield-halved w-4 text-center text-[#FF6B35]"></i>
                                <span>2FA Settings</span>
                            </div>
                            @if(auth()->user()->hasTwoFactorEnabled())
                                <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-md">
                                    Active
                                </span>
                            @else
                                <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded-md">
                                    Off
                                </span>
                            @endif
                        </a>
                    </div>

                    {{-- Logout Form (POST to route('admin.logout'), clears session and redirects to admin login) --}}
                    <div class="py-1">
                        <form method="POST" action="{{ route('admin.logout') }}" id="admin-logout-form">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition group"
                            >
                                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center group-hover:-translate-x-0.5 transition-transform"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth

    </div>
</header>

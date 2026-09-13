<!-- Topbar Component -->
<header class="sticky top-0 z-30 h-20 bg-white/80 dark:bg-[#1A1A1A]/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-colors duration-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
    <!-- Left Section: Mobile Toggle & Breadcrumb / Search -->
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Button -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            type="button" 
            class="md:hidden p-2.5 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            aria-label="Open sidebar"
        >
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Search Bar -->
        <div class="hidden sm:flex items-center relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input 
                type="text" 
                placeholder="Search properties, agents, inquiries..." 
                class="w-72 lg:w-96 pl-9 pr-12 py-2 bg-gray-100/80 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition duration-200"
            >
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[10px] font-mono text-gray-400">
                <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 border border-gray-300 dark:border-gray-700">⌘K</kbd>
            </span>
        </div>
    </div>

    <!-- Right Section: Actions, Dark Mode Toggle, Notifications & User Menu -->
    <div class="flex items-center gap-3">
        <!-- Live Property Site Link -->
        <a 
            href="{{ url('/') }}" 
            target="_blank" 
            title="View Public Website"
            class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        >
            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            <span>Live Site</span>
        </a>

        <!-- Dark / Light Mode Switcher Button -->
        <button 
            @click="darkMode = !darkMode" 
            type="button" 
            title="Toggle theme (Light / Dark mode)"
            class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#0F0F0F] text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] flex items-center justify-center transition-colors duration-200"
            aria-label="Toggle theme"
        >
            <!-- Sun Icon (shown in Dark mode) -->
            <i x-show="darkMode" class="fa-regular fa-sun text-base text-amber-400"></i>
            <!-- Moon Icon (shown in Light mode) -->
            <i x-show="!darkMode" class="fa-regular fa-moon text-base text-slate-700"></i>
        </button>

        <!-- Notifications Icon -->
        <button 
            type="button" 
            class="relative w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#0F0F0F] text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] flex items-center justify-center transition-colors duration-200"
            aria-label="Notifications"
        >
            <i class="fa-regular fa-bell text-base"></i>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#FF6B35] ring-2 ring-white dark:ring-[#1A1A1A]"></span>
        </button>

        <!-- Divider -->
        <div class="h-6 w-px bg-gray-200 dark:bg-gray-800 mx-1"></div>

        <!-- User Profile Dropdown -->
        @auth
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open" 
                    @click.away="open = false" 
                    type="button" 
                    class="flex items-center gap-3 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-150"
                >
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#ff8c5f] text-white flex items-center justify-center font-bold text-sm shadow-md shadow-[#FF6B35]/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-400">
                            {{ auth()->user()->roles->first()->name ?? 'Administrator' }}
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 hidden lg:block transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open" 
                    x-cloak 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl py-2 z-50 divide-y divide-gray-100 dark:divide-gray-800"
                >
                    <div class="px-4 py-2.5">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>

                    <div class="py-1">
                        <a 
                            href="{{ route('admin.profile.2fa') }}" 
                            wire:navigate
                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-[#FF6B35] transition"
                        >
                            <i class="fa-solid fa-shield-halved w-4 text-center text-[#FF6B35]"></i>
                            <span>2FA Security</span>
                            @if(auth()->user()->hasTwoFactorEnabled())
                                <span class="ml-auto text-[10px] font-semibold text-emerald-500 bg-emerald-500/10 px-1.5 py-0.5 rounded">Active</span>
                            @endif
                        </a>
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                            >
                                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>

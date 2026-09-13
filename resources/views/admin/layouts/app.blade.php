<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false 
    }" 
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="darkMode ? 'dark' : ''"
    class="h-full"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'TISHA Real Estate - Admin Portal' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Chart.js 4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS (CDN Configured for TISHA Brand & Dark Mode) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        tisha: {
                            orange: '#FF6B35',     // Primary Orange
                            black: '#1A1A1A',      // Secondary Black
                            white: '#FFFFFF',      // Pure White
                            darkBg: '#0F0F0F',     // Dark Background
                            darkCard: '#1A1A1A',   // Dark Card
                            DEFAULT: '#FF6B35',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js & Livewire Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="h-full font-sans antialiased bg-gray-50 dark:bg-[#0F0F0F] text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-screen flex">
        <!-- Sidebar Component -->
        <x-admin.sidebar />

        <!-- Main Wrapper (Offset by sidebar width on desktop) -->
        <div class="flex-1 flex flex-col md:pl-72 min-w-0">
            <!-- Topbar Component -->
            <x-admin.topbar />

            <!-- Main Content Area -->
            <main class="flex-1 bg-gray-50 dark:bg-gray-900 p-4 sm:p-6 lg:p-8 transition-colors duration-200">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Admin Footer -->
            <footer class="border-t border-gray-200 dark:border-gray-800/80 bg-white dark:bg-[#1A1A1A] py-4 px-6 text-center sm:flex sm:justify-between sm:items-center text-xs text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} <span class="font-bold text-gray-800 dark:text-gray-200">TISHA Real Estate</span>. All rights reserved.</p>
                <p class="mt-1 sm:mt-0 font-medium">
                    <span class="text-[#FF6B35]">v1.0.0</span> &bull; Powered by Laravel 12 & Livewire 3
                </p>
            </footer>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>

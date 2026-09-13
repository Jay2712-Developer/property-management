<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true' 
    }" 
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="darkMode ? 'dark' : ''"
    class="scroll-smooth h-full"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'TISHA Real Estate') }} - Luxury Properties & Real Estate</title>
    <meta name="description" content="{{ $metaDescription ?? 'Discover exclusive luxury homes, modern penthouses, and prime estates with TISHA Real Estate.' }}">

    <!-- Inline Dark Mode check to prevent flash of light theme -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS (Vite or CDN fallback with TISHA Brand Palette) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        tisha: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#FF6B35',     // Primary Orange
                            600: '#E55A2B',     // Darker Orange
                            700: '#C2431B',
                            800: '#9A3412',
                            900: '#1A1A1A',     // Secondary Black
                            orange: '#FF6B35',
                            black: '#1A1A1A',
                            white: '#FFFFFF',
                            darkBg: '#0F0F0F',
                            darkCard: '#1A1A1A',
                            DEFAULT: '#FF6B35',
                        },
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#FF6B35',
                            600: '#E55A2B',
                            DEFAULT: '#FF6B35',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(255, 107, 53, 0.3)',
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles & Animations -->
    <style>
        [x-cloak] { display: none !important; }

        html {
            scroll-behavior: smooth;
        }

        /* Fade-up animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animation-delay-100 { animation-delay: 100ms; }
        .animation-delay-200 { animation-delay: 200ms; }
        .animation-delay-300 { animation-delay: 300ms; }
        .animation-delay-400 { animation-delay: 400ms; }

        /* Custom scrollbar matching TISHA brand */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f0f0f;
        }
        ::-webkit-scrollbar-thumb {
            background: #2a2a2a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #FF6B35;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col font-sans antialiased bg-gray-50 text-gray-800 dark:bg-[#0F0F0F] dark:text-gray-200 transition-colors duration-200 selection:bg-[#FF6B35] selection:text-white">

    <!-- Top Navigation Bar -->
    <x-customer.navbar />

    <!-- Main Content Area -->
    <main id="main-content" class="flex-1 w-full animate-fade-up">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Customer Footer -->
    <x-customer.footer />

    <!-- Alpine.js & Livewire Script Hooks -->
    @livewireScripts
    <script>
        // Livewire 3 Page Transition & Scroll Anchor restoration
        document.addEventListener('livewire:navigated', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    @stack('scripts')
</body>
</html>

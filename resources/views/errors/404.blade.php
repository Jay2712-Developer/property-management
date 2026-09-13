<x-layouts.customer 
    title="404 - Page Not Found | TISHA Real Estate"
    metaDescription="The property or page you are looking for does not exist on TISHA Real Estate."
>
    <section class="min-h-[70vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414]">
        
        {{-- Background Glow Accent --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[280px] bg-gradient-to-tr from-[#FF6B35]/20 to-amber-500/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-xl w-full text-center">
            
            {{-- Big 404 Visual with TISHA Orange Gradient --}}
            <div class="relative mb-6">
                <span class="text-8xl sm:text-9xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-b from-[#FF6B35] to-[#E55A2B]/40 select-none">
                    404
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 rounded-2xl bg-white/90 dark:bg-[#1A1A1A]/90 backdrop-blur-md border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-center text-[#FF6B35] text-2xl">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl sm:text-3xl font-black text-gray-950 dark:text-white tracking-tight">
                Page Not Found
            </h1>

            {{-- Exact Required Message --}}
            <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                The property or page you are looking for doesn't exist or may have been relocated.
            </p>

            {{-- Navigation Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a 
                    href="{{ route('home') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-[#FF6B35]/30 transition transform active:scale-95 flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Return to Homepage</span>
                </a>

                <a 
                    href="{{ route('sales') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-[#262626] text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider border border-gray-200 dark:border-gray-800 transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-magnifying-glass text-xs text-[#FF6B35]"></i>
                    <span>Explore Properties</span>
                </a>

                <a 
                    href="{{ route('contact') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-transparent hover:bg-gray-100 dark:hover:bg-[#1A1A1A] text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-headset text-xs"></i>
                    <span>Contact Concierge</span>
                </a>
            </div>

            {{-- Quick Links Bar --}}
            <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-800 flex items-center justify-center gap-6 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('sales') }}" class="hover:text-[#FF6B35] transition">Villas For Sale</a>
                <span>&bull;</span>
                <a href="{{ route('rentals') }}" class="hover:text-[#FF6B35] transition">Penthouses For Rent</a>
                <span>&bull;</span>
                <a href="{{ route('about') }}" class="hover:text-[#FF6B35] transition">About TISHA</a>
            </div>

        </div>
    </section>
</x-layouts.customer>

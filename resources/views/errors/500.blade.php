<x-layouts.customer 
    title="500 - Server Error | TISHA Real Estate"
    metaDescription="Server Error. Something went wrong on our end. Please try again later."
>
    <section class="min-h-[70vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414]">
        
        {{-- Background Glow Accent --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[280px] bg-gradient-to-tr from-[#FF6B35]/20 to-amber-600/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-xl w-full text-center">
            
            {{-- Big 500 Visual with TISHA Orange Gradient --}}
            <div class="relative mb-6">
                <span class="text-8xl sm:text-9xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-b from-[#FF6B35] to-[#E55A2B]/40 select-none">
                    500
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 rounded-2xl bg-white/90 dark:bg-[#1A1A1A]/90 backdrop-blur-md border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-center text-amber-500 text-2xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl sm:text-3xl font-black text-gray-950 dark:text-white tracking-tight">
                Server Error
            </h1>

            {{-- Required Message --}}
            <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                Something went wrong on our end. Our technical team has been notified. Please try again later.
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

                <button 
                    onclick="window.location.reload()" 
                    type="button"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-[#262626] text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider border border-gray-200 dark:border-gray-800 transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-arrows-rotate text-xs text-[#FF6B35]"></i>
                    <span>Refresh Page</span>
                </button>

                <a 
                    href="{{ route('contact') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-transparent hover:bg-gray-100 dark:hover:bg-[#1A1A1A] text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-headset text-xs"></i>
                    <span>Contact Concierge</span>
                </a>
            </div>

        </div>
    </section>
</x-layouts.customer>

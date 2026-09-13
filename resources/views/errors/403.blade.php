<x-layouts.customer 
    title="403 - Access Denied | TISHA Real Estate"
    metaDescription="Access Denied. You do not have permission to view this page on TISHA Real Estate."
>
    <section class="min-h-[70vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414]">
        
        {{-- Background Glow --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[280px] bg-gradient-to-tr from-[#FF6B35]/20 to-rose-500/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-xl w-full text-center">
            
            {{-- Big 403 Visual with TISHA Orange / Rose Gradient --}}
            <div class="relative mb-6">
                <span class="text-8xl sm:text-9xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-b from-[#FF6B35] to-rose-600/40 select-none">
                    403
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 rounded-2xl bg-white/90 dark:bg-[#1A1A1A]/90 backdrop-blur-md border border-gray-200 dark:border-gray-800 shadow-xl flex items-center justify-center text-rose-500 text-2xl">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>
            </div>

            {{-- HTTP 403 Badge --}}
            <div class="mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] animate-ping"></span>
                    <span>HTTP 403 &bull; Access Forbidden</span>
                </span>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl sm:text-3xl font-black text-gray-950 dark:text-white tracking-tight">
                Access Denied &bull; Permission Denied
            </h1>

            {{-- Required Message --}}
            <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                {{ $message ?? ($exception ? $exception->getMessage() : 'Access Denied. You do not have permission to view this page.') }}
            </p>

            {{-- Diagnostic role box for authenticated users --}}
            @auth
                <div class="mt-6 p-4 rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800 text-left max-w-md mx-auto shadow-sm">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-700 dark:text-gray-300">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-user-shield text-[#FF6B35]"></i>
                            <span>Current Role:</span>
                        </span>
                        <span class="font-bold text-[#FF6B35]">
                            {{ auth()->user()->roles->first()->name ?? 'No Role Assigned' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Signed in as <span class="font-medium text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</span>.
                    </p>
                </div>
            @endauth

            {{-- Navigation Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a 
                    href="{{ route('home') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-[#FF6B35]/30 transition transform active:scale-95 flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Return to Homepage</span>
                </a>

                @auth
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-[#262626] text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-wider border border-gray-200 dark:border-gray-800 transition flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-gauge-high text-xs text-[#FF6B35]"></i>
                        <span>Back to Dashboard</span>
                    </a>
                @endauth

                <a 
                    href="{{ route('contact') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-transparent hover:bg-gray-100 dark:hover:bg-[#1A1A1A] text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-headset text-xs"></i>
                    <span>Contact Support</span>
                </a>
            </div>

        </div>
    </section>
</x-layouts.customer>

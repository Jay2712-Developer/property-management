@extends(auth()->check() ? 'admin.layouts.app' : 'admin.layouts.app')

@section('content')
<div class="min-h-[75vh] flex flex-col items-center justify-center p-4 sm:p-6 text-center">
    
    {{-- 403 Forbidden Container --}}
    <div class="max-w-lg w-full bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">
        
        {{-- Ambient Orange Glow Background --}}
        <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-[#FF6B35]/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-rose-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            {{-- Lock / Warning Icon Badge --}}
            <div class="w-20 h-20 rounded-2xl bg-rose-500/10 dark:bg-rose-950/40 text-rose-500 border border-rose-500/20 flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="fa-solid fa-shield-halved text-3xl"></i>
            </div>

            {{-- HTTP Code Badge --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/30 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] animate-ping"></span>
                HTTP 403 &bull; Access Forbidden
            </span>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Permission Denied
            </h1>

            {{-- Descriptive Message --}}
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-3 leading-relaxed">
                {{ $message ?? ($exception ? $exception->getMessage() : 'You do not have the required permissions to view or interact with this administrative resource.') }}
            </p>

            {{-- Permission Diagnostic Box for Authenticated Users --}}
            @auth
                <div class="mt-6 p-4 rounded-2xl bg-gray-50 dark:bg-[#0F0F0F] border border-gray-200 dark:border-gray-800 text-left">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-user-shield text-[#FF6B35]"></i>
                            <span>Current Role:</span>
                        </span>
                        <span class="font-bold text-[#FF6B35]">
                            {{ auth()->user()->roles->first()->name ?? 'No Role Assigned' }}
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-400">
                        Signed in as <span class="font-medium text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</span>. If you require access to this section, please request permission from a <span class="text-amber-500 font-semibold">Super Admin</span>.
                    </p>
                </div>
            @endauth

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                {{-- Back to Dashboard Button --}}
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e05622] text-white text-xs sm:text-sm font-bold shadow-lg shadow-[#FF6B35]/25 transition-all duration-200 active:scale-95"
                >
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Back to Dashboard</span>
                </a>

                {{-- Re-login / Sign Out Option --}}
                <form method="POST" action="{{ route('admin.logout') }}" class="w-full sm:w-auto">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-semibold transition"
                    >
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs text-rose-500"></i>
                        <span>Sign In as Another User</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

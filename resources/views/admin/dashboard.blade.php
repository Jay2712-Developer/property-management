<x-layouts.app :title="'Admin Dashboard - TISHA Real Estate'">
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Top Nav -->
        <nav class="border-b border-slate-800 bg-slate-900/50 backdrop-blur-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center font-black text-slate-950">
                    T
                </div>
                <div>
                    <h1 class="text-base font-bold text-white leading-none">TISHA Real Estate</h1>
                    <span class="text-xs text-teal-400 font-medium">Admin Control Panel</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.profile.2fa') }}" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-lg text-slate-200 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    2FA Security
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-semibold rounded-lg transition">
                        Sign Out
                    </button>
                </form>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
            <div class="bg-gradient-to-r from-teal-950/40 via-slate-900 to-slate-900 border border-teal-900/30 rounded-3xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold text-white">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-sm text-slate-400 mt-1">Logged in with roles: <span class="text-teal-400 font-semibold">{{ auth()->user()->roles->pluck('name')->implode(', ') ?: 'No Role Assigned' }}</span></p>

                <div class="mt-6 flex gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-teal-500/10 text-teal-400 border border-teal-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-teal-400' : 'bg-amber-400' }}"></span>
                        {{ auth()->user()->hasTwoFactorEnabled() ? '2FA Protection Active' : '2FA Recommended' }}
                    </span>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>

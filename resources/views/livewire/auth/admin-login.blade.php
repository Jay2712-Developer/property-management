<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950">
    <!-- Decorative background glow elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <!-- Logo & Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-teal-500 to-teal-300 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/25 mb-4">
                <svg class="w-8 h-8 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">TISHA Real Estate</h2>
            <p class="mt-2 text-sm text-slate-400">Admin Portal Sign In</p>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl">
            <form wire:submit="login" class="space-y-6">
                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <input 
                            wire:model="email" 
                            id="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            placeholder="admin@tisha.com"
                            class="w-full bg-slate-950/60 border @error('email') border-rose-500/80 ring-rose-500/20 @else border-slate-700/80 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-xl px-4 py-3 text-slate-200 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 transition duration-200"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs text-rose-400 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Password
                    </label>
                    <input 
                        wire:model="password" 
                        id="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required 
                        placeholder="••••••••"
                        class="w-full bg-slate-950/60 border @error('password') border-rose-500/80 ring-rose-500/20 @else border-slate-700/80 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-xl px-4 py-3 text-slate-200 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 transition duration-200"
                    >
                    @error('password')
                        <p class="mt-2 text-xs text-rose-400 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            wire:model="remember" 
                            type="checkbox" 
                            class="w-4 h-4 rounded border-slate-700 text-teal-600 bg-slate-950 focus:ring-teal-500/30"
                        >
                        <span class="text-xs text-slate-400 select-none">Remember this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-slate-950 font-bold rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/30 transition-all duration-200 flex items-center justify-center gap-2 group disabled:opacity-50"
                    >
                        <span wire:loading.remove>Sign In to Dashboard</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Authenticating...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500">
            Protected by dynamic role permissions and Two-Factor Authentication.
        </p>
    </div>
</div>

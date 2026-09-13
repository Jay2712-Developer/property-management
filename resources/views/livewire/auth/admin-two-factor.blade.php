<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950">
    <!-- Decorative background glow elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <!-- Logo & Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-teal-500 to-teal-300 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/25 mb-4">
                <svg class="w-8 h-8 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">Two-Factor Challenge</h2>
            <p class="mt-2 text-sm text-slate-400">
                @if($usingRecoveryCode)
                    Enter one of your emergency recovery codes to authenticate.
                @else
                    Enter the 6-digit code from your authenticator app (Google Authenticator, Authy, etc.).
                @endif
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl">
            <form wire:submit="verify" class="space-y-6">
                @if(!$usingRecoveryCode)
                    <!-- 6-digit OTP Code Input -->
                    <div>
                        <label for="code" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2 text-center">
                            Authentication Code
                        </label>
                        <input 
                            wire:model="code" 
                            id="code" 
                            type="text" 
                            inputmode="numeric" 
                            pattern="[0-9]*" 
                            maxlength="6" 
                            autofocus 
                            autocomplete="one-time-code"
                            placeholder="000000"
                            class="w-full bg-slate-950/60 border @error('code') border-rose-500/80 ring-rose-500/20 @else border-slate-700/80 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-xl px-4 py-3.5 text-center text-2xl font-mono tracking-widest text-teal-400 placeholder-slate-600 focus:outline-none focus:ring-2 transition duration-200"
                        >
                        @error('code')
                            <p class="mt-2 text-xs text-rose-400 flex items-center justify-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @else
                    <!-- Emergency Recovery Code Input -->
                    <div>
                        <label for="recovery_code" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            Recovery Code
                        </label>
                        <input 
                            wire:model="recovery_code" 
                            id="recovery_code" 
                            type="text" 
                            autofocus 
                            placeholder="e.g. abcde-12345"
                            class="w-full bg-slate-950/60 border @error('recovery_code') border-rose-500/80 ring-rose-500/20 @else border-slate-700/80 focus:border-teal-500 focus:ring-teal-500/20 @enderror rounded-xl px-4 py-3 text-center font-mono text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition duration-200"
                        >
                        @error('recovery_code')
                            <p class="mt-2 text-xs text-rose-400 flex items-center justify-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-slate-950 font-bold rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/30 transition-all duration-200 flex items-center justify-center gap-2 group disabled:opacity-50"
                    >
                        <span wire:loading.remove>Verify & Sign In</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </div>

                <!-- Toggle recovery code / OTP code -->
                <div class="text-center pt-2">
                    <button 
                        type="button" 
                        wire:click="toggleRecoveryMode" 
                        class="text-xs text-teal-400 hover:text-teal-300 transition-colors duration-150 underline underline-offset-4"
                    >
                        @if($usingRecoveryCode)
                            Use an authentication app code instead
                        @else
                            Use an emergency recovery code
                        @endif
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('admin.login') }}" wire:navigate class="text-xs text-slate-400 hover:text-slate-200 transition-colors">
                ← Back to Login
            </a>
        </div>
    </div>
</div>

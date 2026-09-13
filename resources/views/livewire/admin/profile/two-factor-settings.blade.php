<div class="min-h-screen bg-slate-950 text-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Top Navigation / Breadcrumbs -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Two-Factor Authentication (2FA)</h1>
                <p class="mt-1 text-sm text-slate-400">Add an additional layer of security to your admin account.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-700/80 rounded-xl text-xs font-semibold text-slate-300 hover:text-white transition">
                ← Dashboard
            </a>
        </div>

        <!-- Flash notification -->
        @if (session()->has('status'))
            <div class="bg-teal-500/10 border border-teal-500/30 rounded-2xl p-4 flex items-center gap-3 text-teal-300 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-slate-900/70 border border-slate-800/90 rounded-3xl p-8 backdrop-blur-xl shadow-2xl space-y-8">
            <!-- Current Status Badge -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl {{ $user->hasTwoFactorEnabled() ? 'bg-teal-500/20 text-teal-400' : 'bg-slate-800 text-slate-400' }} flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-white">Status: 
                            @if($user->hasTwoFactorEnabled())
                                <span class="text-teal-400 font-bold ml-1">Enabled</span>
                            @else
                                <span class="text-slate-400 font-medium ml-1">Disabled</span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $user->hasTwoFactorEnabled() 
                                ? 'Your account is secured with time-based one-time passwords (TOTP).' 
                                : 'When enabled, you will be prompted for a 6-digit secure token upon logging in.' }}
                        </p>
                    </div>
                </div>

                @if($user->hasTwoFactorEnabled())
                    <button 
                        type="button" 
                        wire:click="disableTwoFactor"
                        wire:confirm="Are you sure you want to disable Two-Factor Authentication?"
                        class="px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-xl text-xs font-semibold transition"
                    >
                        Disable 2FA
                    </button>
                @elseif(!$showingQrCode)
                    <button 
                        type="button" 
                        wire:click="enableTwoFactor"
                        class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-slate-950 rounded-xl text-xs font-bold shadow-lg shadow-teal-500/20 transition"
                    >
                        Enable 2FA
                    </button>
                @endif
            </div>

            <!-- Enablement Flow / QR Code Setup -->
            @if($showingQrCode)
                <div class="border-t border-slate-800 pt-8 space-y-6">
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-6">
                        <h3 class="text-sm font-semibold text-teal-400 uppercase tracking-wider mb-2">
                            Step 1: Scan QR Code with Authenticator App
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            Open your authenticator app (Google Authenticator, Microsoft Authenticator, 1Password, or Authy) and scan this QR code, or manually enter the key below.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-8">
                            <!-- QR Code SVG -->
                            <div class="p-3 bg-white rounded-2xl shadow-xl border border-slate-200">
                                {!! $qrCodeSvg !!}
                            </div>

                            <!-- Secret Key Box -->
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                        Manual Entry Secret Key:
                                    </span>
                                    <code class="font-mono text-sm text-teal-300 bg-slate-900 border border-slate-700/80 px-3 py-1.5 rounded-lg select-all inline-block">
                                        {{ $secretKey }}
                                    </code>
                                </div>
                                <p class="text-xs text-slate-500 max-w-sm">
                                    Time-based (TOTP), 6 digits, 30-second interval.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Confirm Code -->
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-teal-400 uppercase tracking-wider">
                            Step 2: Enter 6-Digit Code to Confirm
                        </h3>
                        <p class="text-xs text-slate-400">
                            Enter the 6-digit code currently shown in your authenticator app to complete setup.
                        </p>

                        <div class="flex items-center gap-4 max-w-sm">
                            <input 
                                wire:model="confirmationCode" 
                                type="text" 
                                maxlength="6" 
                                placeholder="000000"
                                class="w-40 bg-slate-900 border @error('confirmationCode') border-rose-500 @else border-slate-700 @enderror rounded-xl px-4 py-2.5 text-center font-mono text-lg text-teal-400 tracking-widest focus:outline-none focus:border-teal-500"
                            >
                            <button 
                                wire:click="confirmTwoFactor"
                                class="px-5 py-3 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-slate-950 font-bold rounded-xl text-xs shadow-md transition"
                            >
                                Confirm & Activate
                            </button>
                        </div>
                        @error('confirmationCode')
                            <p class="text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Recovery Codes Section -->
            @if($user->hasTwoFactorEnabled() && !empty($recoveryCodes))
                <div class="border-t border-slate-800 pt-8 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-white">Emergency Recovery Codes</h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Store these recovery codes in a secure password manager. Each code can be used once to log in if you lose your device.
                            </p>
                        </div>
                        <button 
                            wire:click="regenerateRecoveryCodes"
                            wire:confirm="Regenerating codes will invalidate all existing recovery codes. Continue?"
                            class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-medium transition"
                        >
                            Regenerate Codes
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 font-mono text-xs text-teal-300">
                        @foreach($recoveryCodes as $code)
                            <div class="p-2 bg-slate-900/80 border border-slate-800 rounded-lg text-center select-all">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

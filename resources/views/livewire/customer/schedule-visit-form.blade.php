<div class="p-6 sm:p-8">
    @if($submitted)
        <div class="text-center py-6 animate-fade-up">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-4 text-2xl border border-emerald-500/30">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">
                Tour Request Confirmed
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto leading-relaxed mb-6">
                Thank you. Your request for a private guided inspection has been submitted. Our concierge will contact you shortly to confirm arrangements.
            </p>
            <div class="flex items-center justify-center gap-3">
                <button 
                    type="button" 
                    @click="showModal = false" 
                    class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-[#262626] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#333333] text-xs font-bold transition"
                >
                    Close Window
                </button>
                <button 
                    type="button" 
                    wire:click="resetForm" 
                    class="px-6 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold transition shadow-sm"
                >
                    Schedule Another
                </button>
            </div>
        </div>
    @else
        <div>
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-[#FF6B35]"></i>
                        <span>Schedule a Private Tour</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Arrange an exclusive viewing with our senior advisory team.
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="showModal = false" 
                    class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#262626] transition flex items-center justify-center"
                    aria-label="Close modal"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="submit" class="space-y-4">
                
                {{-- Name --}}
                <div>
                    <label for="visit-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-[#FF6B35]">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-gray-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            id="visit-name"
                            wire:model="name" 
                            placeholder="Lord Alexander Vance" 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email & Phone (Grid) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    {{-- Email --}}
                    <div>
                        <label for="visit-email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            Email Address <span class="text-[#FF6B35]">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-gray-400 text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input 
                                type="email" 
                                id="visit-email"
                                wire:model="email" 
                                placeholder="client@domain.com" 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                                required
                            >
                        </div>
                        @error('email')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="visit-phone" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            Phone / WhatsApp <span class="text-[#FF6B35]">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-gray-400 text-xs">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input 
                                type="tel" 
                                id="visit-phone"
                                wire:model="phone" 
                                placeholder="+971 50 123 4567" 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                                required
                            >
                        </div>
                        @error('phone')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Visit Date --}}
                <div>
                    <label for="visit-date" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                        Preferred Date & Time <span class="text-[#FF6B35]">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-gray-400 text-xs">
                            <i class="fa-regular fa-clock"></i>
                        </span>
                        <input 
                            type="datetime-local" 
                            id="visit-date"
                            wire:model="visit_date" 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                            required
                        >
                    </div>
                    @error('visit_date')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-[#FF6B35]/30 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50"
                    >
                        <span wire:loading.remove>Confirm Viewing Request</span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            <span>Scheduling...</span>
                        </span>
                        <i wire:loading.remove class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <p class="text-[11px] text-center text-gray-400">
                    <i class="fa-solid fa-lock text-[10px] text-[#FF6B35] mr-1"></i>
                    Strict client privacy & non-disclosure guaranteed.
                </p>

            </form>
        </div>
    @endif
</div>

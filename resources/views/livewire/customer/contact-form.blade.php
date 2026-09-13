<div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#1A1A1A] border border-gray-200/90 dark:border-gray-800 shadow-xl">
    @if($submitted)
        <div class="text-center py-8 animate-fade-up">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-4 text-2xl border border-emerald-500/30">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">
                Message Received
            </h3>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed mb-6">
                Thank you for reaching out to TISHA Real Estate. Your confidential inquiry has been routed to our senior property advisory team. We will respond within 2 business hours.
            </p>
            <button 
                type="button" 
                wire:click="resetForm" 
                class="px-6 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-[#FF6B35]/30"
            >
                Send Another Message
            </button>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-4">
            
            <div class="mb-4">
                <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-regular fa-paper-plane text-[#FF6B35]"></i>
                    <span>Direct Concierge Inquiry</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Complete the form below and an advisory partner will assist your request.
                </p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="contact-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                    Full Name <span class="text-[#FF6B35]">*</span>
                </label>
                <div class="relative flex items-center">
                    <span class="absolute left-3.5 text-gray-400 text-xs">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input 
                        type="text" 
                        id="contact-name"
                        wire:model="name" 
                        placeholder="e.g. Lord Alexander Vance" 
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                        required
                    >
                </div>
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email & Phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- Email --}}
                <div>
                    <label for="contact-email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-[#FF6B35]">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-gray-400 text-xs">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            id="contact-email"
                            wire:model="email" 
                            placeholder="client@domain.com" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                            required
                        >
                    </div>
                    @error('email')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="contact-phone" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                        Phone Number <span class="text-[#FF6B35]">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-gray-400 text-xs">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input 
                            type="tel" 
                            id="contact-phone"
                            wire:model="phone" 
                            placeholder="+971 50 123 4567" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                            required
                        >
                    </div>
                    @error('phone')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Subject Dropdown --}}
            <div>
                <label for="contact-subject" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                    Inquiry Subject <span class="text-[#FF6B35]">*</span>
                </label>
                <div class="relative flex items-center">
                    <span class="absolute left-3.5 text-gray-400 text-xs pointer-events-none">
                        <i class="fa-solid fa-tag text-[#FF6B35]"></i>
                    </span>
                    <select 
                        id="contact-subject"
                        wire:model="subject" 
                        class="w-full pl-10 pr-8 py-3 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition cursor-pointer"
                        required
                    >
                        <option value="General Inquiry">General Concierge Inquiry</option>
                        <option value="Property Buying Consultation">Property Buying Consultation</option>
                        <option value="Property Selling / Valuation">Property Selling & Valuation</option>
                        <option value="Private Villa Tour Request">Private Villa Tour Request</option>
                        <option value="Luxury Rental Consultation">Luxury Rental Consultation</option>
                        <option value="Investment Advisory / Portfolio">Investment Advisory & Portfolio Management</option>
                    </select>
                </div>
                @error('subject')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label for="contact-message" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                    Your Confidential Message <span class="text-[#FF6B35]">*</span>
                </label>
                <textarea 
                    id="contact-message"
                    wire:model="message" 
                    rows="5" 
                    placeholder="Tell us about your property requirements, preferred locations, budget parameters, or specific questions..."
                    class="w-full p-4 rounded-xl bg-gray-50 dark:bg-[#262626] border border-gray-200 dark:border-gray-700/80 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition resize-y"
                    required
                ></textarea>
                @error('message')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full py-4 rounded-xl bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-[#FF6B35]/30 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50"
                >
                    <span wire:loading.remove>Send Message</span>
                    <span wire:loading class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Transmitting Inquiry...</span>
                    </span>
                    <i wire:loading.remove class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>

            <p class="text-[11px] text-center text-gray-400 pt-1">
                <i class="fa-solid fa-shield-halved text-[10px] text-[#FF6B35] mr-1"></i>
                We respect your confidentiality. Your details are never shared with third parties.
            </p>

        </form>
    @endif
</div>

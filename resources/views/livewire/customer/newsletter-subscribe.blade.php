<div>
    @if($subscribed)
        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2.5 animate-fade-up">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
            <div>
                <p class="font-bold">Thank you for subscribing!</p>
                <p class="text-[11px] text-emerald-300/80">You will receive exclusive luxury listings and market updates.</p>
            </div>
        </div>
    @else
        <form wire:submit.prevent="subscribe" class="space-y-2">
            <div class="relative flex items-center">
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="Enter your email address..." 
                    class="w-full pl-3.5 pr-24 py-2.5 rounded-xl bg-[#262626] border border-gray-700/80 text-xs text-white placeholder-gray-400 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition"
                    required
                >
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="absolute right-1 px-3 py-1.5 rounded-lg bg-[#FF6B35] hover:bg-[#E55A2B] text-white text-[11px] font-bold tracking-wide transition flex items-center gap-1.5 shadow-sm active:scale-95 disabled:opacity-50"
                >
                    <span wire:loading.remove>Join VIP</span>
                    <span wire:loading><i class="fa-solid fa-spinner fa-spin text-xs"></i></span>
                    <i wire:loading.remove class="fa-solid fa-paper-plane text-[10px]"></i>
                </button>
            </div>

            @error('email')
                <p class="text-[11px] text-rose-400 font-medium pl-1">{{ $message }}</p>
            @enderror
            
            <p class="text-[11px] text-gray-400 leading-snug">
                Receive confidential private listings, price drops, and curated real estate analysis. Unsubscribe anytime.
            </p>
        </form>
    @endif
</div>

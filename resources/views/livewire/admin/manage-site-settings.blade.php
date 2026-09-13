@can('manage_settings')
<div class="space-y-6">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Site Settings</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                System &amp; Site Settings
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Customize global website identity, contact details, social links, and SEO parameters.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200 disabled:opacity-50">
                <span wire:loading.remove wire:target="save, logo, favicon">
                    <i class="fa-solid fa-cloud-arrow-up text-xs mr-1"></i> Save Changes
                </span>
                <span wire:loading wire:target="save, logo, favicon">
                    <i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Saving Changes...
                </span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Settings Card Container with Navigation Tabs --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pt-4 bg-gray-50/50 dark:bg-[#141414]/50">
            <div class="flex flex-wrap gap-2 sm:gap-6 -mb-px">
                {{-- General Tab --}}
                <button type="button" wire:click="setTab('general')"
                        class="inline-flex items-center gap-2 pb-4 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'general' ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>General Identity</span>
                </button>

                {{-- Contact Tab --}}
                <button type="button" wire:click="setTab('contact')"
                        class="inline-flex items-center gap-2 pb-4 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'contact' ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    <i class="fa-solid fa-address-book"></i>
                    <span>Contact &amp; Map</span>
                </button>

                {{-- Social Media Tab --}}
                <button type="button" wire:click="setTab('social')"
                        class="inline-flex items-center gap-2 pb-4 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'social' ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>Social Media</span>
                </button>

                {{-- SEO & Analytics Tab --}}
                <button type="button" wire:click="setTab('seo')"
                        class="inline-flex items-center gap-2 pb-4 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'seo' ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                    <span>SEO &amp; Analytics</span>
                </button>
            </div>
        </div>

        {{-- Form Content Area --}}
        <form wire:submit="save" class="p-6 sm:p-8 space-y-6">
            {{-- ==================== TAB 1: GENERAL ==================== --}}
            @if($activeTab === 'general')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Brand &amp; Identity</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configure your website's main brand title, copyright notice, logo, and browser icon.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Site Name --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Site / Company Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="site_name"
                                   placeholder="e.g. TISHA Real Estate"
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('site_name') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Copyright Text --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Copyright Statement
                            </label>
                            <input type="text" wire:model="site_copyright"
                                   placeholder="e.g. © 2026 TISHA Real Estate. All rights reserved."
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('site_copyright') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800">

                    {{-- Media: Logo & Favicon in Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Logo Upload Card --}}
                        <div class="p-5 rounded-2xl bg-gray-50/60 dark:bg-[#141414]/70 border border-gray-100 dark:border-gray-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-gray-900 dark:text-white">Primary Website Logo</h3>
                                    <p class="text-[11px] text-gray-400">Header brand logo (PNG, SVG, JPG, WebP up to 2MB)</p>
                                </div>
                                @if($current_logo)
                                    <button type="button" wire:click="removeLogo"
                                            class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold transition">
                                        <i class="fa-solid fa-trash mr-1"></i> Remove
                                    </button>
                                @endif
                            </div>

                            {{-- Logo Preview --}}
                            <div class="h-24 w-full rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] flex items-center justify-center p-3 relative overflow-hidden">
                                @if($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="max-h-full max-w-full object-contain">
                                    <span class="absolute bottom-1 right-2 text-[10px] bg-amber-500 text-white font-bold px-1.5 py-0.5 rounded">Pending Save</span>
                                @elseif($current_logo)
                                    <img src="{{ asset('storage/' . $current_logo) }}" alt="Current Logo" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="flex items-center gap-2 text-[#FF6B35]">
                                        <i class="fa-solid fa-building text-2xl"></i>
                                        <span class="font-extrabold text-lg text-gray-900 dark:text-white">TISHA</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <input type="file" wire:model="logo" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FF6B35]/10 file:text-[#FF6B35] hover:file:bg-[#FF6B35]/20 cursor-pointer">
                                @error('logo') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Favicon Upload Card --}}
                        <div class="p-5 rounded-2xl bg-gray-50/60 dark:bg-[#141414]/70 border border-gray-100 dark:border-gray-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-gray-900 dark:text-white">Browser Favicon</h3>
                                    <p class="text-[11px] text-gray-400">Browser tab icon (ICO, PNG, SVG up to 1MB)</p>
                                </div>
                                @if($current_favicon)
                                    <button type="button" wire:click="removeFavicon"
                                            class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold transition">
                                        <i class="fa-solid fa-trash mr-1"></i> Remove
                                    </button>
                                @endif
                            </div>

                            {{-- Favicon Preview --}}
                            <div class="h-24 w-full rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] flex items-center justify-center p-3 relative overflow-hidden">
                                @if($favicon)
                                    <img src="{{ $favicon->temporaryUrl() }}" alt="Favicon Preview" class="w-10 h-10 object-contain">
                                    <span class="absolute bottom-1 right-2 text-[10px] bg-amber-500 text-white font-bold px-1.5 py-0.5 rounded">Pending Save</span>
                                @elseif($current_favicon)
                                    <img src="{{ asset('storage/' . $current_favicon) }}" alt="Current Favicon" class="w-10 h-10 object-contain">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-house"></i>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <input type="file" wire:model="favicon" accept=".ico,.png,.svg,.jpg,.webp"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FF6B35]/10 file:text-[#FF6B35] hover:file:bg-[#FF6B35]/20 cursor-pointer">
                                @error('favicon') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==================== TAB 2: CONTACT ==================== --}}
            @if($activeTab === 'contact')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Contact &amp; Physical Office</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Provide official communication channels and location details for prospective clients.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Phone --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Official Phone Number
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </span>
                                <input type="text" wire:model="contact_phone"
                                       placeholder="e.g. +1 (555) 234-5678"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('contact_phone') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Primary Contact Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </span>
                                <input type="email" wire:model="contact_email"
                                       placeholder="e.g. info@tishaproperty.com"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('contact_email') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Address (Full width) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Physical Office Address
                            </label>
                            <textarea wire:model="contact_address" rows="3"
                                      placeholder="e.g. Suite 4200, Marina Plaza, Dubai Marina, Dubai, UAE"
                                      class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition"></textarea>
                            @error('contact_address') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Google Maps Embed Code --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Google Maps Embed Iframe Code
                            </label>
                            <textarea wire:model="contact_map_iframe" rows="4"
                                      placeholder='<iframe src="https://www.google.com/maps/embed?..." width="600" height="450" ...></iframe>'
                                      class="w-full font-mono text-[11px] px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition"></textarea>
                            <p class="text-[10px] text-gray-400 mt-1">Paste the full iframe code from Google Maps share modal.</p>
                            @error('contact_map_iframe') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==================== TAB 3: SOCIAL MEDIA ==================== --}}
            @if($activeTab === 'social')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Social Media Channels</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Link your luxury brokerage's official social media profiles for visitors to connect.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Facebook --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Facebook Profile URL
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#1877F2]">
                                    <i class="fa-brands fa-facebook text-sm"></i>
                                </span>
                                <input type="url" wire:model="social_facebook"
                                       placeholder="https://facebook.com/tisharealestate"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('social_facebook') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Twitter / X --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Twitter / X Profile URL
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-800 dark:text-gray-200">
                                    <i class="fa-brands fa-x-twitter text-sm"></i>
                                </span>
                                <input type="url" wire:model="social_twitter"
                                       placeholder="https://x.com/tishaproperty"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('social_twitter') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Instagram --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Instagram Profile URL
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#E1306C]">
                                    <i class="fa-brands fa-instagram text-sm"></i>
                                </span>
                                <input type="url" wire:model="social_instagram"
                                       placeholder="https://instagram.com/tisharealestate"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('social_instagram') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- LinkedIn --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                LinkedIn Company Page URL
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#0A66C2]">
                                    <i class="fa-brands fa-linkedin text-sm"></i>
                                </span>
                                <input type="url" wire:model="social_linkedin"
                                       placeholder="https://linkedin.com/company/tisha-real-estate"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('social_linkedin') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==================== TAB 4: SEO & ANALYTICS ==================== --}}
            @if($activeTab === 'seo')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Search Engine Optimization &amp; Analytics</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configure global metadata tags and tracking codes for maximum search discoverability.</p>
                    </div>

                    <div class="space-y-5">
                        {{-- Meta Title --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    Default Meta Title
                                </label>
                                <span class="text-[10px] text-gray-400">{{ strlen($seo_meta_title) }} / 60 recommended</span>
                            </div>
                            <input type="text" wire:model="seo_meta_title"
                                   placeholder="e.g. TISHA Real Estate - Luxury Properties & Prime Investments"
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('seo_meta_title') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Meta Description --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    Default Meta Description
                                </label>
                                <span class="text-[10px] text-gray-400">{{ strlen($seo_meta_description) }} / 160 recommended</span>
                            </div>
                            <textarea wire:model="seo_meta_description" rows="3"
                                      placeholder="e.g. Explore exclusive luxury properties, penthouses, and private estates with TISHA Real Estate..."
                                      class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition"></textarea>
                            @error('seo_meta_description') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Google Analytics Measurement ID --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Google Analytics Measurement ID (GA4)
                            </label>
                            <div class="relative max-w-sm">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chart-line text-xs"></i>
                                </span>
                                <input type="text" wire:model="seo_google_analytics_id"
                                       placeholder="e.g. G-XXXXXXXXXX"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition font-mono">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Google Analytics 4 Measurement ID starting with G-</p>
                            @error('seo_google_analytics_id') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Footer Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-800">
                <div class="text-xs text-gray-400">
                    <span>Active Section: </span>
                    <span class="font-bold text-gray-700 dark:text-gray-300 capitalize">{{ $activeTab }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-xs font-bold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save, logo, favicon">
                            <i class="fa-solid fa-check mr-1.5"></i> Save Settings
                        </span>
                        <span wire:loading wire:target="save, logo, favicon">
                            <i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Updating...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<div class="p-8 text-center bg-white dark:bg-[#1A1A1A] rounded-2xl border border-rose-200 dark:border-rose-900">
    <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-xl mb-3">
        <i class="fa-solid fa-shield-xmark"></i>
    </div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white">Access Denied</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have administrative permission to modify site configuration settings.</p>
</div>
@endcan

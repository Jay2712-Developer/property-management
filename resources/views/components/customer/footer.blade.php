@php
    $hasSettings = class_exists(\App\Models\SiteSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('site_settings');

    $siteName = $hasSettings ? \App\Models\SiteSetting::getValue('site_name', 'TISHA Real Estate') : 'TISHA Real Estate';
    $siteEmail = $hasSettings ? \App\Models\SiteSetting::getValue('contact_email', 'info@tisharealty.com') : 'info@tisharealty.com';
    $sitePhone = $hasSettings ? \App\Models\SiteSetting::getValue('contact_phone', '+971 4 123 4567') : '+971 4 123 4567';
    $siteAddress = $hasSettings ? \App\Models\SiteSetting::getValue('contact_address', 'Suite 1402, Marina Plaza, Dubai Marina, Dubai, UAE') : 'Suite 1402, Marina Plaza, Dubai Marina, Dubai, UAE';
    $copyrightText = $hasSettings ? \App\Models\SiteSetting::getValue('copyright_text', 'All rights reserved.') : 'All rights reserved.';

    $facebookUrl = $hasSettings ? \App\Models\SiteSetting::getValue('social_facebook', '#') : '#';
    $twitterUrl = $hasSettings ? \App\Models\SiteSetting::getValue('social_twitter', '#') : '#';
    $instagramUrl = $hasSettings ? \App\Models\SiteSetting::getValue('social_instagram', '#') : '#';
    $linkedinUrl = $hasSettings ? \App\Models\SiteSetting::getValue('social_linkedin', '#') : '#';
@endphp

<footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-600 dark:text-gray-400 text-sm transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
            
            {{-- Column 1: Brand & About (Span 2 on desktop) --}}
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#FF6B35] to-[#FF8C61] flex items-center justify-center text-white shadow-md shadow-[#FF6B35]/20">
                        <i class="fa-solid fa-building text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-gray-950 dark:text-white uppercase leading-none block">
                            TISHA
                        </span>
                        <span class="text-[10px] font-bold tracking-[0.25em] text-[#FF6B35] uppercase block mt-0.5">
                            Real Estate
                        </span>
                    </div>
                </a>

                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm">
                    Connecting discerning buyers and investors with exceptional luxury residences, waterfront estates, and prime architectural properties.
                </p>

                {{-- Social Icons --}}
                <div class="flex items-center gap-2 pt-2">
                    <a href="{{ $facebookUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-800 flex items-center justify-center hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white dark:hover:text-white transition" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f text-xs"></i>
                    </a>
                    <a href="{{ $twitterUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-800 flex items-center justify-center hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white dark:hover:text-white transition" aria-label="Twitter">
                        <i class="fa-brands fa-x-twitter text-xs"></i>
                    </a>
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-800 flex items-center justify-center hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white dark:hover:text-white transition" aria-label="Instagram">
                        <i class="fa-brands fa-instagram text-xs"></i>
                    </a>
                    <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-800 flex items-center justify-center hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white dark:hover:text-white transition" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin-in text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4">
                    Explore
                </h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            All Properties
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#featured') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Featured Listings
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#agents') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Our Expert Agents
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#about') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            About TISHA
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#contact') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Schedule a Tour
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Property Types --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4">
                    Categories
                </h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Luxury Villas
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Modern Penthouses
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Beachfront Mansions
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Urban Townhouses
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/#properties') }}" class="hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition">
                            Commercial Spaces
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4">
                    Headquarters
                </h3>
                <ul class="space-y-3 text-xs sm:text-sm">
                    <li class="flex items-start gap-2.5">
                        <i class="fa-solid fa-location-dot text-[#FF6B35] mt-1 shrink-0"></i>
                        <span>{{ $siteAddress }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone text-[#FF6B35] shrink-0"></i>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="hover:text-[#FF6B35] transition">
                            {{ $sitePhone }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-envelope text-[#FF6B35] shrink-0"></i>
                        <a href="mailto:{{ $siteEmail }}" class="hover:text-[#FF6B35] transition truncate">
                            {{ $siteEmail }}
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright & Legal Bar --}}
        <div class="mt-12 pt-6 border-t border-gray-100 dark:border-gray-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-400">
            <p>
                &copy; {{ date('Y') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $siteName }}</span>. {{ $copyrightText }}
            </p>

            <div class="flex items-center gap-6">
                <a href="{{ url('/login') }}" class="text-gray-400 hover:text-[#FF6B35] transition">
                    Staff Portal
                </a>
                <span>&bull;</span>
                <span class="text-[#FF6B35] font-semibold">TISHA Luxury Real Estate</span>
            </div>
        </div>
    </div>
</footer>

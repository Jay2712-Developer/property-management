@php
    $siteName = $settings['site_name'] ?? 'TISHA Real Estate';
    $siteEmail = $settings['email'] ?? $settings['contact_email'] ?? 'info@tisharealty.com';
    $sitePhone = $settings['phone'] ?? $settings['contact_phone'] ?? '+971 4 123 4567';
    $siteAddress = $settings['address'] ?? $settings['contact_address'] ?? 'Suite 1402, Marina Plaza, Dubai Marina, Dubai, UAE';
    $copyrightText = $settings['copyright_text'] ?? 'All rights reserved.';

    $facebookUrl = $settings['social_facebook'] ?? '#';
    $twitterUrl = $settings['social_twitter'] ?? '#';
    $instagramUrl = $settings['social_instagram'] ?? '#';
    $linkedinUrl = $settings['social_linkedin'] ?? '#';
@endphp

<footer class="bg-[#1A1A1A] border-t border-gray-800 text-gray-400 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- Column 1: About & Social Icons (Span 4 on large screens) --}}
            <div class="lg:col-span-4 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-black border border-[#FF6B35]/40 flex items-center justify-center text-[#FF6B35] shadow-md shadow-[#FF6B35]/20">
                        <i class="fa-solid fa-building-columns text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white uppercase leading-none block">
                            {{ $siteName }}
                        </span>
                        <span class="text-[10px] font-bold tracking-[0.25em] text-[#FF6B35] uppercase block mt-0.5">
                            Real Estate
                        </span>
                    </div>
                </a>

                <p class="text-xs sm:text-sm text-gray-400 leading-relaxed max-w-sm">
                    Premier luxury brokerage representing distinguished estates, waterfront villas, and iconic urban penthouses across prime international destinations.
                </p>

                {{-- Contact Info Snippet --}}
                <div class="space-y-1.5 pt-2 text-xs text-gray-400">
                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[#FF6B35] shrink-0"></i>
                        <span>{{ $siteAddress }}</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-[#FF6B35] shrink-0"></i>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="hover:text-white transition">
                            {{ $sitePhone }}
                        </a>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-[#FF6B35] shrink-0"></i>
                        <a href="mailto:{{ $siteEmail }}" class="hover:text-white transition truncate">
                            {{ $siteEmail }}
                        </a>
                    </p>
                </div>

                {{-- Social Icons from Settings --}}
                <div class="flex items-center gap-2 pt-3">
                    @if($facebookUrl && $facebookUrl !== '#')
                        <a href="{{ $facebookUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                    @else
                        <a href="#" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                    @endif

                    @if($twitterUrl && $twitterUrl !== '#')
                        <a href="{{ $twitterUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Twitter">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                    @else
                        <a href="#" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Twitter">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                    @endif

                    @if($instagramUrl && $instagramUrl !== '#')
                        <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                    @else
                        <a href="#" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                    @endif

                    @if($linkedinUrl && $linkedinUrl !== '#')
                        <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                    @else
                        <a href="#" class="w-9 h-9 rounded-xl bg-[#262626] border border-gray-700/80 flex items-center justify-center text-gray-300 hover:bg-[#FF6B35] hover:border-[#FF6B35] hover:text-white transition" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Column 2: Quick Links (Span 2) --}}
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35]"></span>
                    <span>Quick Links</span>
                </h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li>
                        <a href="{{ url('/') }}" class="hover:text-[#FF6B35] transition">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('sales') }}" class="hover:text-[#FF6B35] transition">For Sale</a>
                    </li>
                    <li>
                        <a href="{{ route('rentals') }}" class="hover:text-[#FF6B35] transition">For Rent</a>
                    </li>
                    <li>
                        <a href="{{ url('/#featured') }}" class="hover:text-[#FF6B35] transition">Featured Listings</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-[#FF6B35] transition">About Us</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-[#FF6B35] transition">Contact</a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Services (Span 3) --}}
            <div class="lg:col-span-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35]"></span>
                    <span>Our Services</span>
                </h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-[10px] text-[#FF6B35]"></i>
                        <span>Luxury Property Acquisition</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-[10px] text-[#FF6B35]"></i>
                        <span>Exclusive Seller Representation</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-[10px] text-[#FF6B35]"></i>
                        <span>High-Yield Investment Advisory</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-[10px] text-[#FF6B35]"></i>
                        <span>Private & VIP Property Tours</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-[10px] text-[#FF6B35]"></i>
                        <span>Comprehensive Asset Valuation</span>
                    </li>
                </ul>
            </div>

            {{-- Column 4: Newsletter (Span 3) --}}
            <div class="lg:col-span-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35]"></span>
                    <span>Newsletter</span>
                </h3>
                <p class="text-xs text-gray-400 mb-3 leading-relaxed">
                    Subscribe to receive invitations to private previews and market insights.
                </p>

                {{-- Livewire Customer Newsletter Subscription Component --}}
                <livewire:customer.newsletter-subscribe />
            </div>

        </div>

        {{-- Bottom Bar: Copyright & Legal Links --}}
        <div class="mt-12 pt-6 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-400">
            <p>
                &copy; {{ date('Y') }} <span class="font-semibold text-gray-200">{{ $siteName }}</span>. {{ $copyrightText }}
            </p>

            <div class="flex items-center gap-5">
                <a href="{{ url('/privacy-policy') }}" class="hover:text-[#FF6B35] transition">
                    Privacy Policy
                </a>
                <span>&bull;</span>
                <a href="{{ url('/terms-of-service') }}" class="hover:text-[#FF6B35] transition">
                    Terms of Service
                </a>
                <span>&bull;</span>
                <a href="{{ route('admin.login') }}" class="text-gray-400 hover:text-[#FF6B35] transition">
                    Staff Portal
                </a>
            </div>
        </div>
    </div>
</footer>

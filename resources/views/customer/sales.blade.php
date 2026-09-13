<x-layouts.customer 
    title="Properties For Sale - TISHA Real Estate"
    metaDescription="Browse exclusive luxury homes, modern villas, and penthouses for sale in Dubai with TISHA Real Estate."
>
    {{-- Header Banner --}}
    <section class="pt-16 pb-12 bg-gradient-to-b from-gray-100 via-white to-gray-50 dark:from-black dark:via-[#0F0F0F] dark:to-[#141414] border-b border-gray-200/80 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            {{-- Breadcrumb Badge --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-[#1A1A1A]/80 backdrop-blur-md text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-4">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-[#FF6B35]">For Sale</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-gray-950 dark:text-white">
                Properties For Sale
            </h1>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 max-w-xl mx-auto leading-relaxed">
                Discover exceptional private residences, prime architectural estates, and waterfront mansions available for purchase.
            </p>
        </div>
    </section>

    {{-- Listing Content --}}
    <section class="py-12 bg-gray-50/50 dark:bg-[#0F0F0F] min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:customer.property-listing status="sale" />
        </div>
    </section>
</x-layouts.customer>

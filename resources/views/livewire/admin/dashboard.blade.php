<div class="space-y-8">
    
    {{-- Welcome Banner with TISHA Brand Palette --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#1A1A1A] via-gray-900 to-[#1A1A1A] p-6 sm:p-8 text-white shadow-xl border border-gray-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-[#FF6B35]/15 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FF6B35]/20 text-[#FF6B35] border border-[#FF6B35]/30 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] animate-pulse"></span>
                    TISHA REALTY HQ &bull; PORTAL METRICS
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-400">
                    Real-time overview of portfolio listings, client inquiries, and team activity logs.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('admin.profile.2fa') }}" 
                    wire:navigate
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20' }}"
                >
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ auth()->user()->hasTwoFactorEnabled() ? '2FA Protected' : 'Enable 2FA' }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 1. Stats KPI Cards (4 Cards with Icons, Counts, and Subtle Backgrounds) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Total Properties --}}
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Properties</span>
                <div class="w-11 h-11 rounded-2xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-city text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-3">
                {{ number_format($totalProperties) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5">
                <span class="font-semibold text-[#FF6B35]">Portfolio database</span>
                <span>&bull; Registered listings</span>
            </p>
        </div>

        {{-- Active Listings --}}
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Listings</span>
                <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-building-circle-check text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-3">
                {{ number_format($activeListings) }}
            </p>
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center gap-1.5">
                <i class="fa-solid fa-globe"></i>
                <span>Live on public market</span>
            </p>
        </div>

        {{-- Pending Inquiries --}}
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Inquiries</span>
                <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center shadow-inner">
                    <i class="fa-regular fa-envelope text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-3">
                {{ number_format($pendingInquiries) }}
            </p>
            <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mt-1 flex items-center gap-1.5">
                <i class="fa-solid fa-bell"></i>
                <span>Awaiting agent response</span>
            </p>
        </div>

        {{-- Scheduled Visits --}}
        <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Scheduled Visits</span>
                <div class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center shadow-inner">
                    <i class="fa-regular fa-calendar-check text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-3">
                {{ number_format($scheduledVisits) }}
            </p>
            <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mt-1 flex items-center gap-1.5">
                <i class="fa-solid fa-clock"></i>
                <span>Property showings</span>
            </p>
        </div>
    </div>

    {{-- 2. Chart Section & Recent Activities Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left 2 Columns: Chart showing "Properties Added per Month" for Current Year --}}
        <div class="lg:col-span-2 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
            
            {{-- Chart Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-[#FF6B35]"></i>
                        <span>Properties Added per Month</span>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Listing velocity and additions for the year {{ $currentYear }}.
                    </p>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-[#FF6B35]/10 text-[#FF6B35] self-start sm:self-auto">
                    <i class="fa-regular fa-calendar text-[10px]"></i>
                    Year {{ $currentYear }}
                </span>
            </div>

            {{-- Chart Canvas Wrapper with Alpine Lifecycle --}}
            <div 
                x-data="{
                    chartInstance: null,
                    initChart() {
                        const canvas = document.getElementById('propertiesMonthlyChart');
                        if (!canvas || typeof Chart === 'undefined') return;

                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                        }

                        const isDark = document.documentElement.classList.contains('dark');
                        const textColor = isDark ? '#9CA3AF' : '#6B7280';
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                        this.chartInstance = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: @json($monthLabels),
                                datasets: [{
                                    label: 'Properties Added',
                                    data: @json($monthlyCounts),
                                    backgroundColor: '#FF6B35',
                                    hoverBackgroundColor: '#e05622',
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    barPercentage: 0.55,
                                    categoryPercentage: 0.8,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#1A1A1A',
                                        titleColor: '#FFFFFF',
                                        bodyColor: '#FF6B35',
                                        borderColor: '#333333',
                                        borderWidth: 1,
                                        padding: 12,
                                        cornerRadius: 10,
                                        displayColors: false,
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { 
                                            color: textColor, 
                                            stepSize: 1,
                                            font: { family: 'Plus Jakarta Sans', size: 11 } 
                                        }
                                    }
                                }
                            }
                        });
                    }
                }"
                x-init="initChart()"
                class="relative h-64 sm:h-80 w-full"
            >
                <canvas id="propertiesMonthlyChart"></canvas>
            </div>
        </div>

        {{-- Right 1 Column: Recent Activities (Last 5-10 actions) --}}
        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl p-6 shadow-xs flex flex-col">
            
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#FF6B35]"></i>
                    <span>Recent Activities</span>
                </h2>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                    Audit Feed
                </span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-3.5 divide-y divide-gray-100 dark:divide-gray-800/60 max-h-80 pr-1">
                @forelse($recentActivities as $activity)
                    <div class="pt-3.5 first:pt-0 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-bolt text-[11px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                {{ $activity->action }}
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                <span class="font-medium text-[#FF6B35]">{{ ucfirst($activity->module) }}</span>
                                <span>&bull;</span>
                                <span>{{ $activity->user->name ?? 'System' }}</span>
                                <span>&bull;</span>
                                <span>{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Default Real Estate Action Demonstrations --}}
                    <div class="pt-3.5 first:pt-0 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-building text-[11px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                New luxury penthouse added to Downtown portfolio
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                <span class="font-medium text-[#FF6B35]">Properties</span>
                                <span>&bull;</span>
                                <span>Just now</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3.5 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-shield-halved text-[11px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                Security permissions updated for Manager role
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                <span class="font-medium text-purple-500">Security</span>
                                <span>&bull;</span>
                                <span>15 mins ago</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3.5 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-envelope-open-text text-[11px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                Client inquiry replied for Villa Sunset Horizon
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                <span class="font-medium text-emerald-500">Inquiries</span>
                                <span>&bull;</span>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 3. Recent Inquiries Table (Last 3 Unread Inquiries) --}}
    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-800/90 rounded-3xl shadow-xs overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 dark:border-gray-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/40 dark:bg-black/10">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-regular fa-comments text-[#FF6B35]"></i>
                    <span>Recent Client Inquiries</span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Latest unread and pending client contact messages.
                </p>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 self-start sm:self-auto">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>Action Required</span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800/80 bg-gray-50/70 dark:bg-black/20 text-gray-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3.5 px-6">Client Name</th>
                        <th class="py-3.5 px-6">Email / Phone</th>
                        <th class="py-3.5 px-6">Subject / Message</th>
                        <th class="py-3.5 px-6">Received</th>
                        <th class="py-3.5 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                    @forelse($recentInquiries as $inquiry)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors">
                            {{-- Client Name --}}
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#1A1A1A] to-gray-700 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($inquiry->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $inquiry->name }}</span>
                                </div>
                            </td>

                            {{-- Email / Phone --}}
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                                <div>{{ $inquiry->email }}</div>
                                @if($inquiry->phone)
                                    <div class="text-[11px] text-gray-400">{{ $inquiry->phone }}</div>
                                @endif
                            </td>

                            {{-- Subject & Message Snippet --}}
                            <td class="py-4 px-6 max-w-xs sm:max-w-md">
                                <span class="font-bold text-gray-900 dark:text-white block truncate">
                                    {{ $inquiry->subject ?: 'General Inquiry' }}
                                </span>
                                <span class="text-xs text-gray-400 block truncate">
                                    {{ Str::limit($inquiry->message, 80) }}
                                </span>
                            </td>

                            {{-- Received --}}
                            <td class="py-4 px-6 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $inquiry->created_at ? $inquiry->created_at->diffForHumans() : 'Recently' }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-4 px-6 text-right">
                                @if($inquiry->status === 'new' || $inquiry->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>New Inquiry</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        <span>Replied</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400">
                                <i class="fa-regular fa-envelope-open text-2xl mb-2 block"></i>
                                <p class="text-xs font-semibold">No pending inquiries at this moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@canany(['view_visits', 'manage_visits'])
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Visit Requests</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Property Visit Requests
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Manage appointment bookings and on-site property tour requests from prospects.
            </p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search by client name, email, phone, or property title..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending Approval</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Visit Requests Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4">Requested Date</th>
                        <th class="py-3.5 px-4">Property</th>
                        <th class="py-3.5 px-4">Client Name</th>
                        <th class="py-3.5 px-4">Phone &amp; Email</th>
                        <th class="py-3.5 px-4">Scheduled Visit</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Request Created Date --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-gray-900 dark:text-white block">{{ $visit->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $visit->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Property Title --}}
                            <td class="py-3.5 px-4 text-xs font-semibold text-gray-900 dark:text-white max-w-[200px]">
                                @if($visit->property)
                                    <span class="block truncate" title="{{ $visit->property->title }}">{{ $visit->property->title }}</span>
                                    <span class="text-[11px] text-gray-400 font-normal">
                                        @if($visit->property->price)
                                            {{ $visit->property->formatted_price }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">General Listing / Unassigned</span>
                                @endif
                            </td>

                            {{-- Client Name --}}
                            <td class="py-3.5 px-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $visit->name }}
                            </td>

                            {{-- Phone & Email --}}
                            <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                                @if($visit->phone)
                                    <a href="tel:{{ $visit->phone }}" class="text-gray-700 dark:text-gray-300 hover:text-[#FF6B35] font-medium flex items-center gap-1.5 mb-0.5">
                                        <i class="fa-solid fa-phone text-[10px] text-gray-400"></i>
                                        <span>{{ $visit->phone }}</span>
                                    </a>
                                @endif
                                @if($visit->email)
                                    <a href="mailto:{{ $visit->email }}" class="text-gray-400 hover:text-[#FF6B35] text-[11px] flex items-center gap-1.5">
                                        <i class="fa-regular fa-envelope text-[10px]"></i>
                                        <span>{{ $visit->email }}</span>
                                    </a>
                                @endif
                            </td>

                            {{-- Visit Date --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                @if($visit->visit_date)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-medium">
                                        <i class="fa-regular fa-calendar-days text-[#FF6B35]"></i>
                                        <span>{{ $visit->visit_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Date flexible</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($visit->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif($visit->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Rejected
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- View Details --}}
                                    <button type="button"
                                            wire:click="viewDetails('{{ $visit->hashid }}')"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-500 hover:text-[#FF6B35] hover:bg-[#FF6B35]/10 dark:hover:bg-[#FF6B35]/20 transition"
                                            title="View Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                    @can('manage_visits')
                                        {{-- Approve Button --}}
                                        @if($visit->status !== 'approved')
                                            <button type="button"
                                                    wire:click="approveVisit('{{ $visit->hashid }}')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition flex items-center gap-1"
                                                    title="Approve Visit">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span>Approve</span>
                                            </button>
                                        @endif

                                        {{-- Reject Button --}}
                                        @if($visit->status !== 'rejected')
                                            <button type="button"
                                                    wire:click="rejectVisit('{{ $visit->hashid }}')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition flex items-center gap-1"
                                                    title="Reject Visit">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                <span>Reject</span>
                                            </button>
                                        @endif

                                        {{-- Delete Button --}}
                                        <button type="button"
                                                wire:click="confirmDelete('{{ $visit->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                                title="Delete Request">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fa-regular fa-calendar-xmark text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No visit requests found</p>
                                <p class="text-xs text-gray-400 mt-0.5">Incoming tour appointments booked by clients will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $visits->links() }}
            </div>
        @endif
    </div>

    {{-- View Visit Details Modal --}}
    <div x-data="{ show: @entangle('showDetailsModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-lg w-full p-6 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-5"
             @click.outside="$wire.closeDetailsModal()">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-lg">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Visit Request Details</h3>
                        <p class="text-[11px] text-gray-400">Received {{ $selectedVisit?->created_at?->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            @if($selectedVisit)
                <div class="space-y-4 text-xs">
                    {{-- Property Card --}}
                    <div class="p-3.5 rounded-xl bg-gray-50/70 dark:bg-[#141414] border border-gray-100 dark:border-gray-800">
                        <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block mb-1">Target Property</span>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $selectedVisit->property?->title ?: 'Unspecified Property' }}
                        </div>
                        @if($selectedVisit->property?->price)
                            <div class="text-xs font-semibold text-[#FF6B35] mt-0.5">
                                {{ $selectedVisit->property->formatted_price }}
                            </div>
                        @endif
                    </div>

                    {{-- Client & Schedule Info --}}
                    <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-gray-50/70 dark:bg-[#141414] border border-gray-100 dark:border-gray-800">
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Client Name</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedVisit->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Current Status</span>
                            <span class="capitalize font-semibold text-gray-900 dark:text-white">{{ $selectedVisit->status }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Email</span>
                            <a href="mailto:{{ $selectedVisit->email }}" class="text-[#FF6B35] hover:underline font-medium">{{ $selectedVisit->email ?: 'N/A' }}</a>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Phone</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $selectedVisit->phone ?: 'N/A' }}</span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-gray-200/50 dark:border-gray-800">
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Visit Schedule</span>
                            <div class="flex items-center gap-2 mt-1 text-gray-900 dark:text-white font-semibold text-xs">
                                <i class="fa-regular fa-clock text-[#FF6B35]"></i>
                                <span>{{ $selectedVisit->visit_date ? $selectedVisit->visit_date->format('l, F j, Y \a\t h:i A') : 'Flexible / To be confirmed' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @can('manage_visits')
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2">
                            @if($selectedVisit->status !== 'approved')
                                <button type="button" wire:click="approveVisit('{{ $selectedVisit->hashid }}')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i>
                                    <span>Approve Request</span>
                                </button>
                            @endif

                            @if($selectedVisit->status !== 'rejected')
                                <button type="button" wire:click="rejectVisit('{{ $selectedVisit->hashid }}')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>Reject Request</span>
                                </button>
                            @else
                                <button type="button" wire:click="markAsPending('{{ $selectedVisit->hashid }}')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500/30 transition">
                                    <span>Reset to Pending</span>
                                </button>
                            @endif
                        </div>

                        <button type="button" wire:click="closeDetailsModal"
                                class="px-4 py-1.5 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                            Close
                        </button>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal (Alpine.js / Livewire) --}}
    <div x-data="{ show: @entangle('showDeleteModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-5"
             @click.outside="$wire.cancelDelete()">
            
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Visit Request</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to permanently delete this visit request?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">Client: {{ $visitToDeleteClient }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $visitToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteVisit" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteVisit">Yes, Delete Request</span>
                    <span wire:loading wire:target="deleteVisit"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center bg-white dark:bg-[#1A1A1A] rounded-2xl border border-rose-200 dark:border-rose-900">
    <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-xl mb-3">
        <i class="fa-solid fa-shield-xmark"></i>
    </div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white">Access Denied</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to view visit requests.</p>
</div>
@endcanany

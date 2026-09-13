@canany(['view_inquiries', 'manage_inquiries'])
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Contact Inquiries</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Contact Inquiries
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Review and respond to inquiries from prospective clients and property buyers.
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
                       placeholder="Search by name, email, phone, subject, or message content..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="new">New Inquiries</option>
                    <option value="replied">Replied</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Inquiries Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Name</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Phone</th>
                        <th class="py-3.5 px-4">Subject</th>
                        <th class="py-3.5 px-4">Assigned Agent</th>
                        <th class="py-3.5 px-4">Message</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Date --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-gray-900 dark:text-white block">{{ $inquiry->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $inquiry->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Name --}}
                            <td class="py-3.5 px-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $inquiry->name }}
                            </td>

                            {{-- Email --}}
                            <td class="py-3.5 px-4 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <a href="mailto:{{ $inquiry->email }}" class="hover:text-[#FF6B35] transition flex items-center gap-1.5">
                                    <i class="fa-regular fa-envelope text-[11px] text-gray-400"></i>
                                    <span>{{ $inquiry->email }}</span>
                                </a>
                            </td>

                            {{-- Phone --}}
                            <td class="py-3.5 px-4 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                @if($inquiry->phone)
                                    <a href="tel:{{ $inquiry->phone }}" class="hover:text-[#FF6B35] transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-[10px] text-gray-400"></i>
                                        <span>{{ $inquiry->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">N/A</span>
                                @endif
                            </td>

                            {{-- Subject --}}
                            <td class="py-3.5 px-4 text-xs font-medium text-gray-900 dark:text-white max-w-[150px] truncate">
                                {{ $inquiry->subject ?: 'No subject' }}
                            </td>

                            {{-- Assigned Agent --}}
                            <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                                @if(!$isAgentOnly && (auth()->user()?->hasRole(['Super Admin', 'Admin', 'Manager']) || auth()->user()?->can('manage_inquiries')))
                                    <select wire:change="assignAgent('{{ $inquiry->hashid }}', $event.target.value)"
                                            class="px-2 py-1 text-xs rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:outline-none transition">
                                        <option value="">-- Unassigned --</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" @selected($inquiry->assigned_agent_id === $agent->id)>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    @if($inquiry->assignedAgent)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-[#FF6B35]/10 text-[#FF6B35] border border-[#FF6B35]/20">
                                            <i class="fa-solid fa-user-tie text-[10px]"></i>
                                            {{ $inquiry->assignedAgent->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">Unassigned</span>
                                    @endif
                                @endif
                            </td>

                            {{-- Message (Truncated) --}}
                            <td class="py-3.5 px-4 text-xs text-gray-500 dark:text-gray-400 max-w-[220px]">
                                <div class="truncate cursor-pointer hover:text-gray-700 dark:hover:text-gray-300" wire:click="viewDetails('{{ $inquiry->hashid }}')" title="Click to view full message">
                                    {{ Str::limit($inquiry->message, 45) }}
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($inquiry->status === 'new')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        New
                                    </span>
                                @elseif($inquiry->status === 'replied')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Replied
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Closed
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- View Details --}}
                                    <button type="button"
                                            wire:click="viewDetails('{{ $inquiry->hashid }}')"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-500 hover:text-[#FF6B35] hover:bg-[#FF6B35]/10 dark:hover:bg-[#FF6B35]/20 transition"
                                            title="View Message">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                    {{-- Mark as Replied --}}
                                    @if($inquiry->status !== 'replied')
                                        <button type="button"
                                                wire:click="markAsReplied('{{ $inquiry->hashid }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition flex items-center gap-1"
                                                title="Mark as Replied">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Replied</span>
                                        </button>
                                    @endif

                                    {{-- Delete Button --}}
                                    @canany(['delete_inquiries', 'manage_inquiries'])
                                        <button type="button"
                                                wire:click="confirmDelete('{{ $inquiry->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                                title="Delete Inquiry">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $isAgentOnly ? 'No inquiries assigned to you yet' : 'No contact inquiries found' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $isAgentOnly ? 'Inquiries assigned to you by administrators or managers will appear here.' : 'When visitors reach out through the contact page, their messages appear here.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>

    {{-- View Message Details Modal --}}
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
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Inquiry Details</h3>
                        <p class="text-[11px] text-gray-400">Received {{ $selectedInquiry?->created_at?->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            @if($selectedInquiry)
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-gray-50/70 dark:bg-[#141414] border border-gray-100 dark:border-gray-800">
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Sender Name</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedInquiry->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Status</span>
                            <span class="capitalize font-semibold text-gray-900 dark:text-white">{{ $selectedInquiry->status }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Email</span>
                            <a href="mailto:{{ $selectedInquiry->email }}" class="text-[#FF6B35] hover:underline font-medium">{{ $selectedInquiry->email }}</a>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Phone</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $selectedInquiry->phone ?: 'None' }}</span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block">Assigned Agent</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedInquiry->assignedAgent?->name ?: 'Unassigned' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block mb-1">Subject</span>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#141414] font-medium text-gray-900 dark:text-white border border-gray-100 dark:border-gray-800">
                            {{ $selectedInquiry->subject ?: 'No subject specified' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold tracking-wider block mb-1">Message</span>
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-800 leading-relaxed whitespace-pre-line max-h-48 overflow-y-auto">
                            {{ $selectedInquiry->message }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        @if($selectedInquiry->status !== 'replied')
                            <button type="button" wire:click="markAsReplied('{{ $selectedInquiry->hashid }}')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>Mark as Replied</span>
                            </button>
                        @endif

                        @if($selectedInquiry->status !== 'closed')
                            <button type="button" wire:click="markAsClosed('{{ $selectedInquiry->hashid }}')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                <span>Close Ticket</span>
                            </button>
                        @else
                            <button type="button" wire:click="markAsNew('{{ $selectedInquiry->hashid }}')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500/30 transition">
                                <span>Reopen</span>
                            </button>
                        @endif
                    </div>

                    <button type="button" wire:click="closeDetailsModal"
                            class="px-4 py-1.5 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Close
                    </button>
                </div>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Inquiry</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to permanently delete this inquiry record?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">{{ $inquiryToDeleteName }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $inquiryToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteInquiry" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteInquiry">Yes, Delete Record</span>
                    <span wire:loading wire:target="deleteInquiry"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to view contact inquiries.</p>
</div>
@endcanany

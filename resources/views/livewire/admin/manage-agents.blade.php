@canany(['view_agents', 'manage_agents'])
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Agents &amp; Team</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Team Management
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Manage luxury real estate brokers, consultants, and property representatives.
            </p>
        </div>

        @canany(['create_agents', 'manage_agents'])
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.agents.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Add New Agent</span>
                </a>
            </div>
        @endcanany
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
                       placeholder="Search by agent name, email, phone, or title..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="activeFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="1">Active Only</option>
                    <option value="0">Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Agents Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4 w-16 text-center">Photo</th>
                        <th class="py-3.5 px-4">Agent Name</th>
                        <th class="py-3.5 px-4">Designation</th>
                        <th class="py-3.5 px-4">Phone</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Properties</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Photo Thumbnail --}}
                            <td class="py-3.5 px-4 text-center">
                                @if($agent->photo_path)
                                    <div class="w-11 h-11 mx-auto rounded-full overflow-hidden border-2 border-[#FF6B35]/20 shadow-sm flex-shrink-0">
                                        <img src="{{ asset('storage/' . $agent->photo_path) }}" alt="{{ $agent->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-11 h-11 mx-auto rounded-full bg-gradient-to-tr from-[#FF6B35] to-[#ff946d] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($agent->name, 0, 2)) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Name & Experience --}}
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-900 dark:text-white leading-snug">
                                    @canany(['edit_agents', 'manage_agents'])
                                        <a href="{{ route('admin.agents.edit', ['agentId' => $agent->hashid]) }}" wire:navigate class="hover:text-[#FF6B35] transition">
                                            {{ $agent->name }}
                                        </a>
                                    @else
                                        {{ $agent->name }}
                                    @endcanany
                                </div>
                                @if($agent->experience_years > 0)
                                    <span class="text-[11px] text-gray-400">
                                        {{ $agent->experience_years }} {{ Str::plural('year', $agent->experience_years) }} exp.
                                    </span>
                                @endif
                            </td>

                            {{-- Designation --}}
                            <td class="py-3.5 px-4 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800">
                                    {{ $agent->designation ?? 'Real Estate Agent' }}
                                </span>
                            </td>

                            {{-- Phone --}}
                            <td class="py-3.5 px-4 text-xs font-mono text-gray-600 dark:text-gray-400">
                                @if($agent->phone)
                                    <a href="tel:{{ $agent->phone }}" class="hover:text-[#FF6B35] transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-[10px] text-gray-400"></i>
                                        <span>{{ $agent->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Email --}}
                            <td class="py-3.5 px-4 text-xs text-gray-600 dark:text-gray-400">
                                <a href="mailto:{{ $agent->email }}" class="hover:text-[#FF6B35] transition flex items-center gap-1.5">
                                    <i class="fa-regular fa-envelope text-[10px] text-gray-400"></i>
                                    <span>{{ $agent->email }}</span>
                                </a>
                            </td>

                            {{-- Properties Count --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                    {{ $agent->properties_count }}
                                </span>
                            </td>

                            {{-- Active Toggle --}}
                            <td class="py-3.5 px-4 text-center">
                                @canany(['edit_agents', 'manage_agents'])
                                    <button type="button" wire:click="toggleActive('{{ $agent->hashid }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $agent->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 border border-gray-200 dark:border-gray-700' }}"
                                            title="Toggle active status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $agent->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $agent->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $agent->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $agent->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcanany
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @canany(['edit_agents', 'manage_agents'])
                                        <a href="{{ route('admin.agents.edit', ['agentId' => $agent->hashid]) }}" wire:navigate
                                           class="p-2 text-xs font-semibold rounded-lg text-gray-600 dark:text-gray-300 hover:text-[#FF6B35] dark:hover:text-[#FF6B35] hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                                           title="Edit Agent Profile">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcanany

                                    @canany(['delete_agents', 'manage_agents'])
                                        <button type="button" wire:click="confirmDelete('{{ $agent->hashid }}')"
                                                class="p-2 text-xs font-semibold rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition"
                                                title="Delete Agent">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-slash text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No agents registered</p>
                                <p class="text-xs text-gray-400 mt-0.5">Add team members to assign listings and handle incoming property visits.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agents->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $agents->links() }}
            </div>
        @endif
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Remove Agent</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to remove this team member?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">{{ $agentToDeleteName }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $agentToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteAgent" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteAgent">Yes, Remove Agent</span>
                    <span wire:loading wire:target="deleteAgent"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to manage team agents.</p>
</div>
@endcanany

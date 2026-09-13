@can('view_activity_logs')
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Activity Logs</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                System &amp; Audit Logs
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Comprehensive security audit trail tracking administrative actions, resource modifications, and user access.
            </p>
        </div>

        @if($search !== '' || $userFilter !== '' || $moduleFilter !== '')
            <div>
                <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Search Bar --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search by action, IP, or user..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            {{-- Filter by User --}}
            <div>
                <select wire:model.live="userFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Administrators &amp; Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter by Module --}}
            <div>
                <select wire:model.live="moduleFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}">{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Activity Logs Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4 w-44">Date / Time</th>
                        <th class="py-3.5 px-4 w-52">Administrator</th>
                        <th class="py-3.5 px-4">Action Details</th>
                        <th class="py-3.5 px-4 text-center w-36">Module</th>
                        <th class="py-3.5 px-4 text-right w-40">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Date / Time --}}
                            <td class="py-3 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-[#FF6B35] text-[11px]"></i>
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white block">{{ $log->created_at->format('M d, Y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('h:i:s A') }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- User --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-[#FF6B35] to-[#ff946d] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-xs text-gray-900 dark:text-white block truncate">{{ $log->user->name }}</span>
                                            <span class="text-[10px] text-gray-400 block truncate">{{ $log->user->email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <i class="fa-solid fa-robot text-xs"></i>
                                        <span class="text-xs italic">System / CLI</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="py-3 px-4 text-xs text-gray-800 dark:text-gray-200">
                                <div class="font-semibold leading-snug">{{ $log->action }}</div>
                                @if($log->user_agent)
                                    <span class="text-[10px] text-gray-400 block truncate max-w-lg mt-0.5" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent, 65) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Module Badge --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @php
                                    $mod = strtolower($log->module);
                                    $badgeStyle = match(true) {
                                        str_contains($mod, 'prop') => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                        str_contains($mod, 'agent') => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                        str_contains($mod, 'user') || str_contains($mod, 'role') => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                        str_contains($mod, 'inquir') || str_contains($mod, 'visit') => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                        str_contains($mod, 'setting') => 'bg-[#FF6B35]/10 text-[#FF6B35] border-[#FF6B35]/20',
                                        default => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                    {{ ucfirst($log->module) }}
                                </span>
                            </td>

                            {{-- IP Address --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap text-xs font-mono text-gray-500 dark:text-gray-400">
                                @if($log->ip_address)
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-[#141414] border border-gray-200/60 dark:border-gray-800">
                                        {{ $log->ip_address }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No activity logs recorded</p>
                                <p class="text-xs text-gray-400 mt-0.5">Admin operations and data alterations will be automatically tracked here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@else
<div class="p-8 text-center bg-white dark:bg-[#1A1A1A] rounded-2xl border border-rose-200 dark:border-rose-900">
    <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center text-xl mb-3">
        <i class="fa-solid fa-shield-xmark"></i>
    </div>
    <h3 class="text-base font-bold text-gray-900 dark:text-white">Access Denied</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have administrative permission to view system audit logs.</p>
</div>
@endcan

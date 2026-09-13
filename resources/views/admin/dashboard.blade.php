@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner with TISHA Brand Palette -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#1A1A1A] via-gray-900 to-[#1A1A1A] p-8 text-white shadow-xl border border-gray-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-[#FF6B35]/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FF6B35]/20 text-[#FF6B35] border border-[#FF6B35]/30 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] animate-pulse"></span>
                    TISHA REALTY HQ
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="mt-1 text-sm text-gray-400">
                    Here is what is happening across your luxury properties and listings today.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('admin.profile.2fa') }}" 
                    wire:navigate
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20' }}"
                >
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ auth()->user()->hasTwoFactorEnabled() ? '2FA Enabled' : 'Enable 2FA' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Properties -->
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Properties</span>
                <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center">
                    <i class="fa-solid fa-city text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">24</p>
            <p class="text-xs text-emerald-500 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +12% from last month
            </p>
        </div>

        <!-- Registered Agents -->
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Agents</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                    <i class="fa-solid fa-user-tie text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">8</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Licensed realtors</p>
        </div>

        <!-- Inquiries -->
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inquiries</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <i class="fa-regular fa-envelope text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">15</p>
            <p class="text-xs text-emerald-500 font-semibold mt-1 flex items-center gap-1">
                <i class="fa-solid fa-bell"></i> 4 new pending replies
            </p>
        </div>

        <!-- Visit Requests -->
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200 dark:border-gray-800/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Visit Requests</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i class="fa-regular fa-calendar-check text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-3">6</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Scheduled this week</p>
        </div>
    </div>
</div>
@endsection

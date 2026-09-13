@canany([$isEditing ? 'edit_agents' : 'create_agents', 'manage_agents'])
<div class="space-y-6">
    {{-- Top Action Header & Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.agents.index') }}" wire:navigate class="hover:text-[#FF6B35] transition">Agents</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">{{ $isEditing ? 'Edit Profile' : 'Add New Agent' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ $isEditing ? 'Edit: ' . $name : 'Register New Agent' }}
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.agents.index') }}" wire:navigate
               class="px-4 py-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Cancel
            </a>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                <span wire:loading wire:target="save">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span>{{ $isEditing ? 'Update Agent Profile' : 'Save &amp; Publish Agent' }}</span>
            </button>
        </div>
    </div>

    {{-- Flash Status --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Main Form Grid --}}
    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN (2 Spans: Profile Info & Social Links) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Personal & Professional Details --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Profile Details</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Agent credentials, contact points, and experience</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Full Name --}}
                        <div>
                            <label for="agent_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Full Name <span class="text-[#FF6B35]">*</span>
                            </label>
                            <input type="text" id="agent_name" wire:model="name"
                                   placeholder="e.g. Victoria Sterling"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Designation / Title --}}
                        <div>
                            <label for="agent_title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Professional Title
                            </label>
                            <input type="text" id="agent_title" wire:model="designation"
                                   placeholder="e.g. Senior Luxury Property Broker"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('designation') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Email --}}
                        <div class="sm:col-span-2">
                            <label for="agent_email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Official Email Address <span class="text-[#FF6B35]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-gray-400">
                                    <i class="fa-regular fa-envelope text-xs"></i>
                                </span>
                                <input type="email" id="agent_email" wire:model="email"
                                       placeholder="victoria@tishaproperty.com"
                                       class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            </div>
                            @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Experience Years --}}
                        <div>
                            <label for="agent_experience" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Experience (Years)
                            </label>
                            <input type="number" min="0" max="80" id="agent_experience" wire:model="experience_years"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('experience_years') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="agent_phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Direct Phone / WhatsApp Number
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-green-500">
                                <i class="fab fa-whatsapp text-sm"></i>
                            </span>
                            <input type="text" id="agent_phone" wire:model="phone"
                                   placeholder="919876543210"
                                   class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5 flex items-start gap-1">
                            <i class="fab fa-whatsapp text-green-500 shrink-0 mt-px"></i>
                            Enter WhatsApp number with country code for direct chat links &mdash;
                            e.g. <strong class="text-gray-600 dark:text-gray-300 font-mono">919876543210</strong>
                            (91 = India, followed by 10-digit mobile number).
                        </p>
                        @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>


                    {{-- Bio --}}
                    <div>
                        <label for="agent_bio" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Professional Biography
                        </label>
                        <textarea id="agent_bio" rows="4" wire:model="bio"
                                  placeholder="Provide a brief summary of the agent's background, track record in premier real estate, and client advisory expertise..."
                                  class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition"></textarea>
                        @error('bio') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- 2. Social Profiles Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-[#FF6B35]">
                            <i class="fa-solid fa-share-nodes text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Social &amp; Professional Links</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Direct links displayed on public agent profiles</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- LinkedIn --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-brands fa-linkedin text-blue-600 mr-1"></i> LinkedIn
                            </label>
                            <input type="url" wire:model="social_links.linkedin" placeholder="https://linkedin.com/in/username"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none">
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i> WhatsApp
                            </label>
                            <input type="text" wire:model="social_links.whatsapp" placeholder="https://wa.me/15551234567"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none">
                        </div>

                        {{-- Instagram --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-brands fa-instagram text-rose-500 mr-1"></i> Instagram
                            </label>
                            <input type="url" wire:model="social_links.instagram" placeholder="https://instagram.com/username"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none">
                        </div>

                        {{-- Twitter / X --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-brands fa-x-twitter text-gray-700 dark:text-gray-300 mr-1"></i> X (Twitter)
                            </label>
                            <input type="url" wire:model="social_links.twitter" placeholder="https://x.com/username"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none">
                        </div>

                        {{-- Facebook --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                <i class="fa-brands fa-facebook text-blue-700 mr-1"></i> Facebook
                            </label>
                            <input type="url" wire:model="social_links.facebook" placeholder="https://facebook.com/username"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none">
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN (1 Span: Profile Photo & Visibility) --}}
            <div class="space-y-6">

                {{-- Profile Photo Upload Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-800">
                        Profile Photo
                    </h2>

                    {{-- Photo Preview Box --}}
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414]">
                        @if ($photo)
                            {{-- Newly Selected Photo Preview --}}
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-[#FF6B35] shadow-lg">
                                    <img src="{{ $photo->temporaryUrl() }}" alt="New Photo Preview" class="w-full h-full object-cover">
                                </div>
                                <button type="button" wire:click="removeSelectedPhoto"
                                        class="absolute top-0 right-0 w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shadow hover:bg-rose-700 transition"
                                        title="Cancel photo">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                            <span class="text-xs font-semibold text-[#FF6B35] mt-3">Ready to upload</span>
                        @elseif ($existing_photo_path)
                            {{-- Existing Saved Photo --}}
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 dark:border-gray-700 shadow-lg">
                                    <img src="{{ asset('storage/' . $existing_photo_path) }}" alt="Agent Photo" class="w-full h-full object-cover">
                                </div>
                                <button type="button" wire:click="removeExistingPhoto"
                                        wire:confirm="Are you sure you want to remove this profile photo?"
                                        class="absolute top-0 right-0 w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shadow hover:bg-rose-700 transition"
                                        title="Delete photo">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                            <span class="text-xs text-gray-400 mt-3">Current Profile Photo</span>
                        @else
                            {{-- Placeholder --}}
                            <div class="w-32 h-32 rounded-full bg-orange-50 dark:bg-orange-950/30 text-[#FF6B35] flex items-center justify-center text-4xl border-2 border-[#FF6B35]/30">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <span class="text-xs text-gray-400 mt-3">No photo uploaded</span>
                        @endif

                        {{-- File Input --}}
                        <div class="mt-4 w-full">
                            <label class="block w-full py-2 px-3 text-center rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-[#FF6B35] hover:text-white dark:hover:bg-[#FF6B35] text-xs font-semibold text-gray-700 dark:text-gray-300 transition cursor-pointer">
                                <span><i class="fa-solid fa-camera mr-1"></i> Choose Image</span>
                                <input type="file" wire:model="photo" accept="image/*" class="sr-only">
                            </label>
                            <p class="text-[10px] text-gray-400 text-center mt-1">Square JPG/PNG up to 3MB</p>
                            @error('photo') <p class="text-rose-500 text-xs text-center mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Loading Indicator --}}
                        <div wire:loading wire:target="photo" class="mt-2 text-xs text-[#FF6B35] font-semibold">
                            <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Processing photo...
                        </div>
                    </div>
                </div>

                {{-- Visibility & Publishing Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-6 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-800">
                        Agent Status
                    </h2>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 block">Active Status</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Available to be assigned listings</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF6B35]"></div>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-3 px-4 rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white font-bold text-sm shadow-md shadow-[#FF6B35]/25 transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading wire:target="save">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </span>
                            <span>{{ $isEditing ? 'Update Agent Profile' : 'Publish Agent Profile' }}</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>
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

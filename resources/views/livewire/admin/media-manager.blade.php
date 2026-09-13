@can('manage_media')
<div class="space-y-6">
    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Media Manager</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Media &amp; Asset Library
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Browse, preview, organize, and manage uploaded property photos, agent portraits, and site assets.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="openUploadModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                <span>Upload Media</span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Storage Symlink Info Banner --}}
    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 flex items-start sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-circle-info text-amber-500 text-base flex-shrink-0"></i>
            <div>
                <span class="font-bold">Public Storage Tip:</span>
                <span>To ensure uploaded images are publicly accessible via browser URLs, make sure the symlink exists: <code class="font-mono bg-amber-500/20 px-1.5 py-0.5 rounded text-[11px] font-semibold text-amber-900 dark:text-amber-200">php artisan storage:link</code></span>
            </div>
        </div>
        <span class="font-mono text-[11px] font-bold text-amber-600 dark:text-amber-400 whitespace-nowrap">
            {{ $totalFiles }} Total {{ Str::plural('Asset', $totalFiles) }}
        </span>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search by file name or directory path..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            {{-- Folder Filter --}}
            <div>
                <select wire:model.live="folderFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="all">All Folders</option>
                    <option value="uploads">uploads/</option>
                    <option value="uploads/settings">uploads/settings/</option>
                    <option value="uploads/properties">uploads/properties/</option>
                    <option value="properties">properties/</option>
                    <option value="agents">agents/</option>
                    <option value="testimonials">testimonials/</option>
                    @foreach($folders as $folder)
                        @if(!in_array($folder, ['uploads', 'uploads/settings', 'uploads/properties', 'properties', 'agents', 'testimonials']))
                            <option value="{{ $folder }}">{{ $folder }}/</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- File Type Filter --}}
            <div>
                <select wire:model.live="typeFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="all">All File Types</option>
                    <option value="images">Images Only</option>
                    <option value="documents">Documents &amp; Others</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Media Assets Grid View --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm p-5">
        @if($mediaFiles->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($mediaFiles as $file)
                    <div class="group relative rounded-xl border border-gray-200/80 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414] hover:border-[#FF6B35]/50 transition-all duration-200 overflow-hidden flex flex-col justify-between">
                        {{-- Preview Box --}}
                        <div class="h-36 w-full bg-gray-100 dark:bg-[#1f1f1f] flex items-center justify-center relative overflow-hidden">
                            @if($file['is_image'])
                                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                {{-- Quick View overlay on hover --}}
                                <a href="{{ $file['url'] }}" target="_blank"
                                   class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white"
                                   title="Open full size in new tab">
                                    <i class="fa-solid fa-up-right-from-square text-base"></i>
                                </a>
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    @if($file['extension'] === 'pdf')
                                        <i class="fa-solid fa-file-pdf text-3xl text-rose-500"></i>
                                    @elseif(in_array($file['extension'], ['zip', 'rar', 'tar', 'gz']))
                                        <i class="fa-solid fa-file-zipper text-3xl text-amber-500"></i>
                                    @elseif(in_array($file['extension'], ['doc', 'docx']))
                                        <i class="fa-solid fa-file-word text-3xl text-blue-500"></i>
                                    @elseif(in_array($file['extension'], ['xls', 'xlsx', 'csv']))
                                        <i class="fa-solid fa-file-excel text-3xl text-emerald-500"></i>
                                    @else
                                        <i class="fa-solid fa-file text-3xl"></i>
                                    @endif
                                    <span class="text-[10px] uppercase font-bold tracking-wider mt-1.5">{{ $file['extension'] }}</span>
                                </div>
                            @endif

                            {{-- Folder Badge --}}
                            <span class="absolute top-2 left-2 text-[9px] font-bold px-1.5 py-0.5 rounded bg-black/60 text-white backdrop-blur-sm truncate max-w-[80%]">
                                {{ $file['folder'] }}
                            </span>
                        </div>

                        {{-- Metadata Details --}}
                        <div class="p-3 space-y-2 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate" title="{{ $file['name'] }}">
                                    {{ $file['name'] }}
                                </h4>
                                <div class="flex items-center justify-between text-[10px] text-gray-400 mt-1">
                                    <span>{{ $file['size'] }}</span>
                                    <span>{{ $file['last_modified'] }}</span>
                                </div>
                            </div>

                            {{-- Card Action Bar --}}
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                                {{-- Copy Public URL --}}
                                <button type="button"
                                        x-data="{ copied: false }"
                                        @click="
                                            navigator.clipboard.writeText('{{ $file['url'] }}');
                                            copied = true;
                                            setTimeout(() => copied = false, 2000);
                                        "
                                        class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 hover:text-[#FF6B35] transition flex items-center gap-1"
                                        title="Copy public URL to clipboard">
                                    <i class="fa-solid text-xs" :class="copied ? 'fa-check text-emerald-500' : 'fa-link'"></i>
                                    <span x-text="copied ? 'Copied!' : 'Copy URL'"></span>
                                </button>

                                {{-- Delete Button with TISHA Orange highlight --}}
                                <button type="button"
                                        wire:click="confirmDelete('{{ $file['path'] }}')"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-xs text-gray-400 hover:text-white hover:bg-[#FF6B35] transition"
                                        title="Delete File">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($mediaFiles->hasPages())
                <div class="pt-6 border-t border-gray-100 dark:border-gray-800 mt-6">
                    {{ $mediaFiles->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center text-gray-400 space-y-3">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-50 dark:bg-[#141414] border border-gray-200 dark:border-gray-800 flex items-center justify-center text-2xl text-gray-300 dark:text-gray-600">
                    <i class="fa-regular fa-images"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">No media assets found</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                        @if($search !== '' || $folderFilter !== 'all' || $typeFilter !== 'all')
                            No files matched your current search filters. Try clearing your search parameters.
                        @else
                            No uploaded files exist in <code class="font-mono text-[#FF6B35]">storage/app/public/uploads</code> yet. Upload files to get started.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- Upload Media Modal --}}
    <div x-data="{ show: @entangle('showUploadModal') }"
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
             @click.outside="$wire.closeUploadModal()">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Upload New Asset</h3>
                        <p class="text-[11px] text-gray-400">Save files into public storage.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit="uploadFile" class="space-y-4 text-xs">
                {{-- Destination Folder --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Destination Subdirectory</label>
                    <select wire:model="uploadFolder"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                        <option value="uploads">uploads/ (Default general uploads)</option>
                        <option value="uploads/properties">uploads/properties/</option>
                        <option value="uploads/agents">uploads/agents/</option>
                        <option value="uploads/settings">uploads/settings/</option>
                    </select>
                </div>

                {{-- File Input --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Select File <span class="text-rose-500">*</span></label>
                    <input type="file" wire:model="newFile"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FF6B35]/10 file:text-[#FF6B35] hover:file:bg-[#FF6B35]/20 cursor-pointer">
                    <span class="text-[10px] text-gray-400 mt-1 block">Max file size: 10MB (Images, PDFs, Documents).</span>
                    @error('newFile') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Uploading Progress --}}
                <div wire:loading wire:target="newFile" class="text-xs text-[#FF6B35] font-semibold">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Uploading and preparing file...
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeUploadModal"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="uploadFile">Upload Asset</span>
                        <span wire:loading wire:target="uploadFile"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...</span>
                    </button>
                </div>
            </form>
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
                <div class="w-12 h-12 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Media Asset</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to permanently delete this file from storage?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block truncate">{{ $fileToDeleteName }}</span>
                <span class="text-[11px] text-gray-400 font-mono block truncate mt-0.5">{{ $fileToDeletePath }}</span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deleteFile" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deleteFile">Yes, Delete Asset</span>
                    <span wire:loading wire:target="deleteFile"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have permission to browse or manage media assets.</p>
</div>
@endcan

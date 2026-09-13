@canany(['view_pages', 'manage_pages'])
<div class="space-y-6">
    {{-- TinyMCE CDN --}}
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-[#FF6B35] transition">Dashboard</a>
                <span>/</span>
                <span class="text-[#FF6B35] font-semibold">Custom Pages</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Dynamic Pages &amp; Legal Content
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Manage public company statements, advisory disclaimers, and legal documentation.
            </p>
        </div>

        @canany(['create_pages', 'manage_pages'])
            <div class="flex items-center gap-3">
                <button type="button" wire:click="openCreateModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF6B35] hover:bg-[#e05a2b] text-white text-sm font-semibold rounded-xl shadow-sm shadow-[#FF6B35]/25 transition-all duration-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Create New Page</span>
                </button>
            </div>
        @endcanany
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

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search pages by title, URL slug, or body content..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
            </div>

            <div>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="1">Published / Active Only</option>
                    <option value="0">Draft / Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Pages Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-[#141414]/60 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3.5 px-4">Page Title</th>
                        <th class="py-3.5 px-4">URL Slug</th>
                        <th class="py-3.5 px-4">Meta Title</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($pages as $page)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                            {{-- Title --}}
                            <td class="py-3.5 px-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-file-lines text-[#FF6B35]"></i>
                                    <span>{{ $page->title }}</span>
                                </div>
                            </td>

                            {{-- Slug --}}
                            <td class="py-3.5 px-4 text-xs font-mono text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    /{{ $page->slug }}
                                </span>
                            </td>

                            {{-- Meta Title --}}
                            <td class="py-3.5 px-4 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $page->meta_title ?: 'Not specified' }}
                            </td>

                            {{-- Status Badge / Toggle --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @canany(['edit_pages', 'manage_pages'])
                                    <button type="button" wire:click="toggleActive('{{ $page->hashid }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $page->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border border-gray-200 dark:border-gray-700 hover:bg-gray-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $page->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $page->is_active ? 'Active' : 'Inactive' }}</span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $page->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border border-gray-200 dark:border-gray-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $page->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $page->is_active ? 'Active' : 'Inactive' }}</span>
                                    </span>
                                @endcanany
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Edit Button --}}
                                    @canany(['edit_pages', 'manage_pages'])
                                        <button type="button"
                                                wire:click="openEditModal('{{ $page->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-500 hover:text-[#FF6B35] hover:bg-[#FF6B35]/10 dark:hover:bg-[#FF6B35]/20 transition"
                                                title="Edit Page">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endcanany

                                    {{-- Delete Button --}}
                                    @canany(['delete_pages', 'manage_pages'])
                                        <button type="button"
                                                wire:click="confirmDelete('{{ $page->hashid }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                                title="Delete Page">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <i class="fa-regular fa-file-excel text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No dynamic pages found</p>
                                <p class="text-xs text-gray-400 mt-0.5">Click "Create New Page" to publish legal or informational pages.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $pages->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Page Modal --}}
    <div x-data="{
            show: @entangle('showModal'),
            editorId: 'page-content-editor',
            content: @entangle('content'),
            initEditor() {
                let self = this;
                if (typeof tinymce === 'undefined') return;

                if (tinymce.get(this.editorId)) {
                    tinymce.get(this.editorId).remove();
                }

                let isDark = document.documentElement.classList.contains('dark');

                tinymce.init({
                    selector: '#' + this.editorId,
                    height: 320,
                    menubar: false,
                    branding: false,
                    promotion: false,
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code wordcount',
                    toolbar: 'undo redo | formatselect | bold italic underline backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                    setup: function (editor) {
                        editor.on('init', function () {
                            editor.setContent(self.content || '');
                        });
                        editor.on('change keyup input paste NodeChange', function () {
                            self.content = editor.getContent();
                        });
                    }
                });
            }
         }"
         x-show="show"
         x-cloak
         x-init="
            $watch('show', value => {
                if (value) {
                    $nextTick(() => { initEditor(); });
                }
            });
            window.addEventListener('set-editor-content', (event) => {
                let newContent = event.detail.content || '';
                $nextTick(() => {
                    if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                        tinymce.get(editorId).setContent(newContent);
                    }
                });
            });
         "
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-4xl w-full p-6 sm:p-8 border border-gray-200 dark:border-gray-800 shadow-2xl space-y-6 my-8 max-h-[90vh] overflow-y-auto"
             @click.outside="$wire.closeModal()">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FF6B35]/10 text-[#FF6B35] flex items-center justify-center text-lg">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $isEditing ? 'Edit Dynamic Page' : 'Create Dynamic Page' }}
                        </h3>
                        <p class="text-[11px] text-gray-400">Configure content, URL slug, and SEO search metadata.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Form Fields --}}
            <form wire:submit="save" class="space-y-5 text-xs">
                {{-- Title & Slug --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Page Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model.live.debounce.300ms="title"
                               placeholder="e.g. Advisory Disclaimer"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                        @error('title') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                            URL Slug <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">/</span>
                            <input type="text" wire:model="slug"
                                   placeholder="advisory-disclaimer"
                                   class="w-full pl-6 pr-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#141414] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition font-mono text-xs">
                        </div>
                        @error('slug') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- TinyMCE Rich Text Content --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Page Content (Rich Text) <span class="text-rose-500">*</span>
                    </label>
                    <div wire:ignore class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                        <textarea id="page-content-editor" wire:model="content" class="w-full h-80"></textarea>
                    </div>
                    @error('content') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- SEO Metadata --}}
                <div class="p-4 rounded-xl bg-gray-50/60 dark:bg-[#141414]/60 border border-gray-100 dark:border-gray-800 space-y-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#FF6B35] block">SEO Settings</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                                Meta Title
                            </label>
                            <input type="text" wire:model="meta_title"
                                   placeholder="e.g. Advisory Disclaimer - TISHA Real Estate"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('meta_title') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">
                                Meta Description
                            </label>
                            <input type="text" wire:model="meta_description"
                                   placeholder="Brief summary for search engine results..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] focus:outline-none transition">
                            @error('meta_description') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Active / Published Status Toggle --}}
                <div class="pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" wire:model="is_active"
                               class="w-4 h-4 text-[#FF6B35] rounded border-gray-300 dark:border-gray-700 focus:ring-[#FF6B35]">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Publish immediately (Active)</span>
                    </label>
                </div>

                {{-- Modal Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-[#FF6B35] hover:bg-[#e05a2b] text-white shadow-sm shadow-[#FF6B35]/25 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Update Page' : 'Publish Page' }}</span>
                        <span wire:loading wire:target="save"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...</span>
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
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">Delete Page</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Are you sure you want to permanently delete this dynamic page?</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#141414] border border-gray-100 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white block">{{ $pageToDeleteTitle }}</span>
                <span>Hashid: <code class="font-mono text-[#FF6B35]">{{ $pageToDeleteHashid }}</code></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="button" wire:click="deletePage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="deletePage">Yes, Delete Page</span>
                    <span wire:loading wire:target="deletePage"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...</span>
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
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You do not have administrative permission to view or manage dynamic pages.</p>
</div>
@endcanany

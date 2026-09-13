<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule as LivewireRule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Dynamic Pages Management - TISHA Real Estate')]
class ManagePages extends Component
{
    use WithPagination;

    // Search & Filter
    public string $search = '';
    public string $statusFilter = '';

    // Modal State
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $pageId = null;

    // Form Properties
    #[LivewireRule('required|string|max:255', as: 'page title')]
    public string $title = '';

    public string $slug = '';

    #[LivewireRule('required|string', as: 'page content')]
    public string $content = '';

    #[LivewireRule('nullable|string|max:255', as: 'meta title')]
    public string $meta_title = '';

    #[LivewireRule('nullable|string|max:500', as: 'meta description')]
    public string $meta_description = '';

    #[LivewireRule('boolean')]
    public bool $is_active = true;

    // Delete Confirmation Modal State
    public bool $showDeleteModal = false;
    public ?int $pageToDeleteId = null;
    public string $pageToDeleteTitle = '';
    public string $pageToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Auto-generate slug when title changes.
     */
    public function updatedTitle(string $value): void
    {
        if (!$this->isEditing || empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Ensure slug is URL-friendly when edited manually.
     */
    public function updatedSlug(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    /**
     * Open modal to create a new page.
     */
    public function openCreateModal(): void
    {
        abort_unless(
            auth()->user()?->can('create_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to create dynamic pages.'
        );

        $this->resetValidation();
        $this->reset(['title', 'slug', 'content', 'meta_title', 'meta_description', 'pageId']);
        $this->is_active = true;
        $this->isEditing = false;
        $this->showModal = true;

        $this->dispatch('set-editor-content', content: '');
    }

    /**
     * Open modal to edit an existing page.
     */
    public function openEditModal(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to edit dynamic pages.'
        );

        $this->resetValidation();
        $id = Page::decodeHashid($hashid);
        $page = Page::findOrFail($id);

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->meta_title = $page->meta_title ?? '';
        $this->meta_description = $page->meta_description ?? '';
        $this->is_active = (bool) $page->is_active;

        $this->isEditing = true;
        $this->showModal = true;

        $this->dispatch('set-editor-content', content: $this->content);
    }

    /**
     * Close modal and reset fields.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['title', 'slug', 'content', 'meta_title', 'meta_description', 'pageId']);
        $this->is_active = true;
        $this->isEditing = false;
    }

    /**
     * Sanitize HTML content to prevent XSS.
     */
    protected function sanitizeContent(string $html): string
    {
        // Remove dangerous script tags and inline on* event handlers
        $cleaned = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $cleaned = preg_replace('#\s*on\w+\s*=\s*(["\']).*?\1#is', '', $cleaned);
        $cleaned = preg_replace('#\s*on\w+\s*=[^>\s]+#is', '', $cleaned);
        $cleaned = preg_replace('#href=([\'"])javascript:.*?\1#is', 'href="#"', $cleaned);

        return trim($cleaned);
    }

    /**
     * Save page (Create or Update).
     */
    public function save(): void
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_pages') || auth()->user()?->can('manage_pages'),
                403,
                'Unauthorized. You do not have permission to edit dynamic pages.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_pages') || auth()->user()?->can('manage_pages'),
                403,
                'Unauthorized. You do not have permission to create dynamic pages.'
            );
        }

        // Generate slug if empty
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($this->pageId),
            ],
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $sanitizedContent = $this->sanitizeContent($this->content);

        if ($this->isEditing) {
            $page = Page::findOrFail($this->pageId);
            $page->update([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $sanitizedContent,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'is_active' => $this->is_active,
            ]);

            ActivityLog::record("Updated dynamic page '{$this->title}'", 'Pages', $page->id);
            session()->flash('status', "Page '{$this->title}' has been successfully updated.");
        } else {
            $page = Page::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $sanitizedContent,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'is_active' => $this->is_active,
            ]);

            ActivityLog::record("Created dynamic page '{$this->title}'", 'Pages', $page->id);
            session()->flash('status', "Page '{$this->title}' has been successfully created.");
        }

        $this->closeModal();
    }

    /**
     * Toggle active / published status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to edit pages.'
        );

        $id = Page::decodeHashid($hashid);
        $page = Page::findOrFail($id);

        $page->update([
            'is_active' => !$page->is_active,
        ]);

        $statusText = $page->is_active ? 'active' : 'inactive';
        ActivityLog::record("Changed page '{$page->title}' status to {$statusText}", 'Pages', $page->id);

        session()->flash('status', "Page '{$page->title}' is now {$statusText}.");
    }

    /**
     * Prompt single page delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to delete pages.'
        );

        $id = Page::decodeHashid($hashid);
        $page = Page::findOrFail($id);

        $this->pageToDeleteId = $page->id;
        $this->pageToDeleteTitle = $page->title;
        $this->pageToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->pageToDeleteId = null;
        $this->pageToDeleteTitle = '';
        $this->pageToDeleteHashid = '';
    }

    /**
     * Delete page after confirmation.
     */
    public function deletePage(): void
    {
        abort_unless(
            auth()->user()?->can('delete_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to delete pages.'
        );

        if (!$this->pageToDeleteId) {
            return;
        }

        $page = Page::findOrFail($this->pageToDeleteId);
        $title = $page->title;

        $page->delete();

        ActivityLog::record("Deleted dynamic page '{$title}'", 'Pages', null);

        $this->cancelDelete();
        session()->flash('status', "Page '{$title}' was successfully removed.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_pages') || auth()->user()?->can('manage_pages'),
            403,
            'Unauthorized. You do not have permission to view pages.'
        );

        $pages = Page::query()
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->statusFilter);
            })
            ->orderBy('title')
            ->paginate(10);

        return view('livewire.admin.manage-pages', [
            'pages' => $pages,
        ]);
    }
}

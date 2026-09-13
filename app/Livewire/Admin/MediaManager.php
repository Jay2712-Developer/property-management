<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Media Manager - TISHA Real Estate')]
class MediaManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public string $search = '';
    public string $folderFilter = 'all';
    public string $typeFilter = 'all';

    // Delete Modal State
    public bool $showDeleteModal = false;
    public string $fileToDeletePath = '';
    public string $fileToDeleteName = '';

    // Upload Modal State
    public bool $showUploadModal = false;
    public string $uploadFolder = 'uploads';

    #[Rule('required|file|max:10240', as: 'media file')]
    public $newFile = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFolderFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open upload modal.
     */
    public function openUploadModal(): void
    {
        abort_unless(
            auth()->user()?->can('manage_media'),
            403,
            'Unauthorized. You do not have permission to manage media files.'
        );

        $this->resetValidation();
        $this->newFile = null;
        $this->showUploadModal = true;
    }

    /**
     * Close upload modal.
     */
    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->resetValidation();
        $this->newFile = null;
    }

    /**
     * Upload a new media file into public storage.
     */
    public function uploadFile(): void
    {
        abort_unless(
            auth()->user()?->can('manage_media'),
            403,
            'Unauthorized. You do not have permission to upload media files.'
        );

        $this->validate();

        $targetFolder = trim($this->uploadFolder, '/');
        if (empty($targetFolder)) {
            $targetFolder = 'uploads';
        }

        $path = $this->newFile->store($targetFolder, 'public');
        $fileName = basename($path);

        ActivityLog::record("Uploaded media file '{$fileName}' to {$targetFolder}", 'Media', null);

        $this->closeUploadModal();
        session()->flash('status', "File '{$fileName}' was successfully uploaded.");
    }

    /**
     * Prompt delete confirmation modal.
     */
    public function confirmDelete(string $path): void
    {
        abort_unless(
            auth()->user()?->can('manage_media'),
            403,
            'Unauthorized. You do not have permission to delete media files.'
        );

        $this->fileToDeletePath = $path;
        $this->fileToDeleteName = basename($path);
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->fileToDeletePath = '';
        $this->fileToDeleteName = '';
    }

    /**
     * Permanently delete file from public storage.
     */
    public function deleteFile(): void
    {
        abort_unless(
            auth()->user()?->can('manage_media'),
            403,
            'Unauthorized. You do not have permission to delete media files.'
        );

        if (empty($this->fileToDeletePath)) {
            return;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($this->fileToDeletePath)) {
            $disk->delete($this->fileToDeletePath);
            ActivityLog::record("Deleted media file '{$this->fileToDeleteName}'", 'Media', null);
            session()->flash('status', "File '{$this->fileToDeleteName}' was permanently removed.");
        }

        $this->cancelDelete();
    }

    /**
     * Format bytes into readable string.
     */
    protected function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Scan storage disk and return files collection.
     */
    protected function getFilesCollection(): Collection
    {
        $disk = Storage::disk('public');

        // Ensure default uploads directory exists
        if (!$disk->exists('uploads')) {
            $disk->makeDirectory('uploads');
        }

        // 1. Scan storage/app/public/uploads and subdirectories
        $files = $disk->allFiles('uploads');

        // 2. Also include other standard public storage directories if present
        foreach (['properties', 'agents', 'testimonials', 'settings'] as $folder) {
            if ($disk->exists($folder)) {
                $files = array_merge($files, $disk->allFiles($folder));
            }
        }

        $files = array_unique($files);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp', 'ico'];

        $fileItems = collect();

        foreach ($files as $filePath) {
            // Ignore system hidden files
            if (str_starts_with(basename($filePath), '.')) {
                continue;
            }

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $isImage = in_array($ext, $imageExtensions);
            $fileName = basename($filePath);
            $folder = dirname($filePath);
            if ($folder === '.') {
                $folder = 'root';
            }

            $sizeBytes = $disk->exists($filePath) ? $disk->size($filePath) : 0;
            $lastModified = $disk->exists($filePath) ? $disk->lastModified($filePath) : time();

            // Filter: search keyword
            if ($this->search !== '') {
                $needle = strtolower($this->search);
                if (!str_contains(strtolower($fileName), $needle) && !str_contains(strtolower($folder), $needle)) {
                    continue;
                }
            }

            // Filter: folder
            if ($this->folderFilter !== 'all') {
                if (!str_starts_with($folder, $this->folderFilter)) {
                    continue;
                }
            }

            // Filter: file type
            if ($this->typeFilter === 'images' && !$isImage) {
                continue;
            } elseif ($this->typeFilter === 'documents' && $isImage) {
                continue;
            }

            $fileItems->push([
                'path' => $filePath,
                'name' => $fileName,
                'folder' => $folder,
                'extension' => $ext,
                'is_image' => $isImage,
                'size_bytes' => $sizeBytes,
                'size' => $this->formatSize($sizeBytes),
                'url' => asset('storage/' . $filePath),
                'last_modified' => Carbon::createFromTimestamp($lastModified)->format('M d, Y h:i A'),
                'timestamp' => $lastModified,
            ]);
        }

        return $fileItems->sortByDesc('timestamp')->values();
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('manage_media'),
            403,
            'Unauthorized. You do not have permission to view media files.'
        );

        $allFiles = $this->getFilesCollection();

        // Get distinct folders for dropdown
        $folders = $allFiles->pluck('folder')->unique()->sort()->values();

        // Manual pagination for collections
        $perPage = 18;
        $page = $this->getPage();
        $paginatedItems = new LengthAwarePaginator(
            $allFiles->forPage($page, $perPage),
            $allFiles->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.admin.media-manager', [
            'mediaFiles' => $paginatedItems,
            'totalFiles' => $allFiles->count(),
            'folders' => $folders,
        ]);
    }
}

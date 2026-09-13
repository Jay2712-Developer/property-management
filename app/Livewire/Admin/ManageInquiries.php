<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\ContactInquiry;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Contact Inquiries - TISHA Real Estate')]
class ManageInquiries extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $statusFilter = '';

    // Delete Modal State
    public bool $showDeleteModal = false;
    public ?int $inquiryToDeleteId = null;
    public string $inquiryToDeleteName = '';
    public string $inquiryToDeleteHashid = '';

    // Details Modal State
    public bool $showDetailsModal = false;
    public ?ContactInquiry $selectedInquiry = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * View full inquiry message details.
     */
    public function viewDetails(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('view_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to view inquiries.'
        );

        $id = ContactInquiry::decodeHashid($hashid);
        $this->selectedInquiry = ContactInquiry::findOrFail($id);
        $this->showDetailsModal = true;
    }

    /**
     * Close the inquiry detail modal.
     */
    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedInquiry = null;
    }

    /**
     * Mark inquiry as Replied.
     */
    public function markAsReplied(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('view_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to update inquiry status.'
        );

        $id = ContactInquiry::decodeHashid($hashid);
        $inquiry = ContactInquiry::findOrFail($id);

        $inquiry->update(['status' => 'replied']);

        ActivityLog::record("Marked inquiry from '{$inquiry->name}' as replied", 'Inquiries', $inquiry->id);

        if ($this->selectedInquiry && $this->selectedInquiry->id === $inquiry->id) {
            $this->selectedInquiry->refresh();
        }

        session()->flash('status', "Inquiry from '{$inquiry->name}' marked as replied.");
    }

    /**
     * Mark inquiry as Closed.
     */
    public function markAsClosed(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('view_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to update inquiry status.'
        );

        $id = ContactInquiry::decodeHashid($hashid);
        $inquiry = ContactInquiry::findOrFail($id);

        $inquiry->update(['status' => 'closed']);

        ActivityLog::record("Marked inquiry from '{$inquiry->name}' as closed", 'Inquiries', $inquiry->id);

        if ($this->selectedInquiry && $this->selectedInquiry->id === $inquiry->id) {
            $this->selectedInquiry->refresh();
        }

        session()->flash('status', "Inquiry from '{$inquiry->name}' marked as closed.");
    }

    /**
     * Re-open inquiry as New.
     */
    public function markAsNew(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('view_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to update inquiry status.'
        );

        $id = ContactInquiry::decodeHashid($hashid);
        $inquiry = ContactInquiry::findOrFail($id);

        $inquiry->update(['status' => 'new']);

        ActivityLog::record("Reopened inquiry from '{$inquiry->name}' as new", 'Inquiries', $inquiry->id);

        if ($this->selectedInquiry && $this->selectedInquiry->id === $inquiry->id) {
            $this->selectedInquiry->refresh();
        }

        session()->flash('status', "Inquiry from '{$inquiry->name}' status reset to new.");
    }

    /**
     * Prompt single inquiry delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to delete inquiries.'
        );

        $id = ContactInquiry::decodeHashid($hashid);
        $inquiry = ContactInquiry::findOrFail($id);

        $this->inquiryToDeleteId = $inquiry->id;
        $this->inquiryToDeleteName = $inquiry->name;
        $this->inquiryToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->inquiryToDeleteId = null;
        $this->inquiryToDeleteName = '';
        $this->inquiryToDeleteHashid = '';
    }

    /**
     * Delete inquiry after confirmation.
     */
    public function deleteInquiry(): void
    {
        abort_unless(
            auth()->user()?->can('delete_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to delete inquiries.'
        );

        if (!$this->inquiryToDeleteId) {
            return;
        }

        $inquiry = ContactInquiry::findOrFail($this->inquiryToDeleteId);
        $name = $inquiry->name;

        $inquiry->delete();

        ActivityLog::record("Deleted inquiry from '{$name}'", 'Inquiries', null);

        $this->cancelDelete();

        if ($this->selectedInquiry && $this->selectedInquiry->id === $inquiry->id) {
            $this->closeDetailsModal();
        }

        session()->flash('status', "Inquiry from '{$name}' was successfully deleted.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_inquiries') || auth()->user()?->can('manage_inquiries'),
            403,
            'Unauthorized. You do not have permission to view inquiries.'
        );

        $inquiries = ContactInquiry::query()
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.manage-inquiries', [
            'inquiries' => $inquiries,
        ]);
    }
}

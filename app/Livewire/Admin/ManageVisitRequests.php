<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Agent;
use App\Models\VisitRequest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Property Visit Requests - TISHA Real Estate')]
class ManageVisitRequests extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $statusFilter = '';

    // Agent scoping
    public ?int $agentId = null;

    // Delete Modal State
    public bool $showDeleteModal = false;
    public ?int $visitToDeleteId = null;
    public string $visitToDeleteClient = '';
    public string $visitToDeleteHashid = '';

    // Details Modal State
    public bool $showDetailsModal = false;
    public ?VisitRequest $selectedVisit = null;

    /**
     * Resolve the linked agent ID if authenticated user has role 'Agent'.
     */
    protected function getAgentId(): ?int
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Agent') && !$user->hasRole(['Super Admin', 'Admin', 'Manager'])) {
            $agent = $user->getLinkedAgent();
            return $agent ? $agent->id : -1;
        }
        return null;
    }

    public function mount(): void
    {
        $this->agentId = $this->getAgentId();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * View full visit request details.
     */
    public function viewDetails(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('view_visits') || auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to view visit requests.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::with(['property', 'agent'])->findOrFail($id);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have permission to view this visit request.');
        }

        $this->selectedVisit = $visit;
        $this->showDetailsModal = true;
    }

    /**
     * Close the visit details modal.
     */
    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedVisit = null;
    }

    /**
     * Approve the visit request.
     */
    public function approveVisit(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to manage visit requests.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::findOrFail($id);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have access to this visit request.');
        }

        $visit->update(['status' => 'approved']);

        ActivityLog::record("Approved visit request for client '{$visit->name}'", 'Visits', $visit->id);

        if ($this->selectedVisit && $this->selectedVisit->id === $visit->id) {
            $this->selectedVisit->refresh();
        }

        session()->flash('status', "Visit request for '{$visit->name}' has been approved.");
    }

    /**
     * Reject the visit request.
     */
    public function rejectVisit(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to manage visit requests.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::findOrFail($id);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have access to this visit request.');
        }

        $visit->update(['status' => 'rejected']);

        ActivityLog::record("Rejected visit request for client '{$visit->name}'", 'Visits', $visit->id);

        if ($this->selectedVisit && $this->selectedVisit->id === $visit->id) {
            $this->selectedVisit->refresh();
        }

        session()->flash('status', "Visit request for '{$visit->name}' has been rejected.");
    }

    /**
     * Revert visit request to pending.
     */
    public function markAsPending(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to manage visit requests.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::findOrFail($id);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have access to this visit request.');
        }

        $visit->update(['status' => 'pending']);

        ActivityLog::record("Reset visit request for client '{$visit->name}' to pending", 'Visits', $visit->id);

        if ($this->selectedVisit && $this->selectedVisit->id === $visit->id) {
            $this->selectedVisit->refresh();
        }

        session()->flash('status', "Visit request for '{$visit->name}' has been reset to pending.");
    }

    /**
     * Assign visit request to an agent (Admin/Manager only).
     */
    public function assignAgent(string $hashid, $agentId = null): void
    {
        $user = auth()->user();
        abort_unless(
            $user && ($user->hasRole(['Super Admin', 'Admin', 'Manager']) || $user->can('manage_visits')),
            403,
            'Unauthorized. You do not have permission to assign agents.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::findOrFail($id);

        $agentId = (!empty($agentId) && (int) $agentId > 0) ? (int) $agentId : null;

        $visit->update(['assigned_agent_id' => $agentId]);

        $agentName = $agentId ? (Agent::find($agentId)?->name ?? 'Agent') : 'Unassigned';
        ActivityLog::record("Assigned visit request for '{$visit->name}' to {$agentName}", 'Visits', $visit->id);

        if ($this->selectedVisit && $this->selectedVisit->id === $visit->id) {
            $this->selectedVisit->refresh();
        }

        session()->flash('status', "Visit request for '{$visit->name}' assigned to {$agentName}.");
    }

    /**
     * Prompt single visit delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to delete visit requests.'
        );

        $id = VisitRequest::decodeHashid($hashid);
        $visit = VisitRequest::findOrFail($id);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have access to this visit request.');
        }

        $this->visitToDeleteId = $visit->id;
        $this->visitToDeleteClient = $visit->name;
        $this->visitToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->visitToDeleteId = null;
        $this->visitToDeleteClient = '';
        $this->visitToDeleteHashid = '';
    }

    /**
     * Delete visit request after confirmation.
     */
    public function deleteVisit(): void
    {
        abort_unless(
            auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to delete visit requests.'
        );

        if (!$this->visitToDeleteId) {
            return;
        }

        $visit = VisitRequest::findOrFail($this->visitToDeleteId);

        $agentId = $this->getAgentId();
        if ($agentId !== null && $visit->assigned_agent_id !== $agentId) {
            abort(403, 'Unauthorized. You do not have access to this visit request.');
        }

        $clientName = $visit->name;
        $visit->delete();

        ActivityLog::record("Deleted visit request for '{$clientName}'", 'Visits', null);

        $this->cancelDelete();

        if ($this->selectedVisit && $this->selectedVisit->id === $visit->id) {
            $this->closeDetailsModal();
        }

        session()->flash('status', "Visit request for '{$clientName}' was successfully deleted.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_visits') || auth()->user()?->can('manage_visits'),
            403,
            'Unauthorized. You do not have permission to view visit requests.'
        );

        $agentId = $this->getAgentId();
        $this->agentId = $agentId;

        $visits = VisitRequest::query()
            ->with(['property', 'agent'])
            ->when($agentId !== null, function (Builder $query) use ($agentId) {
                $query->where('assigned_agent_id', $agentId);
            })
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('property', function (Builder $propQuery) use ($search) {
                            $propQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        $agents = ($agentId === null) ? Agent::active()->orderBy('name')->get() : collect();

        return view('livewire.admin.manage-visit-requests', [
            'visits' => $visits,
            'agents' => $agents,
            'isAgentOnly' => ($agentId !== null),
        ]);
    }
}

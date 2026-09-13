<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Agent & Team Management - TISHA Real Estate')]
class ManageAgents extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $activeFilter = '';

    // Delete Modal State
    public bool $showDeleteModal = false;
    public ?int $agentToDeleteId = null;
    public string $agentToDeleteName = '';
    public string $agentToDeleteHashid = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle agent active status.
     */
    public function toggleActive(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('edit_agents') || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized. You do not have permission to edit agents.'
        );

        $id = Agent::decodeHashid($hashid);
        $agent = Agent::findOrFail($id);

        $agent->update([
            'is_active' => !$agent->is_active,
        ]);

        $statusText = $agent->is_active ? 'Active' : 'Inactive';
        ActivityLog::record("Agent '{$agent->name}' status changed to {$statusText}", 'Agents', $agent->id);

        session()->flash('status', "Agent '{$agent->name}' is now {$statusText}.");
    }

    /**
     * Prompt single agent delete confirmation modal.
     */
    public function confirmDelete(string $hashid): void
    {
        abort_unless(
            auth()->user()?->can('delete_agents') || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized. You do not have permission to delete agents.'
        );

        $id = Agent::decodeHashid($hashid);
        $agent = Agent::findOrFail($id);

        $this->agentToDeleteId = $agent->id;
        $this->agentToDeleteName = $agent->name;
        $this->agentToDeleteHashid = $hashid;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deletion.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->agentToDeleteId = null;
        $this->agentToDeleteName = '';
        $this->agentToDeleteHashid = '';
    }

    /**
     * Delete agent after confirmation.
     */
    public function deleteAgent(): void
    {
        abort_unless(
            auth()->user()?->can('delete_agents') || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized. You do not have permission to delete agents.'
        );

        if (!$this->agentToDeleteId) {
            return;
        }

        $agent = Agent::findOrFail($this->agentToDeleteId);
        $name = $agent->name;

        // Clean up profile photo from storage if stored locally
        if ($agent->photo_path && Storage::disk('public')->exists($agent->photo_path)) {
            Storage::disk('public')->delete($agent->photo_path);
        }

        $agent->delete();

        ActivityLog::record("Deleted agent '{$name}'", 'Agents', null);

        $this->cancelDelete();
        session()->flash('status', "Agent '{$name}' was successfully removed.");
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_agents') || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized. You do not have permission to view agents.'
        );

        $agents = Agent::query()
            ->withCount('properties')
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($this->activeFilter !== '', function (Builder $query) {
                $query->where('is_active', (bool) $this->activeFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.manage-agents', [
            'agents' => $agents,
        ]);
    }
}

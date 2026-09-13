<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Activity & Audit Logs - TISHA Real Estate')]
class ManageActivityLogs extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $userFilter = '';
    public string $moduleFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModuleFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Reset all active filters.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'userFilter', 'moduleFilter']);
        $this->resetPage();
    }

    public function render()
    {
        abort_unless(
            auth()->user()?->can('view_activity_logs'),
            403,
            'Unauthorized. You do not have permission to view activity logs.'
        );

        $logs = ActivityLog::query()
            ->with('user')
            ->when(trim($this->search), function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->userFilter !== '', function (Builder $query) {
                $query->where('user_id', $this->userFilter);
            })
            ->when($this->moduleFilter !== '', function (Builder $query) {
                $query->where('module', $this->moduleFilter);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        // Fetch distinct modules and users for filter dropdowns
        $modules = ActivityLog::query()->select('module')->distinct()->orderBy('module')->pluck('module');
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('livewire.admin.manage-activity-logs', [
            'logs' => $logs,
            'modules' => $modules,
            'users' => $users,
        ]);
    }
}

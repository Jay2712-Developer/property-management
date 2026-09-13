<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\ContactInquiry;
use App\Models\Property;
use App\Models\VisitRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Admin Dashboard - TISHA Real Estate')]
class Dashboard extends Component
{
    /**
     * Render the admin dashboard with stats, chart data, recent activities and inquiries.
     */
    public function render()
    {
        // 1. Stats Cards Data
        $totalProperties = Property::count();
        $activeListings = Property::where('is_active', true)->count();
        $pendingInquiries = ContactInquiry::where(function ($query) {
            $query->where('status', 'new')->orWhere('status', 'pending');
        })->count();
        $scheduledVisits = VisitRequest::whereIn('status', ['pending', 'approved', 'scheduled'])->count();

        // 2. Monthly Properties Added Chart Data (Current Year)
        $currentYear = (int) date('Y');
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyCounts = array_fill(0, 12, 0);

        // Fetch properties created in current year in a database-agnostic manner
        Property::whereYear('created_at', $currentYear)
            ->pluck('created_at')
            ->each(function ($date) use (&$monthlyCounts) {
                if ($date) {
                    $monthNum = (int) $date->format('n'); // 1-12
                    $monthlyCounts[$monthNum - 1]++;
                }
            });

        // 3. Recent Activities (Last 5-10 actions)
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // 4. Recent Unread Inquiries (Last 3)
        $recentInquiries = ContactInquiry::where('status', 'new')
            ->latest()
            ->take(3)
            ->get();

        // If no strictly 'new' inquiries, fallback to latest 3
        if ($recentInquiries->isEmpty()) {
            $recentInquiries = ContactInquiry::latest()
                ->take(3)
                ->get();
        }

        return view('livewire.admin.dashboard', [
            'totalProperties' => $totalProperties,
            'activeListings' => $activeListings,
            'pendingInquiries' => $pendingInquiries,
            'scheduledVisits' => $scheduledVisits,
            'currentYear' => $currentYear,
            'monthLabels' => $monthLabels,
            'monthlyCounts' => $monthlyCounts,
            'recentActivities' => $recentActivities,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}

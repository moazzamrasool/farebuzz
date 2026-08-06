<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\LeadActivity;
use App\Models\LeadFollowUp;
use App\Models\PackageEnquiry;
use Illuminate\Support\Facades\Auth;

class CrmDashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $statusCounts = PackageEnquiry::query()
            ->visibleTo($admin)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusBreakdown = collect(LeadStatus::cases())->map(fn (LeadStatus $status) => [
            'status' => $status,
            'count' => (int) ($statusCounts[$status->value] ?? 0),
        ]);

        $totalLeads = $statusBreakdown->sum('count');
        $convertedCount = (int) ($statusCounts[LeadStatus::Converted->value] ?? 0);
        $lostCount = (int) ($statusCounts[LeadStatus::Lost->value] ?? 0) + (int) ($statusCounts[LeadStatus::Junk->value] ?? 0);

        $followUpsDueToday = LeadFollowUp::pendingFor($admin)->dueToday()->count();
        $followUpsOverdue = LeadFollowUp::pendingFor($admin)->overdue()->count();

        $recentActivity = LeadActivity::with(['leadable', 'admin'])
            ->when($admin->isUser(), function ($query) use ($admin) {
                $query->whereHasMorph('leadable', [PackageEnquiry::class], function ($q) use ($admin) {
                    $q->where('assigned_admin_id', $admin->id);
                });
            })
            ->latest()
            ->take(10)
            ->get();

        return view('admin.crm-dashboard', compact(
            'statusBreakdown',
            'totalLeads',
            'convertedCount',
            'lostCount',
            'followUpsDueToday',
            'followUpsOverdue',
            'recentActivity'
        ));
    }
}

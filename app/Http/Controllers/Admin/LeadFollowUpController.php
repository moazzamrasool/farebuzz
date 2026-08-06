<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Models\LeadFollowUp;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadFollowUpController extends Controller
{
    use AuthorizesLeadAccess;

    public function store(Request $request, PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'due_at' => 'required|date',
            'note' => 'nullable|string',
            'assigned_admin_id' => 'nullable|exists:admins,id',
        ]);

        $assignedAdminId = $data['assigned_admin_id'] ?? $packageEnquiry->assigned_admin_id;

        $followUp = $packageEnquiry->followUps()->create([
            'unique_id' => $packageEnquiry->unique_id,
            'admin_id' => Auth::guard('admin')->id(),
            'assigned_admin_id' => $assignedAdminId,
            'due_at' => $data['due_at'],
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        $packageEnquiry->forceFill(['next_follow_up_at' => $followUp->due_at])->save();

        $packageEnquiry->logActivity(
            'follow_up_scheduled',
            'Follow-up scheduled for '.$followUp->due_at->format('d M Y, h:i A').($data['note'] ? ": {$data['note']}" : ''),
            ['follow_up_id' => $followUp->id, 'due_at' => $followUp->due_at->toDateTimeString()]
        );

        return back()->with('success', 'Follow-up scheduled.');
    }

    public function complete(PackageEnquiry $packageEnquiry, LeadFollowUp $followUp)
    {
        $this->authorizeVisibility($packageEnquiry);

        abort_unless($followUp->leadable_type === PackageEnquiry::class && $followUp->leadable_id === $packageEnquiry->id, 404);

        $followUp->complete();

        // Recompute the lead's next-follow-up pointer from any remaining pending follow-ups.
        $next = $packageEnquiry->followUps()->where('status', 'pending')->orderBy('due_at')->first();
        $packageEnquiry->forceFill(['next_follow_up_at' => $next?->due_at])->save();

        return back()->with('success', 'Follow-up marked complete.');
    }

    public function dueToday()
    {
        $admin = Auth::guard('admin')->user();

        $followUps = LeadFollowUp::with('leadable', 'assignedAdmin')
            ->pendingFor($admin)
            ->where('due_at', '<=', now()->endOfDay())
            ->orderBy('due_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.follow-ups.due', compact('followUps'));
    }
}

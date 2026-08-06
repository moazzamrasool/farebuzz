<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;

// Handles the "Add Note" and "Log a Call" quick actions on a lead's detail page.
// Both just write a LeadActivity row via PackageEnquiry::logActivity(); the only
// difference is that a call also carries a structured "outcome" in meta.
class LeadActivityController extends Controller
{
    use AuthorizesLeadAccess;

    public function store(Request $request, PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'type' => 'required|in:note,call',
            'channel' => 'nullable|in:call,meeting,email,whatsapp,general',
            'note' => 'required|string',
            'outcome' => 'required_if:type,call|nullable|in:connected,no_answer,busy,callback',
        ]);

        if ($data['type'] === 'call') {
            $outcomeLabel = ucwords(str_replace('_', ' ', $data['outcome']));
            $packageEnquiry->logActivity(
                'call',
                "Call logged ({$outcomeLabel}): {$data['note']}",
                ['outcome' => $data['outcome']],
                'call'
            );
        } else {
            $packageEnquiry->logActivity(
                'note',
                $data['note'],
                null,
                $data['channel'] ?? 'general'
            );
        }

        return back()->with('success', 'Activity logged.');
    }
}

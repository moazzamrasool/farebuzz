<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;

class LeadAssignmentController extends Controller
{
    use AuthorizesLeadAccess;

    public function update(Request $request, PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'assigned_admin_id' => 'nullable|exists:admins,id',
        ]);

        // Admin::whereIn already scoped to the current tenant by TenantScope, so this
        // also guarantees the target agent belongs to the same company as the lead.
        $admin = $data['assigned_admin_id']
            ? Admin::whereIn('tier', ['admin', 'user'])->findOrFail($data['assigned_admin_id'])
            : null;

        $packageEnquiry->assignTo($admin);

        return back()->with('success', $admin ? "Lead assigned to {$admin->name}." : 'Lead unassigned.');
    }
}

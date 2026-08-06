<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\PackageEnquiry;
use Illuminate\Support\Facades\Auth;

// Shared by every controller that acts on a single lead (notes, follow-ups,
// assignment, WhatsApp, email) so a tier=user agent can't bypass row-level
// visibility by posting directly to a lead ID they can't see in the list.
trait AuthorizesLeadAccess
{
    private function authorizeVisibility(PackageEnquiry $packageEnquiry): void
    {
        $admin = Auth::guard('admin')->user();

        abort_unless(
            ! $admin->isUser() || $packageEnquiry->assigned_admin_id === $admin->id,
            404
        );
    }
}

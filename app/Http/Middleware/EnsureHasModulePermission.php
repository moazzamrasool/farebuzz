<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasModulePermission
{
    /**
     * Gate every module route (holiday-packages, hotels, destinations, roles/users,
     * ...). An Admin (company owner) has implicit full access to everything within
     * their own company — they own the tenant, so requiring them to hold a Spatie
     * permission for their own company would be a bootstrap problem (nobody could
     * grant them one). Sub-admins (tier=user) are governed strictly by whatever
     * permission their assigned role grants. Super Admin bypasses this gate
     * entirely, same as it already bypasses TenantScope and the unique_id-stamping
     * BelongsToTenant hook — it operates across every tenant, so a per-tenant
     * Spatie permission is meaningless for it.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->isAdmin() || $admin->isSuperAdmin()) {
            return $next($request);
        }

        if (!$admin->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}

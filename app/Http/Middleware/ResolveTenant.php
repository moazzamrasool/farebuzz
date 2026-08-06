<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Resolve the current tenant (unique_id) from the authenticated admin and set it
     * as Spatie's active team, so every role/permission check on the request is
     * automatically scoped to that company. Must run after admin.auth.
     *
     * IMPORTANT: passing null as the team id does NOT mean "see every tenant" — Spatie
     * translates it into `unique_id IS NULL`, i.e. genuinely global/unscoped roles.
     * Super Admin's cross-tenant visibility comes from TenantScope's bypass on plain
     * Eloquent queries, not from this team-id mechanism.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        Auth::shouldUse('admin');

        $teamId = $admin->isSuperAdmin() ? null : $admin->unique_id;

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);
        View::share('currentTenantId', $teamId);

        return $next($request);
    }
}

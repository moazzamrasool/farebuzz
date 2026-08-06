<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    /**
     * Gate Super-Admin-only routes (Companies/Admins CRUD). Deliberately independent
     * of Spatie roles/permissions — Super Admin authority is a structural property
     * of the account (tier), not a toggleable permission.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->isSuperAdmin()) {
            abort(403, 'This action is restricted to Super Admins.');
        }

        return $next($request);
    }
}

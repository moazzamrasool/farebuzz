<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBelongsToCompany
{
    /**
     * Gate per-company data (Master Data: Travel Categories, Destinations, Holiday
     * Packages, Activities) to Admin/User tiers only. Super Admin has no unique_id
     * and manages Companies, not any single company's catalog.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->user()?->isSuperAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}

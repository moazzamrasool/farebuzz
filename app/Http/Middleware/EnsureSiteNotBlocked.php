<?php

namespace App\Http\Middleware;

use App\Models\Admin\Admin;
use App\Support\SiteTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteNotBlocked
{
    /**
     * Gate the public frontend (site.blocked alias) for a company the Super Admin has
     * blocked. Resolves the tenant from config (SiteTenant::id()), same as every other
     * frontend query — there is no admin session on these routes to resolve it from.
     *
     * Responds directly with the down page instead of redirecting, so there is no
     * separate "suspended notice" route to exempt and no redirect-loop risk.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Admin::isUniqueIdBlocked(SiteTenant::id())) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'This site is temporarily unavailable.',
            ], 503);
        }

        return response()->view('errors.site-unavailable', [], 503);
    }
}

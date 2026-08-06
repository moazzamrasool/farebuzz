<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('crm.login');
        }

        $admin = Auth::guard('admin')->user();

        // Re-checked on every request (not just at login) so a company suspended by
        // Super Admin mid-session locks its staff out immediately.
        if ($admin->status !== 'active' || $admin->isCompanySuspended()) {
            $message = $admin->suspensionMessage();

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('crm.login')->with('error', $message);
        }

        return $next($request);
    }
}

<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminRedirect;
use App\Http\Middleware\EnsureBelongsToCompany;
use App\Http\Middleware\EnsureHasModulePermission;
use App\Http\Middleware\EnsureIsSuperAdmin;
use App\Http\Middleware\EnsureSiteNotBlocked;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Main public/frontend routes
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Modular routes — loaded AFTER web.php so /{slug} catch-all stays last
        then: function () {
            // CRM (/crm) is NOT gated by 'site.blocked' — it resolves its tenant from the
            // authenticated admin, not from config, and is already enforced per-request by
            // AdminAuthenticate + Admin::isCompanySuspended() (see routes/admin.php).
            Route::middleware('web')->group(base_path('routes/admin.php'));
            // User auth/dashboard is part of the public site — same config-resolved tenant
            // as routes/web.php, so it goes down with the rest of the site when blocked.
            Route::middleware(['web', 'site.blocked'])->group(base_path('routes/user.php'));
            // 'web' group like the other route files; CSRF is exempted below (Meta calls
            // this directly with no session/CSRF token of ours).
            Route::middleware('web')->group(base_path('routes/whatsapp.php'));
            // Catch-all for dynamic pages — must be absolute last route
            Route::middleware(['web', 'site.blocked'])
                ->get('/{slug}', [\App\Http\Controllers\DashboardController::class, 'pages'])
                ->name('pages');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Trust the reverse proxy / load balancer in front of the app (Cloudflare,
        // Nginx, the host's own LB, etc) so Request::isSecure()/url()/route() reflect
        // the ORIGINAL scheme the visitor used, not the http:// the proxy talks to us
        // over internally. Without this, URL::forceScheme('https') below and PayU's
        // surl/furl would still render as http:// behind most managed hosting setups,
        // and PayU rejects a non-HTTPS callback URL outright.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO);

        // Custom middleware aliases
        $middleware->alias([
            'admin.guest'      => AdminRedirect::class,
            'admin.auth'       => AdminAuthenticate::class,
            'tenant.resolve'   => ResolveTenant::class,
            'admin.super'      => EnsureIsSuperAdmin::class,
            'admin.permission' => EnsureHasModulePermission::class,
            'admin.company'    => EnsureBelongsToCompany::class,
            'site.blocked'     => EnsureSiteNotBlocked::class,
        ]);

        // Laravel's built-in 'auth' middleware (routes/user.php's customer dashboard
        // group) implements AuthenticatesRequests and is priority-sorted ahead of plain
        // route middleware regardless of registration order — without this, an
        // unauthenticated visit to a blocked site's /dashboard would hit 'auth' first
        // and redirect to /login instead of showing the 503 page directly. Force
        // site.blocked to run before it (still after StartSession/EncryptCookies, so
        // session/cookie handling on the way out is unaffected).
        $middleware->prependToPriorityList(
            before: \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            prepend: EnsureSiteNotBlocked::class,
        );

        // Default redirect paths for built-in guest/auth middleware (web guard / users)
        $middleware->redirectTo(
            guests: '/login',      // unauthenticated users → user login
            users:  '/dashboard',  // authenticated users on guest pages → user dashboard
        );

        // PayU posts these server-to-server with no session/CSRF token of ours — the
        // response hash (verified in PayUService::verifyResponse) is what authenticates
        // the callback instead, so these two routes are deliberately CSRF-exempt.
        $middleware->validateCsrfTokens(except: [
            'payu/callback/success',
            'payu/callback/failure',
            // Meta posts these server-to-server with no session/CSRF token of ours — the
            // X-Hub-Signature-256 HMAC (verified in WhatsAppWebhookController) authenticates
            // the callback instead.
            'webhook/whatsapp',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

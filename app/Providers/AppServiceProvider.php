<?php

namespace App\Providers;

use App\Contracts\WhatsAppSenderInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WhatsAppSenderInterface::class, config('services.whatsapp.driver'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force https:// on every generated URL (route(), url(), asset(), and — critically
        // — the PayU surl/furl callback URLs PayUService::initiate() builds) whenever
        // APP_URL itself is https. PayU rejects non-HTTPS callback URLs outright, and
        // without this a request that somehow reaches the app over plain HTTP (or a
        // misconfigured proxy that doesn't forward the original scheme) would silently
        // downgrade every link the app generates. Harmless locally, where APP_URL is
        // http://localhost.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // The whole site is Bootstrap 5 (no Tailwind), but Laravel's default
        // paginator view is Tailwind-based — without this, ->links() renders
        // unstyled boxes on every listing page (hotels, activities, packages,
        // destinations, admin lists, etc).
        Paginator::useBootstrapFive();

        View::composer('layouts.header', function ($view) {
            $view->with('pages', DB::table('pages')->get());
        });

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('user.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}

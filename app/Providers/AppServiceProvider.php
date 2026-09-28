<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS URL generation for assets/routes. We can't reliably
        // depend on per-request scheme detection here: Octane keeps the
        // app booted across requests for performance, so Laravel's URL
        // generator can cache whatever scheme the *first* request had and
        // reuse it for every request afterward, regardless of trustProxies
        // configuration. Since this app is always served over HTTPS in
        // production, force it explicitly instead.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}

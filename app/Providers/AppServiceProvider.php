<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Railway / Render / Heroku / any PaaS sits behind an HTTPS reverse proxy.
     * We must trust all proxies so that:
     *   - Request::secure() returns true
     *   - route() / url() generate https:// links
     *   - File download URLs are HTTPS → no "not secure" / mixed-content errors
     */
    public function boot(): void
    {
        // Read proxy trust from env — '*' means trust all (standard for PaaS)
        $proxies = env('TRUSTED_PROXIES', '*');
        if ($proxies === '*') {
            $proxies = '*';
        } else {
            $proxies = array_filter(array_map('trim', explode(',', $proxies)));
        }

        Request::setTrustedProxies(
            is_array($proxies) ? $proxies : ['*'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Always generate https:// URLs in production
        // This prevents "not secure" / mixed-content warnings on every browser
        if (app()->environment('production') || str_starts_with(config('app.url', ''), 'https')) {
            URL::forceScheme('https');
        }
    }
}


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
     * Railway (and most PaaS platforms) sit behind an HTTPS reverse proxy.
     * We must trust all proxies so that:
     *   - Request::secure() returns true
     *   - route() / url() generate https:// links
     *   - The file download URL is HTTPS → no "can't be downloaded securely" error
     */
    public function boot(): void
    {
        // Trust every upstream proxy (Railway load-balancer terminates TLS)
        Request::setTrustedProxies(
            ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'REMOTE_ADDR'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // When running in production (Railway), always generate https:// URLs
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}


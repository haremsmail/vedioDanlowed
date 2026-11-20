<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Render / Railway / Koyeb sit behind an HTTPS reverse proxy.
        $proxies = env('TRUSTED_PROXIES', '*');
        Request::setTrustedProxies(
            $proxies === '*' ? ['0.0.0.0/0', '::/0'] : array_filter(array_map('trim', explode(',', $proxies))),
            Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO
        );

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $tooMany = fn () => response()->json([
            'success' => false,
            'message' => __('messages.rate_limit_exceeded'),
        ], 429);

        foreach (['analyze', 'download', 'status'] as $name) {
            RateLimiter::for($name, fn (Request $request) => Limit::perMinute((int) config("ratelimit.$name", 30))
                ->by($name . '|' . $request->ip())
                ->response($tooMany));
        }
    }
}

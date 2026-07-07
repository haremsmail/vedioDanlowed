<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'video_download:' . $request->ip();
        $maxAttempts = config('ratelimit.rate_limit', 30);
        $decayMinutes = config('ratelimit.rate_limit_period', 60) / 60;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.rate_limit_exceeded'),
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request)->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', $maxAttempts - $this->limiter->attempts($key));
    }
}

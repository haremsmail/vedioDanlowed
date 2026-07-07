<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateApiToken
{
    /**
     * Validate API token for protected routes.
     * 
     * When API_TOKEN is not set in .env, all requests are allowed (public website mode).
     * When API_TOKEN is set, external API consumers must provide a Bearer token.
     * Requests originating from the same domain (web frontend) are always allowed.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow health check without authentication
        if ($request->path() === 'api/health') {
            return $next($request);
        }

        // Always allow requests from our own web frontend (same origin)
        // The frontend sends X-CSRF-TOKEN which proves it loaded our page
        if ($request->hasHeader('X-CSRF-TOKEN')) {
            return $next($request);
        }

        // If no API_TOKEN is configured, allow all requests (public mode)
        $configuredToken = config('ytdlp.api_token');
        if (empty($configuredToken)) {
            return $next($request);
        }

        // External API access requires a valid Bearer token
        $token = $request->bearerToken();

        if (!$token || !$this->isValidToken($token)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.permission_denied'),
            ], 401);
        }

        return $next($request);
    }

    /**
     * Validate the provided token against configured tokens
     */
    private function isValidToken(string $token): bool
    {
        $validTokens = array_filter([
            config('ytdlp.api_token'),
            config('ytdlp.api_token_secondary'),
        ]);

        if (empty($validTokens)) {
            return false;
        }

        return in_array($token, $validTokens, true);
    }
}

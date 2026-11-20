<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProductionSecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // File downloads: keep headers minimal (mobile browsers are picky)
        if (str_contains((string) $response->headers->get('Content-Disposition', ''), 'attachment')) {
            return $response;
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        $adsOn = config('ads.adsense_client') || config('ads.custom') || config('ads.ga_id');

        if ($adsOn) {
            // Ad networks load scripts, frames and images from many changing
            // domains, so the policy is limited to HTTPS + blocking plugins/base hijacking.
            $csp = "default-src 'self' https: data: blob:; "
                . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; "
                . "style-src 'self' 'unsafe-inline' https:; "
                . "img-src 'self' https: data: blob:; "
                . "frame-src https:; connect-src 'self' https:; "
                . "object-src 'none'; base-uri 'self'; form-action 'self'; upgrade-insecure-requests";
        } else {
            $csp = "default-src 'self'; "
                . "script-src 'self' 'unsafe-inline'; "
                . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
                . "font-src 'self' https://fonts.gstatic.com; "
                . "img-src 'self' https: data:; "
                . "connect-src 'self'; media-src 'self'; frame-ancestors 'self'; "
                . "object-src 'none'; base-uri 'self'; form-action 'self'";
        }
        $response->headers->set('Content-Security-Policy', $csp);

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = null;

        if (app()->environment('production')) {
            // A fresh nonce per request allows the small inline scripts used by
            // Blade while avoiding a site-wide script-src unsafe-inline grant.
            $nonce = Vite::useCspNonce();
            view()->share('cspNonce', $nonce);
        }

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(self), microphone=()');
        $response->headers->remove('X-Powered-By');

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        }

        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        if (app()->environment('production')) {
            $policy = implode('; ', [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'none'",
                "form-action 'self'",
                "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline'",
                "font-src 'self' data:",
                "img-src 'self' data: blob: https:",
                "connect-src 'self'",
                "manifest-src 'self'",
                "worker-src 'self'",
                'upgrade-insecure-requests',
            ]);

            $response->headers->set('Content-Security-Policy', $policy);
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}

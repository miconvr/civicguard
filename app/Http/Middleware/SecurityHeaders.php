<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(self), microphone=()');
        $response->headers->remove('X-Powered-By');

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
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
                "font-src 'self' data: https://fonts.bunny.net",
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

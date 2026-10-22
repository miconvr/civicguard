<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestPerformance
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);
        $response = $next($request);
        $durationMs = (microtime(true) - $startedAt) * 1000;

        $response->headers->set('Server-Timing', 'app;dur='.number_format($durationMs, 1, '.', ''));

        $thresholdMs = (float) config('performance.slow_request_threshold_ms', 1500);

        if ($durationMs >= $thresholdMs) {
            Log::warning('Slow HTTP request', [
                'method' => $request->method(),
                'route' => $request->route()?->getName() ?? 'unmatched',
                'status' => $response->getStatusCode(),
                'duration_ms' => (int) round($durationMs),
            ]);
        }

        return $response;
    }
}

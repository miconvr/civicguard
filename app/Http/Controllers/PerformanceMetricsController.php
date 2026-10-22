<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceMetricsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9._-]+$/'],
            'metrics' => ['required', 'array:ttfb_ms,lcp_ms,inp_ms,cls'],
            'metrics.ttfb_ms' => ['nullable', 'numeric', 'between:0,600000'],
            'metrics.lcp_ms' => ['nullable', 'numeric', 'between:0,600000'],
            'metrics.inp_ms' => ['nullable', 'numeric', 'between:0,600000'],
            'metrics.cls' => ['nullable', 'numeric', 'between:0,10'],
        ]);

        $metrics = array_filter($validated['metrics'], fn ($value) => is_numeric($value));

        if ($metrics !== []) {
            Log::info('Sampled browser performance', [
                'route' => $validated['route'],
                'metrics' => array_map(fn ($value) => round((float) $value, 2), $metrics),
            ]);
        }

        return response()->noContent();
    }
}

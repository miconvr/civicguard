<?php

return [
    // Slow request logs contain route names and timings only; no request data or user identifiers.
    'slow_request_threshold_ms' => (int) env('SLOW_REQUEST_THRESHOLD_MS', 1500),
];

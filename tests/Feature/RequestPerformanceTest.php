<?php

use Illuminate\Support\Facades\Log;

test('responses expose server timing without recording request contents', function () {
    config(['performance.slow_request_threshold_ms' => 0]);

    Log::shouldReceive('warning')
        ->once()
        ->with('Slow HTTP request', Mockery::on(function (array $context) {
            expect(array_keys($context))->toBe(['method', 'route', 'status', 'duration_ms']);

            return $context['method'] === 'GET'
                && $context['status'] === 200
                && is_int($context['duration_ms']);
        }));

    $this->get('/?private=example')
        ->assertOk()
        ->assertHeader('Server-Timing');
});

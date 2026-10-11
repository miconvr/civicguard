<?php

use App\Services\GeminiClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('Gemini API key is sent in a header and not the request URL', function () {
    config(['services.gemini.key' => 'test-api-key']);
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'Generated response']]]],
            ],
        ]),
    ]);

    $result = app(GeminiClient::class)->generateText('Test prompt');

    expect($result)->toBe('Generated response');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/models/gemini-flash-latest:generateContent')
        && ! str_contains($request->url(), 'key=')
        && $request->hasHeader('x-goog-api-key', 'test-api-key'));
});

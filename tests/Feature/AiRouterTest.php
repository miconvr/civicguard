<?php

use App\Services\AiRouter;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function aiTestGemini(string $text): array
{
    return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]]];
}

function aiTestChat(string $text): array
{
    return ['choices' => [['message' => ['content' => $text]]]];
}

beforeEach(function () {
    config([
        'services.gemini.key' => 'gem-key',
        'services.groq.key' => 'groq-key',
        'services.groq.model' => 'groq-model',
        'services.groq.fast_model' => 'groq-fast',
        'services.mistral.key' => null,
        'services.mistral.model' => 'mistral-small-latest',
        'services.mistral.fast_model' => 'mistral-small-latest',
        'services.ai.order' => ['gemini', 'groq', 'mistral'],
        'services.ai.severity_vote' => false,
    ]);
});

test('the first working provider answers and later ones are not called', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response(aiTestGemini('Gemini answer')),
        'api.groq.com/*' => Http::response(aiTestChat('Groq answer')),
    ]);

    $router = app(AiRouter::class);

    expect($router->generateText('Hello'))->toBe('Gemini answer');
    expect($router->lastProvider())->toBe('gemini');
    Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'api.groq.com'));
});

test('the next provider answers when the first one fails', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
        'api.groq.com/*' => Http::response(aiTestChat('Groq answer')),
    ]);

    $router = app(AiRouter::class);

    expect($router->generateText('Hello'))->toBe('Groq answer');
    expect($router->lastProvider())->toBe('groq');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'api.groq.com/openai/v1/chat/completions')
        && ! str_contains($request->url(), 'key=')
        && $request->hasHeader('Authorization', 'Bearer groq-key'));
});

test('the router returns null when every provider fails', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
        'api.groq.com/*' => Http::response([], 429),
    ]);

    $router = app(AiRouter::class);

    expect($router->generateText('Hello'))->toBeNull();
    expect($router->lastProvider())->toBeNull();
});

test('providers without a key are skipped and nothing is sent', function () {
    config(['services.gemini.key' => null, 'services.groq.key' => null]);
    Http::fake();

    expect(app(AiRouter::class)->generateText('Hello'))->toBeNull();
    Http::assertNothingSent();
});

test('the severity vote returns the highest valid rating', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response(aiTestGemini('low')),
        'api.groq.com/*' => Http::response(aiTestChat('High.')),
    ]);

    expect(app(AiRouter::class)->severityVote('Rate this.'))->toBe('high');
});

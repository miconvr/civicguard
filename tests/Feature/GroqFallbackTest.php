<?php

use App\Models\User;
use App\Services\AiRouter;
use Illuminate\Support\Facades\Http;

test('groq answers the chatbot when gemini fails', function () {
    config([
        'services.gemini.key' => 'gem-key',
        'services.groq.key' => 'groq-key',
        'services.groq.model' => 'groq-model',
        'services.groq.fast_model' => 'groq-fast',
        'services.ai.order' => ['gemini', 'groq'],
    ]);
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
        'api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Open My Reports to track it.']]]]),
    ]);
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'How do I track my report?'])
        ->assertOk()
        ->assertJsonPath('model_note', 'Answered by Groq');
});

test('a missing groq key means groq is never used', function () {
    config([
        'services.gemini.key' => 'gem-key',
        'services.groq.key' => null,
        'services.ai.order' => ['groq', 'gemini'],
    ]);

    expect(array_keys(app(AiRouter::class)->providers()))->toBe(['gemini']);
});

<?php

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('chatbot replies say which model answered', function () {
    config([
        'services.gemini.key' => null,
        'services.groq.key' => 'groq-key',
        'services.groq.model' => 'groq-model',
        'services.groq.fast_model' => 'groq-fast',
        'services.ai.order' => ['gemini', 'groq'],
    ]);
    Http::fake([
        'api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Open My Reports to track it.']]]]),
    ]);
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'How do I track my report?'])
        ->assertOk()
        ->assertJsonPath('model_note', 'Answered by Groq');
});

test('analytics counts reports by the model that rated them', function () {
    foreach ([['severity_source' => 'Groq'], ['severity_source' => 'Gemini'], ['severity_source' => 'model vote (Gemini: low, Groq: high)'], []] as $metadata) {
        AuditLog::create([
            'user_id' => null,
            'action' => 'report_submitted',
            'auditable_type' => Report::class,
            'auditable_id' => 1,
            'description' => 'Incident report submitted.',
            'metadata' => $metadata,
        ]);
    }
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)->get(route('admin.analytics'))
        ->assertOk()
        ->assertViewHas('modelUsage', fn ($u) => $u['Groq'] === 2
            && $u['Gemini'] === 2
            && ! array_key_exists('Model vote', $u)
            && $u['Before tracking'] === 1);
});

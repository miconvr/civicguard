<?php

use App\Models\AuditLog;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function attributionReport(): void
{
    $category = ReportCategory::firstOrCreate(['name' => 'Noise'], ['default_severity' => 'low']);
    $resident = User::factory()->create(['role' => 'resident']);

    test()->actingAs($resident)->post(route('reports.store'), [
        'category_id' => $category->id,
        'description' => 'Loud music late at night near the basketball court.',
        'location_text' => 'Purok 1',
    ])->assertRedirect(route('reports.index'));
}

beforeEach(function () {
    config([
        'services.gemini.key' => 'gem-key',
        'services.groq.key' => 'groq-key',
        'services.groq.model' => 'groq-model',
        'services.groq.fast_model' => 'groq-fast',
        'services.ai.order' => ['gemini', 'groq'],
    ]);
});

test('the audit log names the model that rated a report', function () {
    config(['services.ai.severity_vote' => false]);
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'high']]]]],
        ]),
    ]);

    attributionReport();

    $entry = AuditLog::where('action', 'report_submitted')->latest('id')->first();
    expect($entry->description)->toContain('Severity rated by Gemini');
});

test('the audit log shows each model rating when the vote is on', function () {
    config(['services.ai.severity_vote' => true]);
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'low']]]]],
        ]),
        'api.groq.com/*' => Http::response([
            'choices' => [['message' => ['content' => 'High']]],
        ]),
    ]);

    attributionReport();

    $entry = AuditLog::where('action', 'report_submitted')->latest('id')->first();
    expect($entry->description)
        ->toContain('model vote')
        ->toContain('Gemini: low')
        ->toContain('Groq: high');
});

test('the audit log says keyword rules when every model fails', function () {
    config(['services.ai.severity_vote' => false]);
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
        'api.groq.com/*' => Http::response([], 500),
    ]);

    attributionReport();

    $entry = AuditLog::where('action', 'report_submitted')->latest('id')->first();
    expect($entry->description)->toContain('keyword rules');
});

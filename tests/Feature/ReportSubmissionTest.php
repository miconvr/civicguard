<?php

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;

test('guests cannot open the incident report form', function () {
    $this->get(route('reports.create'))->assertRedirect(route('login'));
});

test('tanods cannot submit resident incident reports', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('reports.create'))
        ->assertForbidden();
});

test('a resident can submit an incident report and is sent to my reports', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Noise Complaint',
        'default_severity' => 'low',
    ]);

    $response = $this->actingAs($resident)->post(route('reports.store'), [
        'category_id' => $category->id,
        'description' => 'Loud karaoke past midnight near the court.',
        'location_text' => 'Purok 3, near the basketball court',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('reports.index'))
        ->assertSessionHas('status');

    $report = Report::query()->first();

    expect($report)->not->toBeNull()
        ->and($report->user_id)->toBe($resident->id)
        ->and($report->category_id)->toBe($category->id)
        ->and($report->status)->toBe('pending')
        ->and($report->severity)->toBe('low')
        ->and($report->location_text)->toBe('Purok 3, near the basketball court');

    expect(AuditLog::query()->where('action', 'report_submitted')->exists())->toBeTrue();

    $this->actingAs($resident)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Loud karaoke past midnight near the court.');
});

test('urgency keywords raise severity when gemini is unavailable', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Public Disturbance',
        'default_severity' => 'moderate',
    ]);

    $this->actingAs($resident)->post(route('reports.store'), [
        'category_id' => $category->id,
        'description' => 'Someone pulled a knife during a fight by the store.',
        'location_text' => 'Main road, beside the sari-sari store',
    ])->assertRedirect(route('reports.index'));

    expect(Report::query()->value('severity'))->toBe('critical');
});

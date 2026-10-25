<?php

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Services\AiRouter;

test('guests cannot open the incident report form', function () {
    $this->get(route('reports.create'))->assertRedirect(route('login'));
});

test('tanods cannot submit resident incident reports', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('reports.create'))
        ->assertForbidden();
});

test('residents see report writing guidance on the incident form', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->get(route('reports.create'))
        ->assertOk()
        ->assertSee('What happened?')
        ->assertSee('Say what happened, and when it happened.')
        ->assertSee('id="use-location"', false)
        ->assertSee('id="use-location-label">Use current location</span>', false);
});

test('a resident can submit an incident report and is sent to my reports', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn(null);

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Noise Complaint',
        'default_severity' => 'low',
    ]);

    $response = $this->actingAs($resident)->post(route('reports.store'), [
        'category_id' => $category->id,
        'description' => 'A karaoke gathering continued past midnight near the court.',
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
        ->assertSee('Follow each report’s status and updates here.')
        ->assertSee('Your reports are private to you and barangay staff.')
        ->assertSee('class="t-success-check shrink-0" data-state="in"', false)
        ->assertSee('A karaoke gathering continued past midnight near the court.');
});

test('chatbot report submissions receive a success response without a redirect', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn(null);

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Chatbot Report',
        'default_severity' => 'low',
    ]);

    $this->actingAs($resident)
        ->postJson(route('reports.store'), [
            'category_id' => $category->id,
            'description' => 'A resident submitted this from the chatbot review card.',
            'location_text' => 'Purok 8',
        ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('report_id', Report::query()->value('id'))
        ->assertJsonPath('redirect_url', route('reports.index'));

    expect(Report::query()->where('user_id', $resident->id)->count())->toBe(1);
});

test('a resident can confirm a resolved report was fixed', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Resolved Report',
        'default_severity' => 'low',
    ]);
    $report = Report::create([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'A resolved issue that the resident can confirm.',
        'location_text' => 'Purok E2E, beside the community hall',
        'severity' => 'low',
        'status' => 'resolved',
        'resolved_at' => now()->subHour(),
    ]);

    $this->actingAs($resident)
        ->post(route('reports.confirm', $report))
        ->assertRedirect(route('reports.index'))
        ->assertSessionHas('status', __('Thank you for confirming.'));

    expect($report->fresh()->confirmed_at)->not->toBeNull();
    expect(AuditLog::query()
        ->where('user_id', $resident->id)
        ->where('action', 'report_confirmed_fixed')
        ->where('auditable_id', $report->id)
        ->exists())->toBeTrue();

    $this->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Purok E2E, beside the community hall')
        ->assertSee(__('Confirmed fixed'));
});

test('a resident can remove an untouched pending report and the deletion is audited', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Pending Report',
        'default_severity' => 'low',
    ]);
    $report = Report::create([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'A report submitted by mistake.',
        'location_text' => 'Purok 4',
        'severity' => 'low',
        'status' => 'pending',
    ]);

    $this->actingAs($resident)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee(__('Remove report'));

    $this->actingAs($resident)
        ->delete(route('reports.destroyPending', $report))
        ->assertRedirect(route('reports.index'))
        ->assertSessionHas('status', __('Your pending report was removed.'));

    expect(Report::query()->find($report->id))->toBeNull()
        ->and(AuditLog::query()
            ->where('action', 'report_deleted')
            ->where('auditable_id', $report->id)
            ->exists())->toBeTrue();
});

test('a resident cannot remove another resident report or one staff has started handling', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Protected Report',
        'default_severity' => 'low',
    ]);
    $otherReport = Report::create([
        'user_id' => $otherResident->id,
        'category_id' => $category->id,
        'description' => 'A report owned by another resident.',
        'location_text' => 'Purok 5',
        'severity' => 'low',
        'status' => 'pending',
    ]);
    $handledReport = Report::create([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'A report staff have started handling.',
        'location_text' => 'Purok 6',
        'severity' => 'low',
        'status' => 'pending',
    ]);
    AuditLog::create([
        'user_id' => User::factory()->create(['role' => 'official'])->id,
        'action' => 'report_status_changed',
        'auditable_type' => Report::class,
        'auditable_id' => $handledReport->id,
        'description' => 'Report status changed to pending.',
    ]);

    $this->actingAs($resident)
        ->delete(route('reports.destroyPending', $otherReport))
        ->assertNotFound();

    $this->delete(route('reports.destroyPending', $handledReport))
        ->assertUnprocessable();

    expect(Report::query()->find($otherReport->id))->not->toBeNull()
        ->and(Report::query()->find($handledReport->id))->not->toBeNull();
});

test('urgency keywords raise severity when gemini is unavailable', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn(null);

    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::create([
        'name' => 'Test Public Disturbance',
        'default_severity' => 'moderate',
    ]);

    $this->actingAs($resident)->post(route('reports.store'), [
        'category_id' => $category->id,
        'description' => 'Someone pulled a knife during a fight by the store.',
        'location_text' => 'Main road, beside the sari-sari store',
    ])->assertRedirect(route('reports.index'));

    expect(Report::query()->value('severity'))->toBe('critical');
});

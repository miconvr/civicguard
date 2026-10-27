<?php

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ResolutionSummary;
use App\Models\User;

function summaryTestReport(array $attrs = []): Report
{
    $category = ReportCategory::firstOrCreate(['name' => 'Noise'], ['default_severity' => 'low']);
    $resident = User::factory()->create(['role' => 'resident']);

    return Report::create(array_merge([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'Loud karaoke past midnight.',
        'location_text' => 'Rizal Street',
        'severity' => 'low',
        'status' => 'in_progress',
    ], $attrs));
}

function validSummary(array $overrides = []): array
{
    return array_merge([
        'summary_resolution_type' => 'settled_on_site',
        'summary_narrative' => 'Neighbors were holding a party; the host agreed to turn the volume down.',
        'summary_action_taken' => 'Talked to the host and issued a verbal warning.',
        'summary_persons_involved' => 'Host and two neighbors.',
    ], $overrides);
}

test('a tanod cannot resolve a report without an incident summary', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'])
        ->assertSessionHasErrors(['summary_resolution_type', 'summary_narrative', 'summary_action_taken']);

    expect($report->fresh()->status)->toBe('in_progress');
    expect(ResolutionSummary::count())->toBe(0);
});

test('a tanod resolves their report with a summary and it is audited', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary())
        ->assertSessionHasNoErrors();

    $report->refresh();
    expect($report->status)->toBe('resolved');
    expect($report->resolutionSummary->written_by)->toBe($tanod->id);
    expect($report->resolutionSummary->resolution_type)->toBe('settled_on_site');
    expect(AuditLog::where('action', 'report_summary_recorded')->where('auditable_id', $report->id)->exists())->toBeTrue();
});

test('the details page status form also requires a summary when resolving', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.updateStatus', $report), ['status' => 'resolved'])
        ->assertSessionHasErrors('summary_narrative');

    $this->actingAs($tanod)
        ->patch(route('admin.reports.updateStatus', $report), ['status' => 'resolved'] + validSummary())
        ->assertSessionHasNoErrors();

    expect($report->fresh()->status)->toBe('resolved');
    expect($report->fresh()->resolutionSummary)->not->toBeNull();
});

test('follow-up notes are required when follow-up is marked as needed', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary(['summary_follow_up_needed' => '1']))
        ->assertSessionHasErrors('summary_follow_up_notes');
});

test('an already resolved report can still be edited without rewriting the summary', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $report = summaryTestReport(['status' => 'resolved', 'resolved_at' => now()]);

    $this->actingAs($admin)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved', 'severity' => 'high'])
        ->assertSessionHasNoErrors();

    expect($report->fresh()->severity)->toBe('high');
});

test('a tanod cannot write a summary for a report assigned to someone else', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport();

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary())
        ->assertForbidden();
});

test('officials see the summary and can mark it reviewed', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $official = User::factory()->create(['role' => 'official']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());

    $this->actingAs($official)
        ->get(route('admin.reports.details', $report))
        ->assertOk()
        ->assertSee('Talked to the host and issued a verbal warning.')
        ->assertSee('Awaiting review');

    $this->actingAs($official)
        ->patch(route('admin.reports.summary.review', $report))
        ->assertSessionHasNoErrors();

    $summary = $report->fresh()->resolutionSummary;
    expect($summary->reviewed_by)->toBe($official->id);
    expect($summary->reviewed_at)->not->toBeNull();
});

test('tanods cannot mark a summary as reviewed', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());

    $this->actingAs($tanod)
        ->patch(route('admin.reports.summary.review', $report))
        ->assertForbidden();
});

test('editing a reviewed summary clears the review', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $official = User::factory()->create(['role' => 'official']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());
    $this->actingAs($official)->patch(route('admin.reports.summary.review', $report));

    $this->actingAs($tanod)->patch(
        route('admin.reports.update', $report),
        ['status' => 'resolved'] + validSummary(['summary_action_taken' => 'Also referred the host to the Lupon.'])
    );

    expect($report->fresh()->resolutionSummary->reviewed_at)->toBeNull();
});

test('residents cannot reach the summary or its pdf', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());

    $this->actingAs($report->user)
        ->get(route('admin.reports.summary.pdf', $report))
        ->assertForbidden();
});

test('staff can download the summary pdf', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());

    $this->actingAs($tanod)
        ->get(route('admin.reports.summary.pdf', $report))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('saving a summary flags the save so the green check can show', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary())
        ->assertSessionHas('summary_saved', $report->id);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'])
        ->assertSessionMissing('summary_saved');
});

test('a saved summary is shown locked with an edit button and can still be changed', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = summaryTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary());

    $this->actingAs($tanod)
        ->get(route('admin.reports.details', $report))
        ->assertOk()
        ->assertSee('Edit summary');

    $this->actingAs($tanod)
        ->patch(route('admin.reports.update', $report), ['status' => 'resolved'] + validSummary(['summary_action_taken' => 'Updated the action after a follow-up visit.']))
        ->assertSessionHasNoErrors();

    expect($report->fresh()->resolutionSummary->action_taken)->toBe('Updated the action after a follow-up visit.');
});

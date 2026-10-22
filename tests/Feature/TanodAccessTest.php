<?php

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;

function tanodTestReport(array $attrs = []): Report
{
    $category = ReportCategory::firstOrCreate(['name' => 'Noise'], ['default_severity' => 'low']);
    $resident = User::factory()->create(['role' => 'resident']);

    return Report::create(array_merge([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'Test report.',
        'location_text' => 'Somewhere',
        'severity' => 'low',
        'status' => 'pending',
    ], $attrs));
}

test('a tanod can open details of a report assigned to them', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = tanodTestReport(['assigned_to' => $tanod->id]);

    $this->actingAs($tanod)->get(route('admin.reports.details', $report))->assertOk();
});

test('a tanod cannot open details, curfew details or photo of an unassigned report', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $report = tanodTestReport();

    $this->actingAs($tanod)->get(route('admin.reports.details', $report))->assertForbidden();
    $this->actingAs($tanod)->get(route('admin.reports.curfewDetails', $report))->assertForbidden();
    $this->actingAs($tanod)->get(route('admin.reports.photo', $report))->assertForbidden();
});

test('a tanod queue lists only their assigned reports', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    tanodTestReport(['assigned_to' => $tanod->id, 'location_text' => 'Mine Street Alpha']);
    tanodTestReport(['location_text' => 'Other Street Bravo']);

    $this->actingAs($tanod)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Mine Street Alpha')
        ->assertDontSee('Other Street Bravo');
});

test('tanod queue summary counts exclude reports they cannot see', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);
    $unrelatedReport = tanodTestReport([
        'severity' => 'critical',
        'status' => 'pending',
    ]);
    $unrelatedReport->created_at = now()->subDays(3);
    $unrelatedReport->save();

    $this->actingAs($tanod)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-testid="queue-stat-value-critical">0</div>', false)
        ->assertSee('data-testid="queue-stat-value-unassigned">0</div>', false)
        ->assertSee('data-testid="queue-stat-value-overdue">0</div>', false);
});

test('an official can open details of any report', function () {
    $official = User::factory()->create(['role' => 'official']);
    $report = tanodTestReport();

    $this->actingAs($official)->get(route('admin.reports.details', $report))->assertOk();
});

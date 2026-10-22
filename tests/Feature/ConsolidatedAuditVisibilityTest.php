<?php

use App\Models\User;

test('tanods do not see system activity on the consolidated reports page', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertDontSee('Recent System Activity');
});

test('tanods see the scoped summary tab without a link to full analytics', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertSee(route('admin.consolidatedReports'), false)
        ->assertDontSee(route('admin.analytics'), false);
});

test('tanods cannot view the barangay-wide analytics page or its data endpoints', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('admin.analytics'))
        ->assertForbidden();

    $this->get(route('admin.analytics.insights'))->assertForbidden();
    $this->get(route('admin.analytics.exportPdf'))->assertForbidden();
});

test('officials see system activity on the consolidated reports page', function () {
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)
        ->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertSee('Recent System Activity');
});

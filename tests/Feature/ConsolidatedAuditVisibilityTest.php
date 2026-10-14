<?php

use App\Models\User;

test('tanods do not see system activity on the consolidated reports page', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertDontSee('Recent System Activity');
});

test('officials see system activity on the consolidated reports page', function () {
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)
        ->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertSee('Recent System Activity');
});

<?php

use App\Models\User;

test('each user role can render its role-specific dashboard', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Good day,')
        ->assertSee(ucfirst($role));

    if ($role === 'resident') {
        $response->assertSee('Track your reports and see the latest updates.');
    }
})->with(['resident', 'tanod', 'official', 'admin']);

test('staff roles can render the incident queue', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Incident Queue')
        ->assertSee('Search by category, location, or report details')
        ->assertSee('bg-stone-200/70')
        ->assertDontSee('border-l-2 border-maroon-700');
})->with(['tanod', 'official', 'admin']);

test('admin analytics and staff creation titles support dark mode', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.analytics'))
        ->assertOk()
        ->assertSee('<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">', false);

    $this->get(route('admin.staff.create'))
        ->assertOk()
        ->assertSee('Add Tanod / Official Account')
        ->assertSee('<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">', false);
});

test('staff report views are grouped under one reports section with tabs', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.analytics'))
        ->assertOk()
        ->assertSee('Reports')
        ->assertSee(route('admin.consolidatedReports'))
        ->assertSee('Analytics');

    $this->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertSee('Reports')
        ->assertSee(route('admin.analytics'))
        ->assertSee('Summary');
});

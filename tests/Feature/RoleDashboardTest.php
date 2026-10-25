<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('each user role can render its role-specific dashboard', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Good day,')
        ->assertSee(ucfirst($role))
        ->assertSee('class="cg-page-enter flex-1"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('data-testid="sidebar-toggle"', false)
        ->assertSee('x-transition:enter="t-dropdown"', false)
        ->assertSee('aria-label="Toggle dark mode"', false)
        ->assertSeeInOrder([
            'Language',
            'Appearance',
            'href="'.route('profile.edit').'"',
        ], false)
        ->assertSee('role="switch"', false);

    expect(substr_count($response->getContent(), 'role="switch"'))->toBe(1);

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
        ->assertSee('bg-white/15')
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

test('admins and officials can reset resident passwords', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $admin = User::factory()->create(['role' => 'admin']);
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($admin)
        ->get(route('admin.passwordResets.index'))
        ->assertOk()
        ->assertSee('Reset Resident Passwords')
        ->assertSee($resident->email);

    $this->actingAs($official)
        ->get(route('admin.passwordResets.index'))
        ->assertOk()
        ->assertSee($resident->email);

    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)
        ->get(route('admin.passwordResets.index'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->patch(route('admin.passwordResets.update', $resident), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect(route('admin.passwordResets.index'));

    expect(Hash::check('new-password', $resident->refresh()->password))->toBeTrue();

    $staff = User::factory()->create(['role' => 'official']);

    $this->actingAs($admin)
        ->patch(route('admin.passwordResets.update', $staff), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertNotFound();
});

test('staff report views are grouped under one reports section with tabs', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.analytics'))
        ->assertOk()
        ->assertSee('Reports')
        ->assertSee('class="text-3xl font-bold cg-kpi"', false)
        ->assertSee(route('admin.consolidatedReports'))
        ->assertSee('Analytics');

    $this->get(route('admin.consolidatedReports'))
        ->assertOk()
        ->assertSee('Reports')
        ->assertSee('class="text-3xl font-bold cg-kpi"', false)
        ->assertSee(route('admin.analytics'))
        ->assertSee('Summary');
});

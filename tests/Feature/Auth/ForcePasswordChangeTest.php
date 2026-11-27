<?php

use App\Models\User;

test('staff flagged for a password change are redirected to the change page', function () {
    $user = User::factory()->create(['role' => 'tanod', 'must_change_password' => true]);

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('password.force.edit'));
});

test('flagged staff can still open the change page and log out', function () {
    $user = User::factory()->create(['role' => 'tanod', 'must_change_password' => true]);

    $this->actingAs($user)->get(route('password.force.edit'))->assertOk();
    $this->actingAs($user)->post('/logout')->assertRedirect('/');
});

test('changing the password clears the flag', function () {
    $user = User::factory()->create(['role' => 'official', 'must_change_password' => true]);

    $this->actingAs($user)->put(route('password.force.update'), [
        'current_password' => 'password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect(route('dashboard'));

    expect($user->fresh()->must_change_password)->toBeFalse();
    $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
});

test('the new password must differ from the current one', function () {
    $user = User::factory()->create(['role' => 'tanod', 'must_change_password' => true]);

    $this->actingAs($user)->put(route('password.force.update'), [
        'current_password' => 'password',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('password');

    expect($user->fresh()->must_change_password)->toBeTrue();
});

test('accounts created by an admin must change their password', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.staff.store'), [
        'name' => 'New Tanod',
        'email' => 'newtanod@example.com',
        'password' => 'temporary-pass-1',
        'role' => 'tanod',
    ])->assertRedirect(route('admin.staff.create'));

    expect(User::where('email', 'newtanod@example.com')->first()->must_change_password)->toBeTrue();
});

test('residents are not affected', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)->get('/dashboard')->assertOk();
});

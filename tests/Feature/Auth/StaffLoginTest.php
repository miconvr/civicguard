<?php

use App\Models\User;

test('staff login page renders', function () {
    $this->get('/staff/login')->assertOk();
});

test('staff roles can sign in on the staff page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->post('/staff/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
})->with(['tanod', 'official', 'admin']);

test('residents cannot sign in on the staff page', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->post('/staff/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => trans('auth.failed')]);

    $this->assertGuest();
});

test('staff are blocked on the resident login page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
})->with(['tanod', 'official', 'admin']);

test('residents can still sign in on the resident login page', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);

    $this->assertAuthenticatedAs($user);
});

<?php

use App\Models\User;

test('a resident with correct credentials is told to use the resident login', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->post(route('staff.login'), [
        'email' => $resident->email,
        'password' => 'password',
    ])->assertSessionHasErrors(['email' => __('Resident accounts must sign in on the resident login page.')]);

    $this->assertGuest();
});

test('a resident with a wrong password gets the generic message', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->post(route('staff.login'), [
        'email' => $resident->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors(['email' => trans('auth.failed')]);
});

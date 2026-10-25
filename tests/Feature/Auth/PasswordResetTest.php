<?php

test('forgot password page explains the staff-assisted reset process', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200)
        ->assertSee('CivicGuard does not send password-reset emails.')
        ->assertSee('barangay administrator or official')
        ->assertSee(route('login', absolute: false));
});

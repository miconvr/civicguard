<?php

test('resident login shows the resident badge and the switcher', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('staff.login'))
        ->assertSee('Resident');
});

test('resident signup links back to the login and has its own title', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee(route('login'))
        ->assertSee('<title>Create Account', false);
});

test('staff login shows the staff band and links residents back', function () {
    $this->get('/staff/login')
        ->assertOk()
        ->assertSee('<title>Staff Login', false)
        ->assertSee('Staff only')
        ->assertSee('Sign-ins to this page are recorded.')
        ->assertSee(route('login'))
        ->assertDontSee('Create an Account');
});

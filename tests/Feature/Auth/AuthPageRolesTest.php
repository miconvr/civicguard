<?php

test('resident login shows the resident badge and the switcher', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('staff.login'))
        ->assertSee('Resident');
});

test('resident signup shows the switcher with a link to the staff login', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee(route('staff.login'))
        ->assertSee(route('login'));
});

test('staff login shows the staff band and links residents back', function () {
    $this->get('/staff/login')
        ->assertOk()
        ->assertSee('Staff only')
        ->assertSee('Sign-ins to this page are recorded.')
        ->assertSee(route('login'))
        ->assertDontSee('Create an Account');
});

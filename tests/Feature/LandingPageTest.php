<?php

test('public landing page clearly links to resident and staff entry points', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Let’s take care of our barangay.')
        ->assertSee('For the people of Barangay Maimpis')
        ->assertSee('landingPage')
        ->assertSee('Tagalog')
        ->assertSee(route('register'))
        ->assertSee(route('login'))
        ->assertSee('Emergency? Call 911. CivicGuard reports are not monitored in real time.');
});

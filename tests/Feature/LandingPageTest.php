<?php

test('public landing page clearly links to resident and staff entry points', function () {
    $response = $this->get('/')
        ->assertOk()
        ->assertSee('Let’s take care of our barangay.')
        ->assertSee('For the people of Barangay Maimpis')
        ->assertSee('landingPage')
        ->assertSee('toggleLanguage()')
        ->assertSee('Switch language to Tagalog')
        ->assertSee(route('register'))
        ->assertSee(route('login'))
        ->assertSee('Emergency? Call 911. CivicGuard reports are not monitored in real time.');

    expect(strpos($response->getContent(), 'Let’s take care of our barangay.'))
        ->toBeLessThan(strpos($response->getContent(), 'From our kababayans, for our barangay'));
});

<?php

test('privacy policy and terms are public', function () {
    $this->get(route('privacy'))->assertOk()->assertSee('Privacy Policy');
    $this->get(route('terms'))->assertOk()->assertSee('Terms of Use');
});

test('registration requires accepting the terms', function () {
    $this->post('/register', [
        'name' => 'No Consent',
        'email' => 'noconsent@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('terms');

    $this->assertGuest();
});

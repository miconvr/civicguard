<?php

test('landing page has an install button and an install guide', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="install-guide"', false)
        ->assertSee('showGuide()', false)
        ->assertSee('cg-phone', false)
        ->assertSee('Add to Home Screen');
});

test('hero shows the install button and a phone hint', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('cg-phone-chip', false)
        ->assertSee('cg-flow', false)
        ->assertSee('cg-hero-title', false);
});

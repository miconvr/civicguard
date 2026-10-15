<?php

test('landing page has an install button and an install guide', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="install-guide"', false)
        ->assertSee('showGuide()', false)
        ->assertSee('cg-phone', false)
        ->assertSee('Add to Home Screen');
});

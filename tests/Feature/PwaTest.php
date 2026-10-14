<?php

test('public pages link the web app manifest', function () {
    $this->get('/')->assertOk()->assertSee('rel="manifest"', false);
    $this->get('/login')->assertOk()->assertSee('rel="manifest"', false);
});

test('the manifest is valid and its icons exist', function () {
    $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);

    expect($manifest['name'])->toBe('CivicGuard');
    expect($manifest['display'])->toBe('standalone');

    foreach ($manifest['icons'] as $icon) {
        expect(file_exists(public_path(ltrim($icon['src'], '/'))))->toBeTrue();
    }
});

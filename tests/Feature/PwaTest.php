<?php

test('public pages link the web app manifest', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('rel="manifest"', false)
        ->assertSee('viewport-fit=cover', false)
        ->assertSee('meta name="theme-color" content="#f6f2eb"', false)
        ->assertSee('apple-mobile-web-app-status-bar-style" content="default"', false);
    expect(substr_count($response->getContent(), 'name="theme-color"'))->toBe(1);
    expect(substr_count($response->getContent(), 'name="apple-mobile-web-app-status-bar-style"'))->toBe(1);
    $this->get('/login')->assertOk()->assertSee('rel="manifest"', false);
});

test('the manifest is valid and its icons exist', function () {
    $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);

    expect($manifest['name'])->toBe('CivicGuard');
    expect($manifest['display'])->toBe('standalone');
    expect($manifest['background_color'])->toBe('#f6f2eb');

    foreach ($manifest['icons'] as $icon) {
        expect(file_exists(public_path(ltrim($icon['src'], '/'))))->toBeTrue();
    }
});

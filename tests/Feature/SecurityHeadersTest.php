<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_production_scripts_use_a_request_nonce_without_script_unsafe_inline(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get('/');

        $response->assertOk();
        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertIsString($policy);
        $scriptPolicy = collect(explode(';', $policy))
            ->first(fn (string $directive): bool => str_starts_with(trim($directive), 'script-src '));
        $this->assertIsString($scriptPolicy);
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptPolicy);
        $this->assertStringContainsString("'unsafe-eval'", $scriptPolicy);

        preg_match("/nonce-([^']+)'/", $scriptPolicy, $matches);
        $this->assertNotEmpty($matches[1] ?? null);

        preg_match_all('/<script\\b([^>]*)>/i', $response->getContent(), $scripts);
        $this->assertNotEmpty($scripts[1]);

        foreach ($scripts[1] as $attributes) {
            $this->assertStringContainsString('nonce="'.$matches[1].'"', $attributes);
        }

        $this->assertStringContainsString('property="csp-nonce" nonce="'.$matches[1].'"', $response->getContent());
    }
}

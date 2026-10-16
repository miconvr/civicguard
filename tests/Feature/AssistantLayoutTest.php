<?php

use App\Models\User;

test('assistant page has no purple gradient and shows the CivicGuard header', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->actingAs($user)->get(route('chatbot.widget'))
        ->assertOk()
        ->assertDontSee('from-purple', false)
        ->assertDontSee('purple-950', false)
        ->assertSee('AI Assistant');
});

test('assistant shows a back button only when opened from the report form', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->actingAs($user)->get(route('chatbot.widget', ['from' => 'report']))
        ->assertOk()
        ->assertSee('Back to report form')
        ->assertSee(route('reports.create'));

    $this->actingAs($user)->get(route('chatbot.widget'))
        ->assertOk()
        ->assertDontSee('Back to report form');
});

test('the report form links to the assistant with the back marker and no gradient', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->actingAs($user)->get(route('reports.create'))
        ->assertOk()
        ->assertSee('from=report', false)
        ->assertDontSee('from-purple', false);
});

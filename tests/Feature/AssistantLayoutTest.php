<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('the report form has a collapsed CivicGuard assistant launcher', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->actingAs($user)->get(route('reports.create'))
        ->assertOk()
        ->assertSee('report-ai-chat', false)
        ->assertSee('aria-controls="report-ai-chat"', false)
        ->assertSee('x-show="chatOpen"', false)
        ->assertSee('chatbot/send', false)
        ->assertSee('CivicGuard AI Chatbot');
});

test('the standalone chatbot page route has been removed while message sending remains available', function () {
    expect(Route::has('chatbot.widget'))->toBeFalse()
        ->and(Route::has('chatbot.send'))->toBeTrue();
});

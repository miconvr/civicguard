<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('residents can choose between the AI assistant and the standard report form', function () {
    $user = User::factory()->create(['role' => 'resident']);

    $this->actingAs($user)->get(route('reports.create'))
        ->assertOk()
        ->assertSee('report-ai-chat', false)
        ->assertSee('aria-controls="report-ai-chat"', false)
        ->assertSee('x-show="chatOpen"', false)
        ->assertSee('chatbot/send', false)
        ->assertSee('How would you like to report?')
        ->assertSee('Get help from CivicGuard AI')
        ->assertSee('Fill out the form myself')
        ->assertSee('sm:grid-cols-2', false)
        ->assertSee('min-h-11', false)
        ->assertSee('data-reveal="step"', false)
        ->assertSee('cg-page-enter', false)
        ->assertDontSee('In immediate danger? Call 911 first.')
        ->assertSee('id="description"', false)
        ->assertSee('Need a hand with your report?')
        ->assertSee('Open chat')
        ->assertSee('CivicGuard AI');
});

test('the standalone chatbot page route has been removed while message sending remains available', function () {
    expect(Route::has('chatbot.widget'))->toBeFalse()
        ->and(Route::has('chatbot.send'))->toBeTrue();
});

test('changing language returns residents to their selected reporting method', function () {
    $user = User::factory()->create(['role' => 'resident']);
    $selectedAssistant = route('reports.create', ['report_mode' => 'assistant']);

    $this->actingAs($user)
        ->from($selectedAssistant)
        ->get(route('locale.switch', 'tl'))
        ->assertRedirect($selectedAssistant)
        ->assertSessionHas('locale', 'tl');

    $this->get($selectedAssistant)
        ->assertOk()
        ->assertSee('Paano mo gustong mag-ulat?')
        ->assertSee('Pumili ng ibang paraan ng pag-uulat');
});

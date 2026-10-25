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
        ->assertSee('sm:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]', false)
        ->assertSee('min-h-11', false)
        ->assertSee('cg-page-enter', false)
        ->assertSee('cg-report-panel-transition', false)
        ->assertSee('x-transition:enter="cg-report-panel-transition"', false)
        ->assertSee('@transitionend.self="if ($event.propertyName === \'opacity\') finishPanelExit()"', false)
        ->assertSee('@click="leavePanel()"', false)
        ->assertDontSee("document.getElementById('chat-input')?.focus()", false)
        ->assertDontSee('In immediate danger? Call 911 first.')
        ->assertSee('id="description"', false)
        ->assertSee('Need a hand with your report?')
        ->assertSee('Open chat')
        ->assertSee('id="use-location"', false)
        ->assertSee('id="use-location-label">Use current location</span>', false)
        ->assertSee('maximumAge: 120000', false)
        ->assertSee('AI category suggestion is unavailable. Please choose a category yourself.')
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

<?php

use App\Models\User;
use App\Services\AiRouter;

test('urgent messages get fixed emergency guidance without calling Gemini', function () {
    $this->mock(AiRouter::class)->shouldNotReceive('generateText');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'There is a man with a knife outside right now'])
        ->assertOk()
        ->assertJson(['emergency' => true])
        ->assertJsonFragment(['report_data' => null]);
});

test('after the resident confirms they are safe the chatbot continues normally', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn('Thanks. What category best fits what happened?');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), [
            'message' => 'The man had a knife earlier',
            'emergency_ack' => true,
        ])
        ->assertOk()
        ->assertJsonMissing(['emergency' => true])
        ->assertJsonFragment(['reply' => 'Thanks. What category best fits what happened?']);
});

test('normal messages are not treated as emergencies', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn('You can track it under My Reports.');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'How do I track my report?'])
        ->assertOk()
        ->assertJsonMissing(['emergency' => true]);
});

test('an AI reply that tells the resident to call 911 also shows the continue button', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn('Please call 911 now. I can help with the report once everyone is safe.');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'My neighbor is hurting his wife right now'])
        ->assertOk()
        ->assertJson(['emergency' => true]);
});

test('the hidden emergency marker flags the reply and is never shown to the resident', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn('###EMERGENCY### Please contact the barangay officials right away.');

    $resident = User::factory()->create(['role' => 'resident']);

    $response = $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => 'Someone is breaking into our house'])
        ->assertOk()
        ->assertJson(['emergency' => true]);

    expect($response->json('reply'))->not->toContain('###EMERGENCY###');
});

test('the emergency actions do not repeat after the resident chose to continue', function () {
    $this->mock(AiRouter::class)
        ->shouldReceive('generateText')
        ->once()
        ->andReturn('If anyone is in danger, call 911. Where did it happen?');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), [
            'message' => 'I would like to continue with my report.',
            'emergency_ack' => true,
        ])
        ->assertOk()
        ->assertJsonMissing(['emergency' => true]);
});

test('serious phrases the old keyword list missed now trigger emergency guidance', function (string $message) {
    $this->mock(AiRouter::class)->shouldNotReceive('generateText');

    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($resident)
        ->postJson(route('chatbot.send'), ['message' => $message])
        ->assertOk()
        ->assertJson(['emergency' => true]);
})->with([
    'someone was raped' => 'My friend was raped last night',
    'drowning child' => 'A child is drowning in the creek',
    'tagalog shooting' => 'May nagbabaril sa kanto',
]);

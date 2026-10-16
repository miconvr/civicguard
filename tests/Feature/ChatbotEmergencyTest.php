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

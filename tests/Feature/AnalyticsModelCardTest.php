<?php

use App\Models\User;

test('analytics shows which model rated the reports', function () {
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)->get(route('admin.analytics'))
        ->assertOk()
        ->assertSee('Severity Rated By')
        ->assertSee('Gemini');
});

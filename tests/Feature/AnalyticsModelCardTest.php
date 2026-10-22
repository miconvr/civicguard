<?php

use App\Models\User;

test('analytics shows which model rated the reports', function () {
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)->get(route('admin.analytics'))
        ->assertOk()
        ->assertSee('analytics-category-title')
        ->assertSee('analytics-severity-title')
        ->assertSee('analytics-trend-title')
        ->assertDontSee('cdnjs.cloudflare.com')
        ->assertSee('Severity Rated By')
        ->assertSee('Gemini');
});

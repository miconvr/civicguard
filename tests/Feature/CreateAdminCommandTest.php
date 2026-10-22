<?php

use App\Models\User;

test('the bootstrap command creates the first administrator interactively', function () {
    $this->artisan('civicguard:create-admin')
        ->expectsQuestion('Administrator name', 'CivicGuard Admin')
        ->expectsQuestion('Administrator email', 'admin@example.com')
        ->expectsQuestion('Password (at least 12 characters)', 'CivicGuard-Test-Password-123!')
        ->expectsQuestion('Confirm password', 'CivicGuard-Test-Password-123!')
        ->assertSuccessful();

    $admin = User::where('email', 'admin@example.com')->firstOrFail();

    expect($admin->role)->toBe('admin')
        ->and(password_verify('CivicGuard-Test-Password-123!', $admin->password))->toBeTrue();
});

test('the bootstrap command refuses to create another administrator', function () {
    User::factory()->create(['role' => 'admin']);

    $this->artisan('civicguard:create-admin')
        ->expectsOutput('An administrator already exists. Ask an administrator to create staff accounts.')
        ->assertFailed();

    expect(User::where('role', 'admin')->count())->toBe(1);
});

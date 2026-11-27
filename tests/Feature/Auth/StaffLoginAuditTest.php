<?php

use App\Models\AuditLog;
use App\Models\User;

test('a successful staff sign-in is audited', function () {
    $user = User::factory()->create(['role' => 'tanod']);

    $this->post('/staff/login', ['email' => $user->email, 'password' => 'password']);

    $log = AuditLog::where('action', 'staff_login')->first();
    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($user->id);
});

test('a failed staff sign-in is audited without the email or password', function () {
    $user = User::factory()->create(['role' => 'official']);

    $this->post('/staff/login', ['email' => $user->email, 'password' => 'wrong-password-xyz']);

    $log = AuditLog::where('action', 'staff_login_failed')->first();
    expect($log)->not->toBeNull()
        ->and($log->auditable_id)->toBe($user->id);

    $stored = json_encode($log->toArray());
    expect($stored)->not->toContain('wrong-password-xyz')
        ->and($stored)->not->toContain($user->email);
});

test('a resident trying the staff page is logged without linking their account', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $this->post('/staff/login', ['email' => $resident->email, 'password' => 'password']);

    $log = AuditLog::where('action', 'staff_login_failed')->first();
    expect($log)->not->toBeNull()
        ->and($log->auditable_id)->toBeNull();
});

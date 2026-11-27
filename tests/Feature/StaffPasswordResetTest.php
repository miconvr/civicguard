<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function resetPayload(string $password = 'temporary-pass-1'): array
{
    return ['password' => $password, 'password_confirmation' => $password];
}

test('admins can reset a tanod password and force a change', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($admin)
        ->patch(route('admin.staffPasswordResets.update', $tanod), resetPayload())
        ->assertRedirect(route('admin.staffPasswordResets.index'));

    $tanod->refresh();
    expect(Hash::check('temporary-pass-1', $tanod->password))->toBeTrue()
        ->and($tanod->must_change_password)->toBeTrue();
    expect(AuditLog::where('action', 'staff_password_reset')->where('auditable_id', $tanod->id)->exists())->toBeTrue();
});

test('officials can reset a tanod password', function () {
    $official = User::factory()->create(['role' => 'official']);
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($official)
        ->patch(route('admin.staffPasswordResets.update', $tanod), resetPayload())
        ->assertRedirect(route('admin.staffPasswordResets.index'));

    expect($tanod->fresh()->must_change_password)->toBeTrue();
});

test('officials cannot reset officials or admins', function (string $role) {
    $official = User::factory()->create(['role' => 'official']);
    $target = User::factory()->create(['role' => $role]);

    $this->actingAs($official)
        ->patch(route('admin.staffPasswordResets.update', $target), resetPayload())
        ->assertNotFound();

    expect($target->fresh()->must_change_password)->toBeFalse();
})->with(['official', 'admin']);

test('admins cannot reset another admin on the staff page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $other = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->patch(route('admin.staffPasswordResets.update', $other), resetPayload())
        ->assertNotFound();
});

test('residents cannot be reset through the staff page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $resident = User::factory()->create(['role' => 'resident']);

    $this->actingAs($admin)
        ->patch(route('admin.staffPasswordResets.update', $resident), resetPayload())
        ->assertNotFound();
});

test('tanods cannot open the staff reset page', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)->get(route('admin.staffPasswordResets.index'))->assertForbidden();
});

test('the staff reset page lists staff the user may reset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['role' => 'tanod', 'name' => 'Juan Tanod Cruz']);

    $this->actingAs($admin)->get(route('admin.staffPasswordResets.index'))
        ->assertOk()
        ->assertSee('Juan Tanod Cruz');
});

<?php

use App\Models\AppNotification;
use App\Models\ResidentVerification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('review decisions retain the private proof and send the resident the decision reason', function () {
    Storage::fake('report_photos');
    $resident = User::factory()->create(['role' => 'resident']);
    $reviewer = User::factory()->create(['role' => 'official']);
    $path = 'verification-documents/'.$resident->id.'/proof.jpg';
    Storage::disk('report_photos')->put($path, 'test proof bytes');
    $verification = ResidentVerification::create([
        'user_id' => $resident->id,
        'document_path' => $path,
        'status' => ResidentVerification::PENDING,
        'consented_at' => now(),
        'submitted_at' => now(),
    ]);

    $this->actingAs($reviewer)->patch(route('admin.verifications.decide', $verification), [
        'decision' => ResidentVerification::REJECTED,
        'rejection_reason' => 'The document does not show a Maimpis address.',
    ])->assertRedirect(route('admin.verifications.index'));

    expect($verification->fresh()->rejection_reason)->toBe('The document does not show a Maimpis address.')
        ->and($verification->fresh()->document_path)->toBe($path);
    Storage::disk('report_photos')->assertExists($path);

    $notification = AppNotification::where('user_id', $resident->id)->firstOrFail();
    expect($notification->verification_id)->toBe($verification->id)
        ->and($notification->message)->toContain('The document does not show a Maimpis address.');

    $this->actingAs($resident)->post(route('notifications.open', $notification))
        ->assertRedirect(route('profile.verification.show'));
    $this->actingAs($resident)->get(route('profile.verification.show'))
        ->assertOk()
        ->assertSee('The document does not show a Maimpis address.')
        ->assertSee(route('profile.verification.document'), false);
    $this->actingAs($resident)->get(route('profile.verification.document'))->assertOk();
});

test('a resident can mark only their own notification as read', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);
    $own = AppNotification::create(['user_id' => $resident->id, 'message' => 'Your update.', 'is_read' => false]);
    $other = AppNotification::create(['user_id' => $otherResident->id, 'message' => 'Private update.', 'is_read' => false]);

    $this->actingAs($resident)->post(route('notifications.read', $own))->assertRedirect();
    expect($own->fresh()->is_read)->toBeTrue();

    $this->actingAs($resident)->post(route('notifications.read', $other))->assertForbidden();
    expect($other->fresh()->is_read)->toBeFalse();
});

test('declining a verification requires a reason', function () {
    Storage::fake('report_photos');
    $resident = User::factory()->create(['role' => 'resident']);
    $reviewer = User::factory()->create(['role' => 'admin']);
    $path = 'verification-documents/'.$resident->id.'/proof.jpg';
    Storage::disk('report_photos')->put($path, 'test proof bytes');
    $verification = ResidentVerification::create([
        'user_id' => $resident->id,
        'document_path' => $path,
        'status' => ResidentVerification::PENDING,
        'consented_at' => now(),
        'submitted_at' => now(),
    ]);

    $this->actingAs($reviewer)->from(route('admin.verifications.index'))->patch(route('admin.verifications.decide', $verification), [
        'decision' => ResidentVerification::REJECTED,
    ])->assertRedirect(route('admin.verifications.index'))
        ->assertSessionHasErrors('rejection_reason');

    expect($verification->fresh()->status)->toBe(ResidentVerification::PENDING);
});

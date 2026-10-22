<?php

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ResidentVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

test('deleting a resident account removes their reports and photo files', function () {
    Storage::fake('report_photos');
    $resident = User::factory()->create(['role' => 'resident']);
    $category = ReportCategory::firstOrCreate(['name' => 'Noise'], ['default_severity' => 'low']);
    Storage::disk('report_photos')->put('report-photos/a.jpg', 'x');
    $report = Report::create([
        'user_id' => $resident->id,
        'category_id' => $category->id,
        'description' => 'Test report.',
        'location_text' => 'Somewhere',
        'severity' => 'low',
        'status' => 'pending',
        'photo_path' => 'report-photos/a.jpg',
    ]);

    $this->actingAs($resident)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

    $this->assertDatabaseMissing('users', ['id' => $resident->id]);
    $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    Storage::disk('report_photos')->assertMissing('report-photos/a.jpg');
});

test('deleting a resident account removes any stored chatbot messages', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    DB::table('chatbot_conversations')->insert([
        'user_id' => $resident->id,
        'session_id' => 'legacy-chat-session',
        'sender' => 'user',
        'message' => 'A private incident description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($resident)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

    $this->assertDatabaseMissing('chatbot_conversations', [
        'session_id' => 'legacy-chat-session',
        'message' => 'A private incident description',
    ]);
});

test('staff cannot delete their own account', function () {
    foreach (['tanod', 'official', 'admin'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
});

test('resident account deletion removes their name from retained verification audit history', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $verification = ResidentVerification::create([
        'user_id' => $resident->id,
        'status' => ResidentVerification::VERIFIED,
        'consented_at' => now(),
        'submitted_at' => now(),
        'reviewed_at' => now(),
    ]);
    $audit = AuditLog::create([
        'user_id' => null,
        'action' => 'resident_verified',
        'auditable_type' => ResidentVerification::class,
        'auditable_id' => $verification->id,
        'description' => "Residence verification approved for {$resident->name} ({$resident->email}).",
    ]);

    $this->actingAs($resident)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

    expect($audit->fresh()->description)->toBe('Residence verification decision recorded.')
        ->and($audit->fresh()->description)->not->toContain($resident->email);
});

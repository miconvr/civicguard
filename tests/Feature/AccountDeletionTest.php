<?php

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
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

test('staff cannot delete their own account', function () {
    foreach (['tanod', 'official', 'admin'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
});

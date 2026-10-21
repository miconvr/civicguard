<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class ReportPhotoController extends Controller
{
    public function show(Report $report)
    {
        abort_unless($report->isVisibleTo(auth()->user()), 403);

        $disk = Storage::disk('report_photos');

        abort_unless($report->photo_path && $disk->exists($report->photo_path), 404);

        return $disk->response($report->photo_path, null, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function assignedTanodAvatar(Report $report)
    {
        $viewer = auth()->user();
        abort_unless($viewer->role !== 'resident' || (int) $report->user_id === (int) $viewer->id, 403);

        $tanod = $report->assignedTo;
        abort_unless($tanod && $tanod->role === 'tanod', 404);

        $path = $tanod->avatarPath();
        $disk = Storage::disk('report_photos');
        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

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
}

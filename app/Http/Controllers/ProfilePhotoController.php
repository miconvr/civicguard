<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    public function show(Request $request): StreamedResponse
    {
        $path = $request->user()->avatar_path;

        abort_unless($path && Storage::disk('report_photos')->exists($path), 404);

        return Storage::disk('report_photos')->response($path, headers: [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

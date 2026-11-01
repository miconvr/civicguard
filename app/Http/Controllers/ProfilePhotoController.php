<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    public function show(Request $request): StreamedResponse
    {
        $path = $request->user()->avatarPath();
        $version = $request->query('v');

        abort_unless(
            $path
            && ($version === null || hash_equals(basename($path), (string) $version))
            && Storage::disk('report_photos')->exists($path),
            404
        );

        return Storage::disk('report_photos')->response($path, headers: [
            'Cache-Control' => $version === null
                ? 'private, max-age=300'
                : 'private, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function showUser(Request $request, User $user): StreamedResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'official'], true), 403);

        $path = $user->avatarPath();
        $version = $request->query('v');
        abort_unless(
            $path
            && $version !== null
            && hash_equals(basename($path), (string) $version)
            && Storage::disk('report_photos')->exists($path),
            404
        );

        return Storage::disk('report_photos')->response($path, headers: [
            'Cache-Control' => 'private, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

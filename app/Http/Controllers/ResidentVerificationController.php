<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\ResidentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentVerificationController extends Controller
{
    public function show(Request $request): View
    {
        abort_unless($request->user()->role === 'resident', 403);

        $verification = $request->user()->residentVerification;

        return view('profile.verification', compact('verification'));
    }

    public function document(Request $request): StreamedResponse
    {
        abort_unless($request->user()->role === 'resident', 403);

        $verification = $request->user()->residentVerification;
        abort_unless($verification?->document_path && Storage::disk('report_photos')->exists($verification->document_path), 404);

        return Storage::disk('report_photos')->response($verification->document_path, headers: [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'resident', 403);

        $validated = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=8000,max_height=8000'],
            'consent' => ['accepted'],
        ]);

        $user = $request->user();
        $verification = $user->residentVerification;

        abort_if($verification?->status === ResidentVerification::VERIFIED, 403, __('Your residence is already verified.'));

        if ($verification?->status === ResidentVerification::PENDING) {
            return back()->withErrors(['proof' => __('Your verification request is already being reviewed.')]);
        }

        $disk = Storage::disk('report_photos');
        $directory = 'verification-documents/'.$user->id;
        $path = $request->file('proof')->storeAs(
            $directory,
            Str::uuid().'.'.$request->file('proof')->extension(),
            'report_photos'
        );

        if (! $path) {
            return back()->withInput()->withErrors(['proof' => __('The proof image could not be saved. Please try again.')]);
        }

        $previousPath = $verification?->document_path;

        try {
            DB::transaction(function () use ($user, $verification, $path) {
                $record = $verification ?? new ResidentVerification(['user_id' => $user->id]);
                $record->fill([
                    'document_path' => $path,
                    'status' => ResidentVerification::PENDING,
                    'rejection_reason' => null,
                    'consented_at' => now(),
                    'submitted_at' => now(),
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);
                $record->save();

                AppNotification::create([
                    'user_id' => $user->id,
                    'report_id' => null,
                    'message' => __('Your residence verification was submitted for review.'),
                ]);
            });
        } catch (\Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }

        if ($previousPath) {
            $disk->delete($previousPath);
        }

        return redirect()->route('profile.edit')->with('status', __('Your verification request was submitted.'));
    }
}

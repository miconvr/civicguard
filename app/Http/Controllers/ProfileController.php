<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ResidentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('residentVerification'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $disk = Storage::disk('report_photos');
        $photoDirectory = 'profile-photos/'.$user->id;
        $nameChanged = $user->name !== $validated['name'];
        $emailChanged = $user->email !== $validated['email'];
        $avatarChange = null;

        if ($request->boolean('remove_avatar') || $request->hasFile('avatar')) {
            if ($request->boolean('remove_avatar')) {
                $hadAvatar = $user->avatarPath() !== null;
                $disk->deleteDirectory($photoDirectory);

                if ($hadAvatar) {
                    $avatarChange = __('Profile picture removed successfully.');
                }
            } else {
                $avatarPath = $request->file('avatar')->storeAs(
                    $photoDirectory,
                    'avatar-'.Str::uuid().'.'.$request->file('avatar')->extension(),
                    'report_photos'
                );

                if (! $avatarPath) {
                    return Redirect::back()->withInput()->withErrors([
                        'avatar' => __('The profile picture could not be saved. Please try again.'),
                    ]);
                }

                foreach ($disk->files($photoDirectory) as $path) {
                    if ($path !== $avatarPath) {
                        $disk->delete($path);
                    }
                }

                $avatarChange = __('Profile picture uploaded successfully.');
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($avatarChange) {
            $this->notifyProfileChange($user->id, $avatarChange);
        }

        if ($nameChanged) {
            $this->notifyProfileChange($user->id, __('Name changed successfully.'));
        }

        if ($emailChanged) {
            $this->notifyProfileChange($user->id, __('Email address changed successfully.'));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function notifyProfileChange(int $userId, string $message): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'report_id' => null,
            'message' => $message,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'resident', 403, 'Staff accounts cannot be deleted here. Please contact the barangay administrator.');

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $photoPaths = Report::where('user_id', $user->id)->whereNotNull('photo_path')->pluck('photo_path');
        $verificationDocument = $user->residentVerification?->document_path;
        $verificationId = $user->residentVerification?->id;

        if ($verificationId) {
            AuditLog::where('auditable_type', ResidentVerification::class)
                ->where('auditable_id', $verificationId)
                ->update(['description' => __('Residence verification decision recorded.')]);
        }

        Auth::logout();

        $user->delete();

        $photoPaths->each(fn ($path) => Storage::disk('report_photos')->delete($path));

        Storage::disk('report_photos')->deleteDirectory('profile-photos/'.$user->id);
        Storage::disk('report_photos')->deleteDirectory('verification-documents/'.$user->id);

        if ($verificationDocument) {
            Storage::disk('report_photos')->delete($verificationDocument);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

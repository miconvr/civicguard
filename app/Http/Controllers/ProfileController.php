<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Report;
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
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $oldPhotoPath = null;

        if ($request->boolean('remove_avatar') || $request->hasFile('avatar')) {
            $oldPhotoPath = $user->avatar_path;
            $user->avatar_path = $request->boolean('remove_avatar')
                ? null
                : $request->file('avatar')->storeAs(
                    'profile-photos/'.$user->id,
                    Str::uuid().'.'.$request->file('avatar')->extension(),
                    'report_photos'
                );
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($oldPhotoPath) {
            Storage::disk('report_photos')->delete($oldPhotoPath);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
        $avatarPath = $user->avatar_path;

        Auth::logout();

        $user->delete();

        $photoPaths->each(fn ($path) => Storage::disk('report_photos')->delete($path));

        if ($avatarPath) {
            Storage::disk('report_photos')->delete($avatarPath);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

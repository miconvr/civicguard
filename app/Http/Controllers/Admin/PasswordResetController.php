<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function index(): View
    {
        $residents = User::where('role', 'resident')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.password-resets.index', compact('residents'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'resident', 404);

        $validated = $request->validateWithBag('reset-'.$user->id, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'resident_password_reset',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'description' => __('Resident password reset by authorized staff.'),
            'metadata' => [
                'resident_id' => $user->id,
            ],
        ]);

        return redirect()
            ->route('admin.passwordResets.index')
            ->with('status', __('Resident password reset successfully.'));
    }
}

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

class StaffPasswordResetController extends Controller
{
    /**
     * Admins reset tanods and officials. Officials reset tanods only.
     * Admin passwords are never reset here.
     *
     * @return list<string>
     */
    private function resettableRoles(User $actor): array
    {
        return $actor->role === 'admin' ? ['tanod', 'official'] : ['tanod'];
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $staffMembers = User::whereIn('role', $this->resettableRoles($request->user()))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.staff-password-resets.index', compact('staffMembers', 'search'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, $this->resettableRoles($request->user()), true), 404);

        $validated = $request->validateWithBag('reset-'.$user->id, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'remember_token' => Str::random(60),
        ])->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'staff_password_reset',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'description' => __('Staff password reset by authorized staff.'),
            'metadata' => [
                'staff_id' => $user->id,
                'role' => $user->role,
            ],
        ]);

        return redirect()
            ->route('admin.staffPasswordResets.index')
            ->with('status', __('Staff password reset successfully.'));
    }
}

<?php

namespace App\Http\Requests\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Throwable;

class StaffLoginRequest extends LoginRequest
{
    /**
     * Attempt to authenticate a staff member. Non-staff accounts fail with
     * the same generic message as a wrong password.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password')
            + [fn ($query) => $query->whereIn('role', User::STAFF_ROLES)];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $known = User::whereIn('role', User::STAFF_ROLES)
                ->where('email', (string) $this->input('email'))
                ->first();

            $this->record('staff_login_failed', __('Failed staff sign-in attempt.'), null, $known);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $this->record('staff_login', __('Staff signed in.'), Auth::id(), Auth::user());
    }

    public function throttleKey(): string
    {
        return 'staff|'.parent::throttleKey();
    }

    /**
     * Write an audit entry. A logging problem must never block sign-in.
     */
    private function record(string $action, string $description, ?int $actorId, ?User $subject): void
    {
        try {
            AuditLog::create([
                'user_id' => $actorId,
                'action' => $action,
                'auditable_type' => $subject ? User::class : null,
                'auditable_id' => $subject?->id,
                'description' => $description,
                'metadata' => ['ip' => $this->ip()],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}

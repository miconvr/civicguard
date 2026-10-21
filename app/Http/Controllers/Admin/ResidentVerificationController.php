<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\ResidentVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        $search = trim($validated['search'] ?? '');
        $status = $validated['status'] ?? ResidentVerification::PENDING;
        $counts = ResidentVerification::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $verifications = ResidentVerification::with(['user', 'reviewer'])
            ->where('status', $status)
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->oldest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.verifications.index', compact('verifications', 'search', 'status', 'counts'));
    }

    public function document(ResidentVerification $verification): StreamedResponse
    {
        abort_unless($verification->status === ResidentVerification::PENDING, 404);
        abort_unless($verification->document_path && Storage::disk('report_photos')->exists($verification->document_path), 404);

        return Storage::disk('report_photos')->response($verification->document_path, headers: [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function avatar(ResidentVerification $verification): StreamedResponse
    {
        $path = $verification->user->avatarPath();
        abort_unless($path && Storage::disk('report_photos')->exists($path), 404);

        return Storage::disk('report_photos')->response($path, headers: [
            // Avatars are private, but short browser caching avoids re-reading
            // the same file when staff switch between verification tabs.
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function decide(Request $request, ResidentVerification $verification): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:verified,rejected'],
        ]);

        $decision = $validated['decision'];

        $documentPath = DB::transaction(function () use ($verification, $decision, $request) {
            $verification = ResidentVerification::query()
                ->whereKey($verification->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($verification->status === ResidentVerification::PENDING, 409, __('This request has already been reviewed.'));

            $user = $verification->user;
            $documentPath = $verification->document_path;
            abort_unless($documentPath && Storage::disk('report_photos')->exists($documentPath), 409, __('The proof image is no longer available.'));

            $verification->update([
                'status' => $decision,
                'document_path' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $decision === ResidentVerification::VERIFIED ? 'resident_verified' : 'residence_verification_rejected',
                'auditable_type' => ResidentVerification::class,
                'auditable_id' => $verification->id,
                'description' => $decision === ResidentVerification::VERIFIED
                    ? __('Residence verification approved for :name (:email).', ['name' => $user->name, 'email' => $user->email])
                    : __('Residence verification declined for :name (:email).', ['name' => $user->name, 'email' => $user->email]),
            ]);

            AppNotification::create([
                'user_id' => $user->id,
                'report_id' => null,
                'message' => $decision === ResidentVerification::VERIFIED
                    ? __('Your Maimpis residence has been verified.')
                    : __('Your verification could not be approved. You may submit another proof image.'),
            ]);

            return $documentPath;
        });

        Storage::disk('report_photos')->deleteDirectory('verification-documents/'.$verification->user_id);

        return redirect()->route('admin.verifications.index')->with(
            'status',
            $decision === ResidentVerification::VERIFIED
                ? __('Resident verified.')
                : __('Verification request declined.')
        );
    }
}

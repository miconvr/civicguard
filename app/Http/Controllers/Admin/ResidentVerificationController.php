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

        $descriptions = [
            ResidentVerification::PENDING => __('Review submitted proof images. Verified residents receive a permanent badge. Submitted images are deleted after a decision.'),
            ResidentVerification::VERIFIED => __('Previously verified residents are listed here. Their proof images were deleted after review.'),
            ResidentVerification::REJECTED => __('Declined requests are kept here for review history. Their proof images were deleted after the decision.'),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $status,
                'description' => $descriptions[$status],
                'html' => view('admin.verifications.partials.results', compact('verifications', 'search', 'status'))->render(),
            ]);
        }

        return view('admin.verifications.index', compact('verifications', 'search', 'status', 'counts', 'descriptions'));
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

    public function avatar(Request $request, ResidentVerification $verification): StreamedResponse
    {
        $path = $verification->user->avatarPath();
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

    public function decide(Request $request, ResidentVerification $verification): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:verified,rejected'],
            'rejection_reason' => ['required_if:decision,rejected', 'nullable', 'string', 'max:500'],
        ]);

        $decision = $validated['decision'];

        $documentPath = DB::transaction(function () use ($verification, $decision, $request, $validated) {
            $verification = ResidentVerification::query()
                ->whereKey($verification->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($verification->status === ResidentVerification::PENDING, 409, __('This request has already been reviewed.'));

            $user = $verification->user;
            abort_unless($verification->document_path && Storage::disk('report_photos')->exists($verification->document_path), 409, __('The proof image is no longer available.'));
            $documentPath = $verification->document_path;

            $verification->update([
                'document_path' => null,
                'status' => $decision,
                'rejection_reason' => $decision === ResidentVerification::REJECTED ? trim($validated['rejection_reason']) : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $decision === ResidentVerification::VERIFIED ? 'resident_verified' : 'residence_verification_rejected',
                'auditable_type' => ResidentVerification::class,
                'auditable_id' => $verification->id,
                'description' => $decision === ResidentVerification::VERIFIED
                    ? __('Residence verification approved.')
                    : __('Residence verification declined.'),
            ]);

            AppNotification::create([
                'user_id' => $user->id,
                'report_id' => null,
                'verification_id' => $verification->id,
                'message' => $decision === ResidentVerification::VERIFIED
                    ? __('Your Maimpis residence has been verified.')
                    : __('Your residence verification was declined. Reason: :reason', ['reason' => trim($validated['rejection_reason'])]),
            ]);

            return $documentPath;
        });

        Storage::disk('report_photos')->delete($documentPath);

        return redirect()->route('admin.verifications.index')->with(
            'status',
            $decision === ResidentVerification::VERIFIED
                ? __('Resident verified.')
                : __('Verification request declined.')
        );
    }
}

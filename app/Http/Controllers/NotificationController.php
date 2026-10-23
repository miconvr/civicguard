<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', Auth::id())
            ->with('report')
            ->latest()
            ->paginate(20);

        $unread = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unread'));
    }

    public function open(AppNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->update(['is_read' => true]);

        $report = $notification->report;
        $verification = $notification->verification;

        if ($verification && Auth::user()->role === 'resident' && (int) $verification->user_id === (int) Auth::id()) {
            return redirect()->route('profile.verification.show');
        }

        if (! $report) {
            return redirect()->route('notifications.index');
        }

        $user = Auth::user();

        if ($report->user_id === $user->id) {
            return redirect()->to(route('reports.index').'#report-'.$report->id);
        }

        if (in_array($user->role, ['tanod', 'admin', 'official'])) {
            return redirect()->route('admin.reports.details', $report);
        }

        return redirect()->route('notifications.index');
    }

    public function readAll(Request $request)
    {
        AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.index');
    }

    public function clear()
    {
        AppNotification::where('user_id', Auth::id())->delete();

        return redirect()->route('notifications.index')->with('status', __('Notifications cleared.'));
    }
}

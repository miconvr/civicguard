<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
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
        if (! $report) {
            return redirect()->route('notifications.index');
        }

        $user = Auth::user();

        if ($report->user_id === $user->id) {
            return redirect()->to(route('reports.index') . '#report-' . $report->id);
        }

        if (in_array($user->role, ['tanod', 'admin', 'official'])) {
            return redirect()->route('admin.reports.details', $report);
        }

        return redirect()->route('notifications.index');
    }

    public function readAll()
    {
        AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('notifications.index');
    }
}

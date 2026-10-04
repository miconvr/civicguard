<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $open = ['pending', 'in_progress'];
        $bySeverity = "FIELD(severity, 'critical', 'high', 'moderate', 'low')";
        $data = ['user' => $user];

        if ($user->role === 'resident') {
            $data['recent'] = Report::where('user_id', $user->id)->with('category')->latest()->limit(3)->get();
            $data['needsFeedback'] = Report::where('user_id', $user->id)
                ->where('status', 'resolved')
                ->whereNull('confirmed_at')
                ->where('resolved_at', '>', now()->subDays(7))
                ->count();
        } elseif ($user->role === 'tanod') {
            $data['assigned'] = Report::with('category')
                ->where('assigned_to', $user->id)
                ->whereIn('status', $open)
                ->orderByRaw($bySeverity)
                ->oldest()
                ->limit(10)
                ->get();
        } else {
            $data['stats'] = [
                'critical' => Report::whereIn('status', $open)->where('severity', 'critical')->count(),
                'unassigned' => Report::whereIn('status', $open)->whereNull('assigned_to')->count(),
                'overdue' => Report::where('status', 'pending')->where('created_at', '<', now()->subHours(48))->count(),
            ];
            $data['urgent'] = Report::with('category')
                ->whereIn('status', $open)
                ->orderByRaw($bySeverity)
                ->oldest()
                ->limit(5)
                ->get();
        }

        return view('home', $data);
    }
}

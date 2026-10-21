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
        $data = ['user' => $user];

        if ($user->role === 'resident') {
            $data['recent'] = Report::where('user_id', $user->id)->with('category')->latest()->limit(3)->get();
            $numbers = Report::where('user_id', $user->id)->orderBy('created_at')->orderBy('id')->pluck('id')->flip();
            foreach ($data['recent'] as $recentReport) {
                $recentReport->resident_number = $numbers[$recentReport->id] + 1;
            }
            $data['needsFeedback'] = Report::where('user_id', $user->id)
                ->where('status', 'resolved')
                ->whereNull('confirmed_at')
                ->where('resolved_at', '>', now()->subDays(7))
                ->count();
        } elseif ($user->role === 'tanod') {
            $data['assigned'] = Report::with(['category', 'user:id,name'])
                ->where('assigned_to', $user->id)
                ->whereIn('status', $open)
                ->orderByUrgency()
                ->oldest()
                ->limit(10)
                ->get();
        } else {
            $data['stats'] = [
                'critical' => Report::whereIn('status', $open)->where('severity', 'critical')->count(),
                'unassigned' => Report::whereIn('status', $open)->whereNull('assigned_to')->count(),
                'overdue' => Report::where('status', 'pending')->where('created_at', '<', now()->subHours(48))->count(),
            ];
            $data['urgent'] = Report::with(['category', 'user:id,name'])
                ->whereIn('status', $open)
                ->orderByUrgency()
                ->oldest()
                ->limit(5)
                ->get();
        }

        \App\Support\ResidentNumbers::assign($data['assigned'] ?? collect());
        \App\Support\ResidentNumbers::assign($data['urgent'] ?? collect());

        return view('home', $data);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['category', 'user', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $reports = $query->latest()->paginate(15);

        return view('admin.dashboard', compact('reports'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,resolved'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        return redirect()->back()->with('status', 'Report status updated.');
    }
}
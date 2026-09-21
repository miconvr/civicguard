<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
       public function index(Request $request)
    {
        $query = Report::with(['category', 'user', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'resolved');
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $reports = $query->latest()->paginate(15);
        $tanods = \App\Models\User::where('role', 'tanod')->get();

        $resolvedReports = Report::with(['category', 'user', 'assignedTo'])
            ->where('status', 'resolved')
            ->latest('resolved_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('reports', 'tanods', 'resolvedReports'));
    }

    public function exportReportsPdf(Request $request)
    {
        $isResolved = $request->query('group') === 'resolved';

        $query = Report::with(['category', 'user', 'assignedTo']);

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($isResolved) {
            $reports = $query->where('status', 'resolved')->latest('resolved_at')->get();
            $title = 'Resolved Reports';
            $filename = 'civicguard-resolved-reports-' . now()->format('Y-m-d') . '.pdf';
        } else {
            $reports = $query->where('status', '!=', 'resolved')->latest()->get();
            $title = 'Pending & In Progress Reports';
            $filename = 'civicguard-active-reports-' . now()->format('Y-m-d') . '.pdf';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports-pdf', compact('reports', 'title', 'isResolved'));

        return $pdf->download($filename);
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

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $validated['status'] === 'resolved' ? 'report_resolved' : 'report_status_changed',
            'auditable_type' => Report::class,
            'auditable_id' => $report->id,
            'description' => 'Report status changed to ' . str_replace('_', ' ', $validated['status']) . '.',
            'metadata' => [
                'status' => $validated['status'],
            ],
        ]);

        \App\Models\AppNotification::create([
            'user_id' => $report->user_id,
            'report_id' => $report->id,
            'message' => "Your report (\"{$report->category->name}\") status changed to " . ucfirst(str_replace('_', ' ', $validated['status'])) . '.',
        ]);

        return redirect()->back()->with('status', 'Report status updated.');
    }

    public function assign(Request $request, Report $report)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $report->update(['assigned_to' => $validated['assigned_to']]);

        \App\Models\CaseAssignment::create([
            'report_id' => $report->id,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'report_assigned',
            'auditable_type' => Report::class,
            'auditable_id' => $report->id,
            'description' => 'Report assigned to a tanod.',
            'metadata' => [
                'assigned_to' => $validated['assigned_to'],
            ],
        ]);

        \App\Models\AppNotification::create([
            'user_id' => $validated['assigned_to'],
            'report_id' => $report->id,
            'message' => "You've been assigned to a report: \"{$report->category->name}\" at {$report->location_text}.",
        ]);

        return redirect()->back()->with('status', 'Tanod assigned successfully.');
    }

    public function showCurfewDetails(Report $report)
    {
        $report->load('curfewLog');
        return view('admin.curfew-details', compact('report'));
    }
}
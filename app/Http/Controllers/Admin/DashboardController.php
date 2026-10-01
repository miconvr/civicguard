<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'active');
        $open = ['pending', 'in_progress'];

        $query = Report::with(['category', 'user', 'assignedTo'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($q) => $q
                    ->where('location_text', 'like', "%{$s}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$s}%")));
            })
            ->when($tab === 'active', fn ($q) => $q->whereIn('status', $open))
            ->when(in_array($tab, ['pending', 'in_progress', 'resolved']), fn ($q) => $q->where('status', $tab))
            ->when($request->filled('severity'), fn ($q) => $q->where('severity', $request->severity))
            ->when($request->assignee === 'none', fn ($q) => $q->whereNull('assigned_to'));

        if ($tab === 'resolved') {
            $query->latest('resolved_at');
        } else {
            $query->orderByRaw("FIELD(severity, 'critical', 'high', 'moderate', 'low')")->oldest();
        }

        $stats = [
            'critical'   => Report::whereIn('status', $open)->where('severity', 'critical')->count(),
            'unassigned' => Report::whereIn('status', $open)->whereNull('assigned_to')->count(),
            'overdue'    => Report::where('status', 'pending')->where('created_at', '<', now()->subHours(48))->count(),
            'resolved'   => Report::where('status', 'resolved')->where('resolved_at', '>=', now()->startOfWeek())->count(),
        ];

        return view('admin.dashboard', [
            'reports' => $query->paginate(15)->withQueryString(),
            'tanods'  => \App\Models\User::where('role', 'tanod')->get(['id', 'name']),
            'stats'   => $stats,
            'tab'     => $tab,
        ]);
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

        $from = $report->status;

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
                'from' => $from,
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
            'assigned_to' => ['required', Rule::exists('users', 'id')->where('role', 'tanod')],
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

    public function showDetails(Report $report)
    {
        $report->load(['category', 'user', 'assignedTo', 'curfewLog']);
        return view('admin.report-details', compact('report'));
    }

    public function showCurfewDetails(Report $report)
    {
        $report->load('curfewLog');
        return view('admin.curfew-details', compact('report'));
    }
}

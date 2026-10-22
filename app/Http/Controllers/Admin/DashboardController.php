<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\CaseAssignment;
use App\Models\Report;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'active');
        $open = ['pending', 'in_progress'];

        $query = Report::with(['category', 'user.residentVerification', 'assignedTo'])
            ->visibleTo($request->user())
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
            $query->orderByUrgency()->oldest();
        }

        $visibleReports = Report::visibleTo($request->user());
        $stats = [
            'critical' => (clone $visibleReports)->whereIn('status', $open)->where('severity', 'critical')->count(),
            'unassigned' => (clone $visibleReports)->whereIn('status', $open)->whereNull('assigned_to')->count(),
            'overdue' => (clone $visibleReports)->where('status', 'pending')->where('created_at', '<', now()->subHours(48))->count(),
            'resolved' => (clone $visibleReports)->where('status', 'resolved')->where('resolved_at', '>=', now()->startOfWeek())->count(),
        ];

        return view('admin.dashboard', [
            'reports' => $query->paginate(15)->withQueryString(),
            'tanods' => User::where('role', 'tanod')->get(['id', 'name']),
            'stats' => $stats,
            'tab' => $tab,
        ]);
    }

    public function exportReportsPdf(Request $request)
    {
        $isResolved = $request->query('group') === 'resolved';

        $query = Report::with(['category', 'user', 'assignedTo'])->visibleTo($request->user());

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($isResolved) {
            $reports = $query->where('status', 'resolved')->latest('resolved_at')->get();
            $title = 'Resolved Reports';
            $filename = 'civicguard-resolved-reports-'.now()->format('Y-m-d').'.pdf';
        } else {
            $reports = $query->where('status', '!=', 'resolved')->latest()->get();
            $title = 'Pending & In Progress Reports';
            $filename = 'civicguard-active-reports-'.now()->format('Y-m-d').'.pdf';
        }

        $pdf = Pdf::loadView('admin.reports-pdf', compact('reports', 'title', 'isResolved'));

        return $pdf->download($filename);
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,resolved'],
        ]);

        if (auth()->user()->role === 'tanod' && (int) $report->assigned_to !== (int) auth()->id()) {
            abort(403, 'You can only update reports assigned to you.');
        }

        $from = $report->status;

        $report->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
            'confirmed_at' => null,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $validated['status'] === 'resolved' ? 'report_resolved' : 'report_status_changed',
            'auditable_type' => Report::class,
            'auditable_id' => $report->id,
            'description' => 'Report status changed to '.str_replace('_', ' ', $validated['status']).'.',
            'metadata' => [
                'from' => $from,
                'status' => $validated['status'],
            ],
        ]);

        AppNotification::create([
            'user_id' => $report->user_id,
            'report_id' => $report->id,
            'message' => "Your report (\"{$report->category->name}\") status changed to ".ucfirst(str_replace('_', ' ', $validated['status'])).'.',
        ]);

        return redirect()->back()->with('status', __('Report status updated.'));
    }

    public function updateQueueReport(Request $request, Report $report)
    {
        $actor = $request->user();
        abort_unless($report->isVisibleTo($actor), 403);

        $canManage = in_array($actor->role, ['admin', 'official'], true);
        $canUpdateStatus = $canManage || (int) $report->assigned_to === (int) $actor->id;
        abort_unless($canManage || $canUpdateStatus, 403);

        $rules = [];
        if ($request->exists('status')) {
            abort_unless($canUpdateStatus, 403);
            $rules['status'] = ['required', 'in:pending,in_progress,resolved'];
        }
        if ($request->exists('severity')) {
            abort_unless($canManage, 403);
            $rules['severity'] = ['required', 'in:low,moderate,high,critical'];
        }
        if ($request->exists('assigned_to')) {
            abort_unless($canManage, 403);
            $rules['assigned_to'] = ['nullable', Rule::exists('users', 'id')->where('role', 'tanod')];
        }

        $validated = $request->validate($rules);
        $report->loadMissing('category');

        DB::transaction(function () use ($actor, $canManage, $report, $validated) {
            if (isset($validated['severity']) && $report->severity !== $validated['severity']) {
                $from = $report->severity;
                $report->severity = $validated['severity'];
                AuditLog::create([
                    'user_id' => $actor->id,
                    'action' => 'report_severity_changed',
                    'auditable_type' => Report::class,
                    'auditable_id' => $report->id,
                    'description' => "Severity changed from {$from} to {$validated['severity']}.",
                    'metadata' => ['from' => $from, 'to' => $validated['severity']],
                ]);
            }

            if (isset($validated['status']) && $report->status !== $validated['status']) {
                $from = $report->status;
                $report->status = $validated['status'];
                $report->resolved_at = $validated['status'] === 'resolved' ? now() : null;
                $report->confirmed_at = null;
                AuditLog::create([
                    'user_id' => $actor->id,
                    'action' => $validated['status'] === 'resolved' ? 'report_resolved' : 'report_status_changed',
                    'auditable_type' => Report::class,
                    'auditable_id' => $report->id,
                    'description' => 'Report status changed to '.str_replace('_', ' ', $validated['status']).'.',
                    'metadata' => ['from' => $from, 'status' => $validated['status']],
                ]);
                AppNotification::create([
                    'user_id' => $report->user_id,
                    'report_id' => $report->id,
                    'message' => "Your report (\"{$report->category->name}\") status changed to ".ucfirst(str_replace('_', ' ', $validated['status'])).'.',
                ]);
            }

            if ($canManage && ! empty($validated['assigned_to'])) {
                $tanod = User::findOrFail($validated['assigned_to']);
                $previousAssigneeId = $report->assigned_to ? (int) $report->assigned_to : null;

                if ($previousAssigneeId !== (int) $tanod->id) {
                    $report->assigned_to = $tanod->id;
                    CaseAssignment::create([
                        'report_id' => $report->id,
                        'assigned_to' => $tanod->id,
                        'assigned_by' => $actor->id,
                        'assigned_at' => now(),
                        'status' => 'assigned',
                    ]);
                    AuditLog::create([
                        'user_id' => $actor->id,
                        'action' => 'report_assigned',
                        'auditable_type' => Report::class,
                        'auditable_id' => $report->id,
                        'description' => 'Report assigned to '.$tanod->name.'.',
                        'metadata' => ['from' => $previousAssigneeId, 'assigned_to' => $tanod->id, 'assigned_name' => $tanod->name],
                    ]);

                    AppNotification::create([
                        'user_id' => $tanod->id,
                        'report_id' => $report->id,
                        'message' => "You've been assigned to a report: \"{$report->category->name}\" at {$report->location_text}.",
                    ]);
                    AppNotification::create([
                        'user_id' => $report->user_id,
                        'report_id' => $report->id,
                        'message' => __('A tanod has been assigned to your report: :name.', ['name' => $tanod->name]),
                    ]);
                }
            }

            $report->save();
        });

        return redirect()->back()->with('status', __('Report changes saved.'));
    }

    public function updateSeverity(Request $request, Report $report)
    {
        $validated = $request->validate([
            'severity' => ['required', 'in:low,moderate,high,critical'],
        ]);

        $from = $report->severity;

        if ($from !== $validated['severity']) {
            $report->update(['severity' => $validated['severity']]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'report_severity_changed',
                'auditable_type' => Report::class,
                'auditable_id' => $report->id,
                'description' => "Severity changed from {$from} to {$validated['severity']}.",
                'metadata' => [
                    'from' => $from,
                    'to' => $validated['severity'],
                ],
            ]);
        }

        return redirect()->back()->with('status', __('Severity updated.'));
    }

    public function assign(Request $request, Report $report)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', Rule::exists('users', 'id')->where('role', 'tanod')],
        ]);

        $previousAssigneeId = $report->assigned_to ? (int) $report->assigned_to : null;
        $assignedTanod = User::findOrFail($validated['assigned_to']);
        $report->update(['assigned_to' => $assignedTanod->id]);

        CaseAssignment::create([
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
            'description' => 'Report assigned to '.$assignedTanod->name.'.',
            'metadata' => [
                'from' => $previousAssigneeId,
                'assigned_to' => $assignedTanod->id,
                'assigned_name' => $assignedTanod->name,
            ],
        ]);

        if ($previousAssigneeId !== (int) $assignedTanod->id) {
            AppNotification::create([
                'user_id' => $assignedTanod->id,
                'report_id' => $report->id,
                'message' => "You've been assigned to a report: \"{$report->category->name}\" at {$report->location_text}.",
            ]);

            AppNotification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'message' => __('A tanod has been assigned to your report: :name.', ['name' => $assignedTanod->name]),
            ]);
        }

        return redirect()->back()->with('status', __('Tanod assigned successfully.'));
    }

    public function showDetails(Report $report)
    {
        abort_unless($report->isVisibleTo(auth()->user()), 403);

        $report->load(['category', 'user', 'assignedTo', 'curfewLog']);

        $history = AuditLog::with('user:id,name')
            ->where('auditable_type', Report::class)
            ->where('auditable_id', $report->id)
            ->oldest()
            ->get();

        $tanods = in_array(auth()->user()->role, ['admin', 'official'], true)
            ? User::where('role', 'tanod')->get(['id', 'name'])
            : collect();

        return view('admin.report-details', compact('report', 'history', 'tanods'));
    }

    public function showCurfewDetails(Report $report)
    {
        abort_unless($report->isVisibleTo(auth()->user()), 403);

        $report->load('curfewLog');

        return view('admin.curfew-details', compact('report'));
    }
}

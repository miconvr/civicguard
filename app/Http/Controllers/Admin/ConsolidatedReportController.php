<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CurfewLog;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsolidatedReportController extends Controller
{
    public function index()
    {
        return view('admin.consolidated-reports', $this->reportData());
    }

    public function exportPdf()
    {
        return Pdf::loadView('admin.consolidated-reports-pdf', $this->reportData())
            ->download('civicguard-consolidated-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function reportData(): array
    {
        $reports = Report::with(['category', 'user', 'assignedTo', 'curfewLog'])
            ->latest()
            ->get();
        $curfewLogs = CurfewLog::with(['report.category', 'tanod'])
            ->latest('apprehension_datetime')
            ->get();
        $auditLogs = AuditLog::with('user')
            ->latest()
            ->limit(100)
            ->get();

        $resolvedReports = $reports->filter(fn (Report $report) => $report->status === 'resolved' && $report->resolved_at);
        $responseTimes = $resolvedReports->map(
            fn (Report $report) => $report->created_at->diffInMinutes($report->resolved_at)
        );

        return [
            'reports' => $reports,
            'curfewLogs' => $curfewLogs,
            'auditLogs' => $auditLogs,
            'totalReports' => $reports->count(),
            'resolvedCount' => $resolvedReports->count(),
            'assignedCount' => $reports->whereNotNull('assigned_to')->count(),
            'curfewCount' => $curfewLogs->count(),
            'averageResponseHours' => $responseTimes->isNotEmpty()
                ? round($responseTimes->average() / 60, 1)
                : null,
        ];
    }
}

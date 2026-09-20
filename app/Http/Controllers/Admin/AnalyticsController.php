<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $byCategory = Report::join('report_categories', 'reports.category_id', '=', 'report_categories.id')
            ->select('report_categories.name', DB::raw('count(*) as total'))
            ->groupBy('report_categories.name')
            ->orderByDesc('total')
            ->get();

        $bySeverity = Report::select('severity', DB::raw('count(*) as total'))
            ->groupBy('severity')
            ->get()
            ->keyBy('severity');

        $byStatus = Report::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $last14Days = Report::where('created_at', '>=', now()->subDays(14))
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $totalReports = Report::count();
        $pendingCount = $byStatus->get('pending')->total ?? 0;
        $resolvedCount = $byStatus->get('resolved')->total ?? 0;

        return view('admin.analytics', compact(
            'byCategory', 'bySeverity', 'byStatus', 'last14Days',
            'totalReports', 'pendingCount', 'resolvedCount'
        ));
    }
}
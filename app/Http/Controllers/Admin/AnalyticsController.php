<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurfewLog;
use App\Models\Report;
use App\Services\GeminiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(GeminiClient $gemini)
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
        $curfewCount = CurfewLog::count();

        // Fixed cache key with a real time window, so repeated visits actually reuse the cached AI result
        // instead of regenerating every time the underlying data changes by even one row.
        $insights = Cache::remember('analytics-insights', now()->addMinutes(30), fn () => $this->buildInsights(
            $byCategory,
            $bySeverity,
            $byStatus,
            $last14Days,
            $curfewCount,
            $gemini
        ));

        $recommendations = $insights['recommendations'];
        $analysisSummary = $insights['analysisSummary'];
        $patterns = $insights['patterns'];
        $riskFlags = $insights['riskFlags'];

        return view('admin.analytics', compact(
            'byCategory', 'bySeverity', 'byStatus', 'last14Days',
            'totalReports', 'pendingCount', 'resolvedCount', 'curfewCount',
            'recommendations', 'analysisSummary', 'patterns', 'riskFlags'
        ));
    }

    public function exportPdf(GeminiClient $gemini)
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
        $curfewCount = CurfewLog::count();

        $insights = Cache::remember('analytics-insights', now()->addMinutes(30), fn () => $this->buildInsights(
            $byCategory, $bySeverity, $byStatus, $last14Days, $curfewCount, $gemini
        ));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.analytics-pdf', [
            'byCategory' => $byCategory,
            'bySeverity' => $bySeverity,
            'byStatus' => $byStatus,
            'last14Days' => $last14Days,
            'totalReports' => $totalReports,
            'pendingCount' => $pendingCount,
            'resolvedCount' => $resolvedCount,
            'curfewCount' => $curfewCount,
            'recommendations' => $insights['recommendations'],
            'analysisSummary' => $insights['analysisSummary'],
            'patterns' => $insights['patterns'],
            'riskFlags' => $insights['riskFlags'],
        ]);

        return $pdf->download('civicguard-analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    private function buildInsights($byCategory, $bySeverity, $byStatus, $last14Days, int $curfewCount, GeminiClient $gemini): array
    {
        $metrics = [
            'total_reports' => $byCategory->sum('total'),
            'category_totals' => $byCategory->pluck('total', 'name'),
            'severity_totals' => $bySeverity->map(fn ($item) => $item->total),
            'status_totals' => $byStatus->map(fn ($item) => $item->total),
            'reports_last_14_days' => $last14Days->sum('total'),
            'curfew_logs' => $curfewCount,
        ];

        $prompt = "You are analyzing CivicGuard data for barangay officials. Based only on these metrics, return valid JSON with exactly these keys: summary (one concise sentence), patterns (an array of up to three observed patterns), risk_flags (an array of up to three risks), and recommendations (an array of exactly three concise practical follow-up actions). Do not include markdown or extra text. Metrics: " . json_encode($metrics);

        $text = $gemini->generateText($prompt, 10);

        if ($text) {
            $decoded = json_decode(trim($text), true);

            if (
                is_array($decoded)
                && is_string($decoded['summary'] ?? null)
                && is_array($decoded['patterns'] ?? null)
                && is_array($decoded['risk_flags'] ?? null)
                && is_array($decoded['recommendations'] ?? null)
            ) {
                return [
                    'recommendations' => array_values(array_slice(array_filter($decoded['recommendations'], 'is_string'), 0, 3)),
                    'analysisSummary' => $decoded['summary'],
                    'patterns' => array_values(array_slice(array_filter($decoded['patterns'], 'is_string'), 0, 3)),
                    'riskFlags' => array_values(array_slice(array_filter($decoded['risk_flags'], 'is_string'), 0, 3)),
                ];
            }
        }

        return $this->fallbackInsights($byCategory, $bySeverity, $byStatus, $last14Days, $curfewCount);
    }

    private function fallbackInsights($byCategory, $bySeverity, $byStatus, $last14Days, int $curfewCount): array
    {
        $recommendations = [];
        $patterns = [];
        $riskFlags = [];
        $pendingCount = $byStatus->get('pending')->total ?? 0;
        $criticalCount = ($bySeverity->get('critical')->total ?? 0) + ($bySeverity->get('high')->total ?? 0);
        $recentCount = $last14Days->sum('total');
        $topCategory = $byCategory->first();

        if ($topCategory) {
            $patterns[] = "{$topCategory->name} is the most frequently reported category with {$topCategory->total} report(s).";
        }

        $patterns[] = "{$recentCount} report(s) were filed during the last 14 days.";

        if ($curfewCount > 0) {
            $patterns[] = "Curfew monitoring contains {$curfewCount} logged violation(s).";
        }

        if ($pendingCount > 0) {
            $riskFlags[] = "{$pendingCount} report(s) remain pending.";
        }

        if ($criticalCount > 0) {
            $riskFlags[] = "{$criticalCount} report(s) are high or critical severity.";
        }

        if ($curfewCount > 0) {
            $riskFlags[] = 'Curfew records should be reviewed for repeat violations and safeguarding needs.';
        }

        if ($pendingCount > 0) {
            $recommendations[] = "Review {$pendingCount} pending report(s) and assign urgent cases first.";
        }

        if ($criticalCount > 0) {
            $recommendations[] = "Prioritize {$criticalCount} high or critical report(s) for follow-up.";
        }

        if ($curfewCount > 0) {
            $recommendations[] = "Review {$curfewCount} curfew log(s) for repeat violations and appropriate support.";
        }

        if (count($recommendations) < 3) {
            $recommendations[] = "Continue monitoring the {$recentCount} report(s) recorded in the last 14 days for emerging patterns.";
        }

        return [
            'recommendations' => array_slice($recommendations, 0, 3),
            'analysisSummary' => "CivicGuard recorded {$recentCount} report(s) in the last 14 days with {$pendingCount} still pending.",
            'patterns' => array_slice($patterns, 0, 3),
            'riskFlags' => array_slice($riskFlags, 0, 3),
        ];
    }
}
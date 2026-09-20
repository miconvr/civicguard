<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurfewLog;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        $curfewCount = CurfewLog::count();
        $recommendations = $this->buildRecommendations(
            $byCategory,
            $bySeverity,
            $byStatus,
            $last14Days,
            $curfewCount
        );

        return view('admin.analytics', compact(
            'byCategory', 'bySeverity', 'byStatus', 'last14Days',
            'totalReports', 'pendingCount', 'resolvedCount', 'curfewCount',
            'recommendations'
        ));
    }

    private function buildRecommendations($byCategory, $bySeverity, $byStatus, $last14Days, int $curfewCount): array
    {
        $metrics = [
            'total_reports' => $byCategory->sum('total'),
            'category_totals' => $byCategory->pluck('total', 'name'),
            'severity_totals' => $bySeverity->map(fn ($item) => $item->total),
            'status_totals' => $byStatus->map(fn ($item) => $item->total),
            'reports_last_14_days' => $last14Days->sum('total'),
            'curfew_logs' => $curfewCount,
        ];

        $apiKey = config('services.gemini.key');

        if ($apiKey) {
            try {
                $response = Http::timeout(8)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [[
                            'role' => 'user',
                            'parts' => [[
                                'text' => "You are advising barangay officials. Based only on these CivicGuard metrics, return exactly three concise, practical follow-up recommendations as a JSON array of strings. Do not include markdown or extra text. Metrics: " . json_encode($metrics),
                            ]],
                        ]],
                    ]
                );

                $text = trim($response->json('candidates.0.content.parts.0.text') ?? '');
                $decoded = json_decode($text, true);

                if (is_array($decoded) && count($decoded) > 0 && collect($decoded)->every('is_string')) {
                    return array_values(array_slice($decoded, 0, 3));
                }
            } catch (\Throwable $exception) {
                // Use local recommendations when the AI service is unavailable.
            }
        }

        return $this->fallbackRecommendations($bySeverity, $byStatus, $last14Days, $curfewCount);
    }

    private function fallbackRecommendations($bySeverity, $byStatus, $last14Days, int $curfewCount): array
    {
        $recommendations = [];
        $pendingCount = $byStatus->get('pending')->total ?? 0;
        $criticalCount = ($bySeverity->get('critical')->total ?? 0) + ($bySeverity->get('high')->total ?? 0);
        $recentCount = $last14Days->sum('total');

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

        return array_slice($recommendations, 0, 3);
    }
}
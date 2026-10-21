<?php

namespace App\Support;

use App\Models\Report;
use Illuminate\Pagination\AbstractPaginator;

class ResidentNumbers
{
    /** Sets ->resident_number on each report: its position among that resident's own reports. */
    public static function assign($reports): void
    {
        $items = $reports instanceof AbstractPaginator ? $reports->getCollection() : collect($reports);
        $userIds = $items->pluck('user_id')->unique()->values();

        if ($userIds->isEmpty()) {
            return;
        }

        $positions = Report::whereIn('user_id', $userIds)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'user_id'])
            ->groupBy('user_id')
            ->map(fn ($group) => $group->pluck('id')->values()->flip());

        foreach ($items as $report) {
            $report->resident_number = ($positions->get($report->user_id)?->get($report->id) ?? 0) + 1;
        }
    }
}

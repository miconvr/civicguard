<?php

namespace App\Support;

class Duration
{
    public static function human(?float $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }
        if ($minutes < 1) {
            return 'under 1 min';
        }
        if ($minutes < 60) {
            return round($minutes) . ' min';
        }

        $hours = $minutes / 60;

        if ($hours < 48) {
            return round($hours, 1) . ' h';
        }

        return round($hours / 24, 1) . ' days';
    }
}

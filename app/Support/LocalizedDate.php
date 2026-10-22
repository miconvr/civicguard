<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

class LocalizedDate
{
    public static function format(DateTimeInterface $date, string $format): string
    {
        return Carbon::instance($date)
            ->locale(app()->getLocale())
            ->translatedFormat($format);
    }

    public static function relative(?DateTimeInterface $date): ?string
    {
        return $date
            ? Carbon::instance($date)->locale(app()->getLocale())->diffForHumans()
            : null;
    }
}

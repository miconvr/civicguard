<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'photo_path',
        'location_text',
        'latitude',
        'longitude',
        'severity',
        'status',
        'assigned_to',
        'resolved_at',
        'confirmed_at',
        'reopen_count',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function averageResolutionMinutes(): ?float
    {
        $times = static::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get(['created_at', 'resolved_at'])
            ->map(fn ($r) => $r->created_at->diffInMinutes($r->resolved_at));

        return $times->isNotEmpty() ? (float) $times->average() : null;
    }

    public function curfewLog()
    {
        return $this->hasOne(CurfewLog::class, 'report_id');
    }
}
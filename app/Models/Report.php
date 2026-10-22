<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopeOrderByUrgency(Builder $query): Builder
    {
        return $query->orderByRaw(
            "CASE severity WHEN 'critical' THEN 0 WHEN 'high' THEN 1 WHEN 'moderate' THEN 2 WHEN 'low' THEN 3 ELSE 4 END"
        );
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return match ($user->role) {
            'admin', 'official' => $query,
            'tanod' => $query->where(
                fn (Builder $q) => $q->where('assigned_to', $user->id)->orWhere('user_id', $user->id)
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function isVisibleTo(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return match ($user->role) {
            'admin', 'official' => true,
            'tanod' => (int) $this->assigned_to === (int) $user->id
                || (int) $this->user_id === (int) $user->id,
            'resident' => (int) $this->user_id === (int) $user->id,
            default => false,
        };
    }

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
        $driver = DB::getDriverName();
        $averageDuration = match ($driver) {
            'mysql' => 'TIMESTAMPDIFF(SECOND, created_at, resolved_at) / 60',
            'pgsql' => 'EXTRACT(EPOCH FROM (resolved_at - created_at)) / 60',
            'sqlite' => '(julianday(resolved_at) - julianday(created_at)) * 1440',
            default => throw new \RuntimeException('Unsupported database driver for resolution metrics.'),
        };

        $average = static::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->selectRaw("AVG({$averageDuration}) as average_minutes")
            ->value('average_minutes');

        return $average === null ? null : (float) $average;
    }

    public function curfewLog()
    {
        return $this->hasOne(CurfewLog::class, 'report_id');
    }
}

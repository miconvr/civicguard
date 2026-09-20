<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurfewLog extends Model
{
    protected $fillable = [
        'report_id',
        'minor_name',
        'minor_age',
        'guardian_name',
        'guardian_contact',
        'guardian_notified',
        'referral_action',
        'apprehension_datetime',
        'apprehension_location',
        'prior_violations_count',
        'tanod_id',
        'notes',
    ];

    protected $casts = [
        'guardian_notified' => 'boolean',
        'apprehension_datetime' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function tanod()
    {
        return $this->belongsTo(User::class, 'tanod_id');
    }
}
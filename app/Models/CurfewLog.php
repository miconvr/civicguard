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
        'apprehension_datetime',
        'apprehension_location',
        'prior_violations_count',
        'tanod_id',
        'notes',
    ];

    protected $casts = [
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
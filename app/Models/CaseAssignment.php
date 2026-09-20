<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseAssignment extends Model
{
    protected $fillable = [
        'report_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'status',
        'notes',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
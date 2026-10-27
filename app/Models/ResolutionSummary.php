<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The incident summary a tanod (or official) writes once a report is resolved.
 * It is only visible to staff, never to the resident who filed the report.
 */
class ResolutionSummary extends Model
{
    protected $table = 'report_resolution_summaries';

    public const TYPES = [
        'settled_on_site',
        'referred_lupon',
        'referred_pnp',
        'referred_agency',
        'no_action_needed',
        'other',
    ];

    protected $fillable = [
        'report_id',
        'written_by',
        'resolution_type',
        'narrative',
        'action_taken',
        'persons_involved',
        'follow_up_needed',
        'follow_up_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'follow_up_needed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /** @return array<string, string> */
    public static function typeLabels(): array
    {
        return [
            'settled_on_site' => __('Settled on site'),
            'referred_lupon' => __('Referred to the Lupon (Katarungang Pambarangay)'),
            'referred_pnp' => __('Referred to the PNP'),
            'referred_agency' => __('Referred to another agency'),
            'no_action_needed' => __('No action needed (unfounded or duplicate)'),
            'other' => __('Other'),
        ];
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->resolution_type] ?? ucfirst(str_replace('_', ' ', $this->resolution_type));
    }

    /** @return array<string, array<int, mixed>> */
    public static function rules(): array
    {
        return [
            'summary_resolution_type' => ['required', Rule::in(self::TYPES)],
            'summary_narrative' => ['required', 'string', 'min:10', 'max:3000'],
            'summary_action_taken' => ['required', 'string', 'min:5', 'max:2000'],
            'summary_persons_involved' => ['nullable', 'string', 'max:1000'],
            'summary_follow_up_needed' => ['nullable', 'boolean'],
            'summary_follow_up_notes' => ['nullable', 'string', 'max:1000', 'required_if:summary_follow_up_needed,1,true,on'],
        ];
    }

    /** True when the submitted form carries any summary field with content. */
    public static function requestHasInput(Request $request): bool
    {
        foreach (['summary_narrative', 'summary_action_taken', 'summary_persons_involved', 'summary_follow_up_notes'] as $field) {
            if (filled($request->input($field))) {
                return true;
            }
        }

        return false;
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'written_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

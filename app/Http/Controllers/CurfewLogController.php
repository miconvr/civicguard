<?php

namespace App\Http\Controllers;

use App\Models\CurfewLog;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurfewLogController extends Controller
{
    public function create()
    {
        return view('curfew.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'minor_name' => ['required', 'string', 'max:255'],
            'minor_age' => ['nullable', 'integer', 'min:0', 'max:17'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_contact' => ['nullable', 'string', 'max:50'],
            'guardian_notified' => ['nullable', 'boolean'],
            'referral_action' => ['nullable', 'string', 'max:255'],
            'apprehension_datetime' => ['required', 'date'],
            'apprehension_location' => ['required', 'string', 'max:255'],
            'prior_violations_count' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated) {
            $category = ReportCategory::firstOrCreate(
                ['name' => 'Curfew Violation'],
                ['default_severity' => 'high']
            );

            $severity = $validated['prior_violations_count'] >= 2 ? 'critical' : 'high';

            $report = Report::create([
                'user_id' => Auth::id(),
                'category_id' => $category->id,
                'description' => "Curfew violation involving minor: {$validated['minor_name']}. "
                    . ($validated['notes'] ?? 'No additional notes.'),
                'location_text' => $validated['apprehension_location'],
                'severity' => $severity,
                'status' => 'pending',
            ]);

            CurfewLog::create([
                'report_id' => $report->id,
                'minor_name' => $validated['minor_name'],
                'minor_age' => $validated['minor_age'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_contact' => $validated['guardian_contact'] ?? null,
                'guardian_notified' => $validated['guardian_notified'] ?? false,
                'referral_action' => $validated['referral_action'] ?? null,
                'apprehension_datetime' => $validated['apprehension_datetime'],
                'apprehension_location' => $validated['apprehension_location'],
                'prior_violations_count' => $validated['prior_violations_count'],
                'tanod_id' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'curfew_logged',
                'auditable_type' => Report::class,
                'auditable_id' => $report->id,
                'description' => 'Curfew violation logged.',
                'metadata' => [
                    'minor_age' => $validated['minor_age'] ?? null,
                    'prior_violations_count' => $validated['prior_violations_count'],
                    'severity' => $severity,
                    'guardian_notified' => (bool) ($validated['guardian_notified'] ?? false),
                    'referral_action' => $validated['referral_action'] ?? null,
                ],
            ]);
        });

        return redirect()->route('curfew.create')->with('status', 'Curfew violation logged successfully.');
    }
}
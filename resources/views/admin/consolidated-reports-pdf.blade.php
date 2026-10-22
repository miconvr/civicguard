<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'tl' ? 'tl' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('CivicGuard Consolidated Report') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 8px; }
        h1 { color: #7f1d1d; font-size: 17px; margin: 0 0 2px; }
        h2 { color: #7f1d1d; font-size: 11px; margin: 14px 0 5px; }
        .generated { color: #6b7280; margin-bottom: 8px; }
        .summary { width: 100%; margin-bottom: 8px; }
        .summary td { background: #f3f4f6; padding: 5px; text-align: center; }
        .summary strong { display: block; color: #7f1d1d; font-size: 13px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #7f1d1d; color: #fff; text-align: left; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 3px; vertical-align: top; }
        table.data tr:nth-child(even) { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>{{ __('CivicGuard Consolidated Report') }}</h1>
    <div class="generated">{{ __('Generated') }} {{ now()->format('M d, Y g:i A') }}</div>

    <table class="summary">
        <tr>
            <td><strong>{{ $totalReports }}</strong>{{ __("Total Reports") }}</td>
            <td><strong>{{ $resolvedCount }}</strong>{{ __("Resolved") }}</td>
            <td><strong>{{ $assignedCount }}</strong>{{ __("Assigned") }}</td>
            <td><strong>{{ $curfewCount }}</strong>{{ __('Curfew Logs') }}</td>
            <td><strong>{{ $averageResolution }}</strong>{{ __('Avg. Resolution') }}</td>
        </tr>
    </table>

    <h2>{{ __('Incident Summary') }}</h2>
    <table class="data">
        <thead><tr><th>{{ __("Date") }}</th><th>{{ __('Category') }}</th><th>{{ __('Reported By') }}</th><th>{{ __("Severity") }}</th><th>{{ __("Status") }}</th><th>{{ __("Assigned To") }}</th><th>{{ __('Coordinates') }}</th><th>{{ __('Photo') }}</th></tr></thead>
        <tbody>
            @forelse ($reports as $report)
                <tr><td>{{ $report->created_at->format('M d, Y g:i A') }}</td><td>{{ __($report->category->name) }}</td><td>{{ $report->user->name }}</td><td>{{ __(ucfirst($report->severity)) }}</td><td>{{ __(ucwords(str_replace('_', ' ', $report->status))) }}</td><td>{{ $report->assignedTo->name ?? '-' }}</td><td>{{ $report->latitude !== null && $report->longitude !== null ? $report->latitude . ', ' . $report->longitude : '-' }}</td><td>@if ($report->photo_path && \Illuminate\Support\Facades\Storage::disk('report_photos')->exists($report->photo_path))<img src="data:{{ \Illuminate\Support\Facades\Storage::disk('report_photos')->mimeType($report->photo_path) }};base64,{{ base64_encode(\Illuminate\Support\Facades\Storage::disk('report_photos')->get($report->photo_path)) }}" alt="{{ __('Incident photo') }}" style="width: 42px; height: 32px; object-fit: cover;">@else - @endif</td></tr>
            @empty
                <tr><td colspan="8">{{ __("No incident reports yet.") }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>{{ __("Curfew Monitoring") }}</h2>
    <table class="data">
        <thead><tr><th>{{ __("Date") }}</th><th>{{ __("Minor") }}</th><th>{{ __("Age") }}</th><th>{{ __("Location") }}</th><th>{{ __("Prior Violations") }}</th><th>{{ __("Guardian Notified") }}</th><th>{{ __("Follow-up") }}</th><th>{{ __("Logged By") }}</th></tr></thead>
        <tbody>
            @forelse ($curfewLogs as $log)
                <tr><td>{{ $log->apprehension_datetime->format('M d, Y g:i A') }}</td><td>{{ $log->minor_name }}</td><td>{{ $log->minor_age ?? '-' }}</td><td>{{ $log->apprehension_location }}</td><td>{{ $log->prior_violations_count }}</td><td>{{ $log->guardian_notified ? __('Yes') : __('No') }}</td><td>{{ $log->referral_action ? __($log->referral_action) : '-' }}</td><td>{{ $log->tanod->name ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="8">{{ __("No curfew logs yet.") }}</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($canViewAudit ?? true)
    <h2>{{ __("Recent System Activity") }}</h2>
    <table class="data">
        <thead><tr><th>{{ __("Date") }}</th><th>{{ __("Actor") }}</th><th>{{ __("Action") }}</th><th>{{ __("Description") }}</th></tr></thead>
        <tbody>
            @forelse ($auditLogs as $auditLog)
                <tr><td>{{ $auditLog->created_at->format('M d, Y g:i A') }}</td><td>{{ $auditLog->user->name ?? 'System' }}</td><td>{{ ucwords(str_replace('_', ' ', $auditLog->action)) }}</td><td>{{ $auditLog->description ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4">{{ __("No system activity yet.") }}</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif
</body>
</html>

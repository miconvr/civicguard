<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CivicGuard Consolidated Report</title>
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
    <h1>CivicGuard Consolidated Report</h1>
    <div class="generated">Generated {{ now()->format('M d, Y g:i A') }}</div>

    <table class="summary">
        <tr>
            <td><strong>{{ $totalReports }}</strong>Total Reports</td>
            <td><strong>{{ $resolvedCount }}</strong>Resolved</td>
            <td><strong>{{ $assignedCount }}</strong>Assigned</td>
            <td><strong>{{ $curfewCount }}</strong>Curfew Logs</td>
            <td><strong>{{ $averageResponseHours !== null ? $averageResponseHours . 'h' : '-' }}</strong>Avg. Resolution</td>
        </tr>
    </table>

    <h2>Incident Summary</h2>
    <table class="data">
        <thead><tr><th>Date</th><th>Category</th><th>Reported By</th><th>Severity</th><th>Status</th><th>Assigned To</th><th>Coordinates</th><th>Photo</th></tr></thead>
        <tbody>
            @forelse ($reports as $report)
                <tr><td>{{ $report->created_at->format('M d, Y g:i A') }}</td><td>{{ $report->category->name }}</td><td>{{ $report->user->name }}</td><td>{{ ucfirst($report->severity) }}</td><td>{{ ucwords(str_replace('_', ' ', $report->status)) }}</td><td>{{ $report->assignedTo->name ?? '-' }}</td><td>{{ $report->latitude !== null && $report->longitude !== null ? $report->latitude . ', ' . $report->longitude : '-' }}</td><td>@if ($report->photo_path && file_exists(public_path('storage/' . $report->photo_path)))<img src="{{ public_path('storage/' . $report->photo_path) }}" alt="Incident photo" style="width: 42px; height: 32px; object-fit: cover;">@else - @endif</td></tr>
            @empty
                <tr><td colspan="8">No incident reports yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Curfew Monitoring</h2>
    <table class="data">
        <thead><tr><th>Date</th><th>Minor</th><th>Age</th><th>Location</th><th>Prior Violations</th><th>Guardian Notified</th><th>Follow-up</th><th>Logged By</th></tr></thead>
        <tbody>
            @forelse ($curfewLogs as $log)
                <tr><td>{{ $log->apprehension_datetime->format('M d, Y g:i A') }}</td><td>{{ $log->minor_name }}</td><td>{{ $log->minor_age ?? '-' }}</td><td>{{ $log->apprehension_location }}</td><td>{{ $log->prior_violations_count }}</td><td>{{ $log->guardian_notified ? 'Yes' : 'No' }}</td><td>{{ $log->referral_action ?? '-' }}</td><td>{{ $log->tanod->name ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="8">No curfew logs yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Recent System Activity</h2>
    <table class="data">
        <thead><tr><th>Date</th><th>Actor</th><th>Action</th><th>Description</th></tr></thead>
        <tbody>
            @forelse ($auditLogs as $auditLog)
                <tr><td>{{ $auditLog->created_at->format('M d, Y g:i A') }}</td><td>{{ $auditLog->user->name ?? 'System' }}</td><td>{{ ucwords(str_replace('_', ' ', $auditLog->action)) }}</td><td>{{ $auditLog->description ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4">No system activity yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

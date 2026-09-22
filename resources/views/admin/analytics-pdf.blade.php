<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CivicGuard Analytics</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; }
        h1 { color: #7f1d1d; font-size: 16px; margin-bottom: 2px; }
        h2 { color: #7f1d1d; font-size: 12px; margin-top: 16px; margin-bottom: 6px; border-bottom: 2px solid #7f1d1d; padding-bottom: 3px; }
        .generated { color: #6b7280; margin-bottom: 10px; }
        .stats { display: table; width: 100%; margin-bottom: 10px; }
        .stat { display: table-cell; width: 33%; text-align: center; padding: 8px; border: 1px solid #d1d5db; }
        .stat .num { font-size: 20px; font-weight: bold; color: #7f1d1d; }
        .stat .label { font-size: 9px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #7f1d1d; color: #fff; text-align: left; padding: 4px; }
        td { border: 1px solid #d1d5db; padding: 4px; }
        ul { margin: 4px 0; padding-left: 16px; }
        li { margin-bottom: 3px; }
    </style>
</head>
<body>
    <h1>CivicGuard Analytics Report</h1>
    <div class="generated">Generated {{ now()->format('M d, Y g:i A') }}</div>

    <div class="stats">
        <div class="stat">
            <div class="num">{{ $totalReports }}</div>
            <div class="label">Total Reports</div>
        </div>
        <div class="stat">
            <div class="num">{{ $pendingCount }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat">
            <div class="num">{{ $resolvedCount }}</div>
            <div class="label">Resolved</div>
        </div>
    </div>

    <h2>AI Summary</h2>
    <p>{{ $analysisSummary }}</p>

    <h2>Recommended Follow-up Actions</h2>
    <ul>
        @forelse ($recommendations as $recommendation)
            <li>{{ $recommendation }}</li>
        @empty
            <li>No recommendations available.</li>
        @endforelse
    </ul>

    <h2>Observed Patterns</h2>
    <ul>
        @forelse ($patterns as $pattern)
            <li>{{ $pattern }}</li>
        @empty
            <li>No patterns detected.</li>
        @endforelse
    </ul>

    <h2>Risk Flags</h2>
    <ul>
        @forelse ($riskFlags as $riskFlag)
            <li>{{ $riskFlag }}</li>
        @empty
            <li>No risk flags detected.</li>
        @endforelse
    </ul>

    <h2>Reports by Category</h2>
    <table>
        <thead><tr><th>Category</th><th>Total</th></tr></thead>
        <tbody>
            @foreach ($byCategory as $category)
                <tr><td>{{ $category->name }}</td><td>{{ $category->total }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Reports by Severity</h2>
    <table>
        <thead><tr><th>Severity</th><th>Total</th></tr></thead>
        <tbody>
            @foreach (['low', 'moderate', 'high', 'critical'] as $level)
                <tr><td>{{ ucfirst($level) }}</td><td>{{ $bySeverity->get($level)->total ?? 0 }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Reports Filed (Last 14 Days)</h2>
    <table>
        <thead><tr><th>Date</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($last14Days as $day)
                <tr><td>{{ $day->day }}</td><td>{{ $day->total }}</td></tr>
            @empty
                <tr><td colspan="2">No reports in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($curfewCount > 0)
        <h2>Curfew Monitoring</h2>
        <p>{{ $curfewCount }} curfew violation log(s) recorded.</p>
    @endif
</body>
</html>

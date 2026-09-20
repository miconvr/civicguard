<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CivicGuard Reports</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 9px; }
        h1 { color: #7f1d1d; font-size: 16px; margin-bottom: 2px; }
        .generated { color: #6b7280; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #7f1d1d; color: #fff; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 4px; vertical-align: top; }
        tr:nth-child(even) { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>CivicGuard Incident Reports</h1>
    <div class="generated">Generated {{ now()->format('M d, Y g:i A') }}</div>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Reported By</th>
                <th>Location</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Coordinates</th>
                <th>Photo</th>
                <th>Date Filed</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report->category->name }}</td>
                    <td>{{ $report->user->name }}</td>
                    <td>{{ $report->location_text }}</td>
                    <td>{{ ucfirst($report->severity) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $report->status)) }}</td>
                    <td>{{ $report->assignedTo->name ?? '-' }}</td>
                    <td>{{ $report->latitude !== null && $report->longitude !== null ? $report->latitude . ', ' . $report->longitude : '-' }}</td>
                    <td>
                        @if ($report->photo_path && file_exists(public_path('storage/' . $report->photo_path)))
                            <img src="{{ public_path('storage/' . $report->photo_path) }}" alt="Incident photo" style="width: 48px; height: 36px; object-fit: cover;">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $report->created_at->format('M d, Y g:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No reports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'tl' ? 'tl' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
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
    <h1>CivicGuard {{ __($title) }}</h1>
    <div class="generated">{{ __('Generated') }} {{ \App\Support\LocalizedDate::format(now(), 'M d, Y g:i A') }}</div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Reported By') }}</th>
                <th>{{ __("Location") }}</th>
                <th>{{ __("Severity") }}</th>
                @if (!$isResolved)
                    <th>{{ __("Status") }}</th>
                @endif
                <th>{{ __("Assigned To") }}</th>
                <th>{{ __('Coordinates') }}</th>
                <th>{{ __('Photo') }}</th>
                <th>{{ $isResolved ? __('Resolved On') : __('Date Filed') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ __($report->category->name) }}</td>
                    <td>{{ $report->user->name }}</td>
                    <td>{{ $report->location_text }}</td>
                    <td>{{ __(ucfirst($report->severity)) }}</td>
                    @if (!$isResolved)
                        <td>{{ __(ucwords(str_replace('_', ' ', $report->status))) }}</td>
                    @endif
                    <td>{{ $report->assignedTo->name ?? '-' }}</td>
                    <td>{{ $report->latitude !== null && $report->longitude !== null ? $report->latitude . ', ' . $report->longitude : '-' }}</td>
                    <td>
                        @if ($report->photo_path && \Illuminate\Support\Facades\Storage::disk('report_photos')->exists($report->photo_path))
                            <img src="data:{{ \Illuminate\Support\Facades\Storage::disk('report_photos')->mimeType($report->photo_path) }};base64,{{ base64_encode(\Illuminate\Support\Facades\Storage::disk('report_photos')->get($report->photo_path)) }}" alt="{{ __('Incident photo') }}" style="width: 48px; height: 36px; object-fit: cover;">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $isResolved ? ($report->resolved_at ? \App\Support\LocalizedDate::format($report->resolved_at, 'M d, Y g:i A') : '-') : \App\Support\LocalizedDate::format($report->created_at, 'M d, Y g:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">{{ __('No reports found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

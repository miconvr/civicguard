<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'tl' ? 'tl' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('CivicGuard Audit Logs') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 9px; }
        h1 { color: #7f1d1d; font-size: 16px; margin-bottom: 2px; }
        .generated { color: #6b7280; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #7f1d1d; color: #fff; text-align: left; padding: 4px; }
        td { border: 1px solid #d1d5db; padding: 4px; }
        tr:nth-child(even) { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>{{ __('CivicGuard Audit Logs') }}</h1>
    <div class="generated">{{ __('Generated') }} {{ now()->format('M d, Y g:i A') }}</div>

    <table>
        <thead>
            <tr>
                <th>{{ __("Date") }}</th>
                <th>{{ __("Actor") }}</th>
                <th>{{ __("Action") }}</th>
                <th>{{ __("Description") }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($auditLogs as $auditLog)
                <tr>
                    <td>{{ $auditLog->created_at->format('M d, Y g:i A') }}</td>
                    <td>{{ $auditLog->user->name ?? 'System' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $auditLog->action)) }}</td>
                    <td>{{ $auditLog->description ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">{{ __("No audit activity yet.") }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

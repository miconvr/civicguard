@php($summary = $report->resolutionSummary)
<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'tl' ? 'tl' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Incident Resolution Summary') }} #{{ $report->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11px; line-height: 1.45; }
        .head { text-align: center; margin-bottom: 14px; }
        .head .org { font-size: 11px; color: #4b5563; }
        .head h1 { color: #7f1d1d; font-size: 17px; margin: 4px 0 2px; }
        .head .ref { color: #6b7280; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { border: 1px solid #d1d5db; padding: 5px 7px; vertical-align: top; }
        table.meta td.label { width: 28%; background: #f3f4f6; font-weight: bold; }
        h2 { font-size: 12px; color: #7f1d1d; border-bottom: 1px solid #d1d5db; padding-bottom: 2px; margin: 14px 0 5px; }
        p { margin: 0 0 4px; white-space: pre-line; }
        .sign { width: 100%; margin-top: 38px; border-collapse: collapse; }
        .sign td { width: 50%; text-align: center; padding: 0 14px; vertical-align: bottom; }
        .line { border-top: 1px solid #1f2937; margin-top: 30px; padding-top: 3px; font-weight: bold; }
        .role { color: #6b7280; font-size: 10px; }
        .foot { margin-top: 22px; color: #6b7280; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="head">
        <div class="org">{{ __('Barangay Maimpis, City of San Fernando, Pampanga') }}</div>
        <h1>{{ __('Incident Resolution Summary') }}</h1>
        <div class="ref">{{ __('Report') }} #{{ $report->id }}</div>
    </div>

    <table class="meta">
        <tr><td class="label">{{ __('Category') }}</td><td>{{ __($report->category->name) }}</td></tr>
        <tr><td class="label">{{ __('Location') }}</td><td>{{ $report->location_text }}</td></tr>
        <tr><td class="label">{{ __('Severity') }}</td><td>{{ __(ucfirst($report->severity)) }}</td></tr>
        <tr><td class="label">{{ __('Reported by') }}</td><td>{{ $report->user->name }}</td></tr>
        <tr><td class="label">{{ __('Date and time filed') }}</td><td>{{ \App\Support\LocalizedDate::format($report->created_at, 'M d, Y g:i A') }}</td></tr>
        <tr><td class="label">{{ __('Date and time resolved') }}</td><td>{{ $report->resolved_at ? \App\Support\LocalizedDate::format($report->resolved_at, 'M d, Y g:i A') : '-' }}</td></tr>
        <tr><td class="label">{{ __('Assigned tanod') }}</td><td>{{ $report->assignedTo->name ?? '-' }}</td></tr>
        <tr><td class="label">{{ __('Outcome') }}</td><td>{{ $summary->typeLabel() }}</td></tr>
    </table>

    <h2>{{ __('Original report') }}</h2>
    <p>{{ $report->description }}</p>

    <h2>{{ __('Summary of what happened') }}</h2>
    <p>{{ $summary->narrative }}</p>

    <h2>{{ __('Action taken') }}</h2>
    <p>{{ $summary->action_taken }}</p>

    @if ($summary->persons_involved)
        <h2>{{ __('Persons involved or witnesses') }}</h2>
        <p>{{ $summary->persons_involved }}</p>
    @endif

    <h2>{{ __('Follow-up') }}</h2>
    @if ($summary->follow_up_needed)
        <p><strong>{{ __('Still needed') }}</strong>@if ($summary->follow_up_notes): {{ $summary->follow_up_notes }}@endif</p>
    @else
        <p>{{ __('None needed') }}</p>
    @endif

    <table class="sign">
        <tr>
            <td>
                <div class="line">{{ $summary->writer->name ?? '' }}</div>
                <div class="role">{{ __('Prepared by') }} &middot; {{ \App\Support\LocalizedDate::format($summary->updated_at, 'M d, Y') }}</div>
            </td>
            <td>
                <div class="line">{{ $summary->reviewer->name ?? '' }}</div>
                <div class="role">{{ __('Reviewed by (barangay official)') }}@if ($summary->reviewed_at) &middot; {{ \App\Support\LocalizedDate::format($summary->reviewed_at, 'M d, Y') }}@endif</div>
            </td>
        </tr>
    </table>

    <div class="foot">{{ __('Generated') }} {{ \App\Support\LocalizedDate::format(now(), 'M d, Y g:i A') }} &middot; {{ __('For barangay use only. Not shared with the resident.') }}</div>
</body>
</html>

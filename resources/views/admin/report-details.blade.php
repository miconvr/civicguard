<x-app-layout>
    <x-slot name="headerWidth">max-w-4xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Report') }} <span class="font-normal cg-muted">#{{ $report->id }}</span>
        </h2>
    </x-slot>

    @php
        $assignedLog = $history->firstWhere('action', 'report_assigned');
        $startedLog = $history->first(fn ($l) => ($l->metadata['status'] ?? null) === 'in_progress');
        $steps = [
            ['Received', true, $report->created_at],
            ['Assigned', (bool) ($assignedLog || $report->assigned_to), $assignedLog?->created_at],
            ['In progress', (bool) ($startedLog || $report->resolved_at || $report->status === 'in_progress'), $startedLog?->created_at],
            ['Resolved', $report->status === 'resolved', $report->resolved_at],
        ];
        $statusUpdates = $history->filter(fn ($log) => in_array($log->action, ['report_status_changed', 'report_resolved', 'report_reopened'], true));
        $hasCoords = $report->latitude !== null && $report->longitude !== null;
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="cg-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $report->category->name }}</h3>
                        <p class="cg-muted text-sm">{{ $report->location_text }}</p>
                        @if ($hasCoords)
                            <a href="https://www.google.com/maps?q={{ (float) $report->latitude }},{{ (float) $report->longitude }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-semibold text-maroon-700 hover:underline dark:text-[#c27872]">{{ __('Open in Maps') }} &rarr;</a>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span class="cg-sev cg-sev-{{ $report->severity }}">{{ __(ucfirst($report->severity)) }}</span>
                        <span class="cg-pill cg-pill-{{ $report->status }}">{{ __(ucwords(str_replace('_', ' ', $report->status))) }}</span>
                    </div>
                </div>

                <ol class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4" aria-label="{{ __('Progress') }}">
                    @foreach ($steps as [$label, $done, $at])
                        <li>
                            <div class="h-1.5 rounded-full {{ $done ? 'cg-bar-fill bg-maroon-700 dark:bg-[#b56764]' : 'bg-gray-200 dark:bg-gray-700' }}" @if ($done) style="--cg-bar-delay: {{ $loop->index * 140 + 250 }}ms" @endif></div>
                            <p class="mt-1.5 text-xs {{ $done ? 'font-semibold text-gray-900 dark:text-gray-100' : 'cg-muted' }}">{{ __($label) }}</p>
                            @if ($at)<p class="text-[11px] leading-tight cg-muted">{{ $at->format('M d') }}<br>{{ $at->format('g:i A') }}</p>@endif
                        </li>
                    @endforeach
                </ol>

                <div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Progress updates') }}</h4>
                    <ol class="mt-3 space-y-3">
                        <li class="flex gap-3 text-sm">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-amber-500" aria-hidden="true"></span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ __('Report submitted') }} · {{ __('Pending') }}</p>
                                <p class="text-xs cg-muted">{{ $report->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </li>
                        @foreach ($statusUpdates as $update)
                            @php
                                $status = $update->metadata['status'] ?? match ($update->action) {
                                    'report_resolved' => 'resolved',
                                    'report_reopened' => 'pending',
                                    default => null,
                                };
                                $fromStatus = $update->metadata['from'] ?? ($update->action === 'report_reopened' ? 'resolved' : null);
                            @endphp
                            <li class="flex gap-3 text-sm">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $status === 'resolved' ? 'bg-green-500' : ($status === 'in_progress' ? 'bg-blue-500' : 'bg-amber-500') }}" aria-hidden="true"></span>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">
                                        @if ($fromStatus)
                                            {{ __(ucwords(str_replace('_', ' ', $fromStatus))) }} &rarr;
                                        @endif
                                        {{ __(ucwords(str_replace('_', ' ', $status ?? 'Status changed'))) }}
                                    </p>
                                    <p class="text-xs cg-muted">{{ $update->user->name ?? __('System') }} · {{ $update->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'official'], true))
            <div class="cg-card">
                <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-start">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="report-severity" class="mb-1.5 block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Severity') }}</label>
                        <select id="report-severity" name="severity" class="cg-select w-full">
                            @foreach (['low' => 'Low', 'moderate' => 'Moderate', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                <option value="{{ $value }}" @selected($report->severity === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Set automatically from the report text. Correct it if it is wrong.') }}</p>
                    </div>
                    <div>
                        <label for="report-status" class="mb-1.5 block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Status') }}</label>
                        <select id="report-status" name="status" class="cg-select w-full">
                            <option value="pending" @selected($report->status === 'pending')>{{ __('Pending') }}</option>
                            <option value="in_progress" @selected($report->status === 'in_progress')>{{ __('In Progress') }}</option>
                            <option value="resolved" @selected($report->status === 'resolved')>{{ __('Resolved') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="report-assignee" class="mb-1.5 block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Assigned tanod') }}</label>
                        <select id="report-assignee" name="assigned_to" class="cg-select w-full">
                            @unless ($report->assigned_to)
                                <option value="" selected disabled>{{ __('Select a tanod') }}</option>
                            @endunless
                            @foreach ($tanods as $tanod)
                                <option value="{{ $tanod->id }}" @selected((int) $report->assigned_to === (int) $tanod->id)>{{ $tanod->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Leave unchanged if no reassignment is needed.') }}</p>
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="cg-btn">{{ __('Save changes') }}</button>
                    </div>
                </form>
            </div>
            @else
            <div class="cg-card">
                <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    @method('PATCH')
                    <div class="w-full sm:max-w-xs">
                        <label for="report-status" class="mb-1.5 block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Update status') }}</label>
                        <select id="report-status" name="status" class="cg-select w-full">
                            <option value="pending" @selected($report->status === 'pending')>{{ __('Pending') }}</option>
                            <option value="in_progress" @selected($report->status === 'in_progress')>{{ __('In Progress') }}</option>
                            <option value="resolved" @selected($report->status === 'resolved')>{{ __('Resolved') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="cg-btn">{{ __('Save') }}</button>
                </form>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="cg-card">
                        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('What happened') }}</h3>
                        <p class="whitespace-pre-line text-sm text-gray-800 dark:text-gray-200">{{ $report->description }}</p>
                    </div>

                    @if ($report->photo_path)
                        <div class="cg-card">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Photo') }}</h3>
                            <a href="{{ route('admin.reports.photo', $report) }}" target="_blank" rel="noopener">
                                <img src="{{ route('admin.reports.photo', $report) }}" alt="{{ __('Incident photo') }}" class="max-h-96 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                            </a>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="cg-card">
                        <dl class="space-y-3 text-sm">
                            <div><dt class="cg-muted">{{ __('Reported by') }}</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $report->user->name }}</dd></div>
                            <div><dt class="cg-muted">{{ __('Assigned to') }}</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $report->assignedTo->name ?? __('Not yet assigned') }}</dd></div>
                            <div><dt class="cg-muted">{{ __('Filed') }}</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $report->created_at->format('M d, Y g:i A') }}</dd></div>
                            @if ($report->resolved_at)
                                <div><dt class="cg-muted">{{ __('Resolved') }}</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $report->resolved_at->format('M d, Y g:i A') }}</dd></div>
                            @endif
                            @if ($report->confirmed_at)
                                <div><dt class="cg-muted">{{ __('Confirmed fixed') }}</dt><dd class="font-medium text-green-700 dark:text-green-400">{{ $report->confirmed_at->format('M d, Y') }}</dd></div>
                            @endif
                            @if ($report->reopen_count > 0)
                                <div><dt class="cg-muted">{{ __('Reopened') }}</dt><dd class="font-medium text-amber-700 dark:text-amber-400">{{ $report->reopen_count }}x</dd></div>
                            @endif
                        </dl>
                        @if ($report->curfewLog)
                            <a href="{{ route('admin.reports.curfewDetails', $report) }}" class="mt-4 block text-sm font-semibold text-maroon-700 hover:underline dark:text-[#c27872]">{{ __('View Curfew Violation Details') }} &rarr;</a>
                        @endif
                    </div>

                    <div class="cg-card">
                        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('History') }}</h3>
                        <ul class="space-y-3 text-sm">
                            @forelse ($history->reverse() as $log)
                                <li>
                                    <p class="text-gray-800 dark:text-gray-200">{{ $log->description }}</p>
                                    <p class="text-xs cg-muted">{{ $log->user->name ?? __('System') }} &middot; {{ $log->created_at->format('M d, g:i A') }}</p>
                                </li>
                            @empty
                                <li class="cg-muted">{{ __('No activity yet.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-maroon-700 hover:underline dark:text-[#c27872]">&larr; {{ __('Back to Incident Queue') }}</a>
        </div>
    </div>
</x-app-layout>

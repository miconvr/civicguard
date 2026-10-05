<x-app-layout>
    <x-slot name="headerWidth">max-w-7xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Summary Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-end">
                <x-export-button :href="route('admin.consolidatedReports.exportPdf')" />
            </div>

            @php
                $summaryKpis = [
                    ['Total Reports', $totalReports, 'cg-kpi'],
                    ['Resolved', $resolvedCount, 'cg-kpi'],
                    ['Assigned', $assignedCount, 'cg-kpi'],
                    ['Curfew Logs', $curfewCount, 'cg-kpi'],
                    ['Avg. Resolution', $averageResponseHours !== null ? $averageResponseHours . 'h' : '-', 'cg-kpi'],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach ($summaryKpis as [$label, $value, $color])
                    <div class="cg-card text-center">
                        <p class="text-3xl font-bold {{ $color }}">{{ $value }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="cg-card p-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Incident Summary</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead><tr><th>Date</th><th>Category</th><th>Reported By</th><th>Severity</th><th>Status</th><th>Assigned To</th></tr></thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $report->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $report->category->name }}</td>
                                    <td>{{ $report->user->name }}</td>
                                    <td>{{ ucfirst($report->severity) }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $report->status)) }}</td>
                                    <td>{{ $report->assignedTo->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-gray-500">No incident reports yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cg-card p-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Curfew Monitoring</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead><tr><th>Date</th><th>Minor</th><th>Age</th><th>Location</th><th>Prior Violations</th><th>Guardian Notified</th><th>Follow-up</th><th>Logged By</th></tr></thead>
                        <tbody>
                            @forelse ($curfewLogs as $log)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $log->apprehension_datetime->format('M d, Y g:i A') }}</td>
                                    <td>{{ $log->minor_name }}</td>
                                    <td>{{ $log->minor_age ?? '-' }}</td>
                                    <td>{{ $log->apprehension_location }}</td>
                                    <td>{{ $log->prior_violations_count }}</td>
                                    <td>{{ $log->guardian_notified ? 'Yes' : 'No' }}</td>
                                    <td>{{ $log->referral_action ?? '-' }}</td>
                                    <td>{{ $log->tanod->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-8 text-center text-gray-500">No curfew logs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cg-card p-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Recent System Activity</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead><tr><th>Date</th><th>Actor</th><th>Action</th><th>Description</th></tr></thead>
                        <tbody>
                            @forelse ($auditLogs as $auditLog)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $auditLog->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $auditLog->user->name ?? 'System' }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $auditLog->action)) }}</td>
                                    <td>{{ $auditLog->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-gray-500">No system activity yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

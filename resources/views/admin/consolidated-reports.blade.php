<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Consolidated Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-end">
                <a href="{{ route('admin.consolidatedReports.exportPdf') }}" class="inline-flex items-center bg-maroon-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                    Export PDF
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="cg-card"><p class="text-2xl font-bold text-maroon-700">{{ $totalReports }}</p><p class="text-xs text-gray-500">Total Reports</p></div>
                <div class="cg-card"><p class="text-2xl font-bold text-green-600">{{ $resolvedCount }}</p><p class="text-xs text-gray-500">Resolved</p></div>
                <div class="cg-card"><p class="text-2xl font-bold text-blue-600">{{ $assignedCount }}</p><p class="text-xs text-gray-500">Assigned</p></div>
                <div class="cg-card"><p class="text-2xl font-bold text-orange-600">{{ $curfewCount }}</p><p class="text-xs text-gray-500">Curfew Logs</p></div>
                <div class="cg-card"><p class="text-2xl font-bold text-gray-700">{{ $averageResponseHours !== null ? $averageResponseHours . 'h' : '-' }}</p><p class="text-xs text-gray-500">Avg. Resolution</p></div>
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
                        <thead><tr><th>Date</th><th>Minor</th><th>Age</th><th>Location</th><th>Prior Violations</th><th>Logged By</th></tr></thead>
                        <tbody>
                            @forelse ($curfewLogs as $log)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $log->apprehension_datetime->format('M d, Y g:i A') }}</td>
                                    <td>{{ $log->minor_name }}</td>
                                    <td>{{ $log->minor_age ?? '-' }}</td>
                                    <td>{{ $log->apprehension_location }}</td>
                                    <td>{{ $log->prior_violations_count }}</td>
                                    <td>{{ $log->tanod->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-gray-500">No curfew logs yet.</td></tr>
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

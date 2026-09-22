@php
    $statusClass = [
        'pending' => 'cg-badge-status',
        'in_progress' => 'cg-badge-status',
        'resolved' => 'cg-badge-status',
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Incident Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="GET" class="flex gap-3 mb-6">
                    <select name="status" onchange="this.form.submit()" class="cg-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    <select name="severity" onchange="this.form.submit()" class="cg-select">
                        <option value="">All Severities</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="moderate" {{ request('severity') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </form>

                <form method="GET" action="{{ route('admin.reports.exportPdf') }}" class="mb-6 flex justify-end items-center gap-2">
                    <select name="group" class="cg-select">
                        <option value="active">Active Reports (Pending &amp; In Progress)</option>
                        <option value="resolved">Resolved Reports</option>
                    </select>
                    <button type="submit" class="inline-flex items-center bg-maroon-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                        Export PDF
                    </button>
                </form>

                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Reported By</th>
                                <th>Location</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td class="font-medium">{{ $report->category->name }}</td>
                                    <td>{{ $report->user->name }}</td>
                                    <td>{{ $report->location_text }}</td>
                                    <td>
                                        <span class="cg-sev cg-sev-{{ $report->severity }}">{{ ucfirst($report->severity) }}</span>
                                    </td>
                                    <td>
                                        <span class="cg-badge {{ $statusClass[$report->status] ?? 'cg-badge-status' }}">
                                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $report->assignedTo->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap">
                                        {{ $report->created_at->format('M d, Y g:i A') }}
                                        <br>
                                        <a href="{{ route('admin.reports.details', $report) }}" class="text-xs text-maroon-700 dark:text-maroon-700 font-semibold hover:underline">
                                            View Details
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" class="flex gap-2 mb-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="cg-select">
                                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                            <button type="submit" class="text-maroon-700 text-sm font-semibold hover:underline transition">Update</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reports.assign', $report) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="assigned_to" class="cg-select">
                                                <option value="">Assign Tanod</option>
                                                @foreach ($tanods as $tanod)
                                                    <option value="{{ $tanod->id }}" {{ $report->assigned_to === $tanod->id ? 'selected' : '' }}>
                                                        {{ $tanod->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="text-maroon-700 text-sm font-semibold hover:underline transition">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-500 dark:text-gray-400">No reports yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                                <div class="mt-4">
                    {{ $reports->links() }}
                </div>

            </div>

            <div class="cg-card p-8 mt-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Recently Resolved</h3>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Reported By</th>
                                <th>Location</th>
                                <th>Severity</th>
                                <th>Assigned To</th>
                                <th>Resolved On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($resolvedReports as $report)
                                <tr>
                                    <td class="font-medium">{{ $report->category->name }}</td>
                                    <td>{{ $report->user->name }}</td>
                                    <td>{{ $report->location_text }}</td>
                                    <td>
                                        <span class="cg-sev cg-sev-{{ $report->severity }}">{{ ucfirst($report->severity) }}</span>
                                    </td>
                                    <td>{{ $report->assignedTo->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap">{{ $report->resolved_at?->format('M d, Y g:i A') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">No resolved reports yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

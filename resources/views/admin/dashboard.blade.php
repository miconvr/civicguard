<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Incident Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="GET" class="flex gap-3 mb-4">
                    <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-md">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    <select name="severity" onchange="this.form.submit()" class="border-gray-300 rounded-md">
                        <option value="">All Severities</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="moderate" {{ request('severity') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </form>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Reported By</th>
                            <th class="px-3 py-2">Location</th>
                            <th class="px-3 py-2">Severity</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Assigned To</th>
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-3 py-2">{{ $report->category->name }}</td>
                                <td class="px-3 py-2">{{ $report->user->name }}</td>
                                <td class="px-3 py-2">{{ $report->location_text }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @class([
                                            'bg-gray-100 text-gray-700' => $report->severity === 'low',
                                            'bg-yellow-100 text-yellow-700' => $report->severity === 'moderate',
                                            'bg-orange-100 text-orange-700' => $report->severity === 'high',
                                            'bg-red-100 text-red-700' => $report->severity === 'critical',
                                        ])">
                                        {{ ucfirst($report->severity) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</td>
                                <td class="px-3 py-2">
                                    {{ $report->assignedTo->name ?? '—' }}
                                </td>
                                <td class="px-3 py-2">{{ $report->created_at->format('M d, Y g:i A') }}</td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" class="flex gap-2 mb-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="text-sm border-gray-300 rounded-md">
                                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                        <button type="submit" class="text-indigo-600 text-sm font-medium">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reports.assign', $report) }}" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="assigned_to" class="text-sm border-gray-300 rounded-md">
                                            <option value="">Assign tanod...</option>
                                            @foreach ($tanods as $tanod)
                                                <option value="{{ $tanod->id }}" {{ $report->assigned_to === $tanod->id ? 'selected' : '' }}>
                                                    {{ $tanod->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-indigo-600 text-sm font-medium">Assign</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-gray-500">No reports yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $reports->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
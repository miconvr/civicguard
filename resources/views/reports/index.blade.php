<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                <div class="mb-4">
                    <a href="{{ route('reports.create') }}" class="bg-maroon-600 text-white px-4 py-2 rounded-md text-sm">
                        + Report New Incident
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Description</th>
                            <th class="px-3 py-2">Location</th>
                            <th class="px-3 py-2">Severity</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Date Filed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-3 py-2">{{ $report->category->name }}</td>
                                <td class="px-3 py-2 max-w-xs truncate">{{ $report->description }}</td>
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
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @class([
                                            'bg-gray-100 text-gray-700' => $report->status === 'pending',
                                            'bg-blue-100 text-blue-700' => $report->status === 'in_progress',
                                            'bg-green-100 text-green-700' => $report->status === 'resolved',
                                        ])">
                                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $report->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                    You haven't filed any reports yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>

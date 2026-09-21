@php
    $statusClass = [
        'pending' => 'cg-badge-status',
        'in_progress' => 'cg-badge-status',
        'resolved' => 'cg-badge-status',
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('My Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                <div class="mb-5">
                    <a href="{{ route('reports.create') }}" class="inline-flex items-center bg-maroon-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                        {{ __('+ Report New Incident') }}
                    </a>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead>
                            <tr>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Severity') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date Filed') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td class="font-medium">{{ $report->category->name }}</td>
                                    <td class="max-w-xs truncate">{{ $report->description }}</td>
                                    <td>{{ $report->location_text }}</td>
                                    <td>
                                        <span class="cg-sev cg-sev-{{ $report->severity }}">{{ __(ucfirst($report->severity)) }}</span>
                                    </td>
                                    <td>
                                        <span class="cg-badge {{ $statusClass[$report->status] ?? 'cg-badge-status' }}">
                                            {{ __(ucwords(str_replace('_', ' ', $report->status))) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap">{{ $report->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('You haven\'t filed any reports yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>


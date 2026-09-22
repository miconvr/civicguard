<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Report Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card">

                <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $report->category->name }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reported By</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->user->name }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->description }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->location_text }}</dd>
                    </div>
                    @if ($report->latitude !== null && $report->longitude !== null)
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Coordinates</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->latitude }}, {{ $report->longitude }}</dd>
                        </div>
                    @endif
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Severity</dt>
                        <dd class="text-sm">
                            <span class="cg-sev cg-sev-{{ $report->severity }}">{{ ucfirst($report->severity) }}</span>
                        </dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $report->status)) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned To</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->assignedTo->name ?? 'Not yet assigned' }}</dd>
                    </div>
                    @if ($report->photo_path)
                        <div class="py-3">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Photo</dt>
                            <dd>
                                <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Incident photo" class="rounded-lg max-w-full h-auto border border-gray-200 dark:border-gray-700">
                            </dd>
                        </div>
                    @endif
                    <div class="py-3 flex justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Filed</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if ($report->resolved_at)
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolved On</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->resolved_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($report->curfewLog)
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.curfewDetails', $report) }}" class="text-maroon-700 dark:text-maroon-700 text-sm font-semibold hover:underline">
                            View Curfew Violation Details &rarr;
                        </a>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-maroon-700 dark:text-maroon-700 text-sm font-semibold hover:underline">
                        &larr; Back to Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

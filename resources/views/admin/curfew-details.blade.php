<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Curfew Violation Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card">

                @if ($report->curfewLog)
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minor's Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $report->curfewLog->minor_name }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Age</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->minor_age ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Guardian's Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->guardian_name ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Guardian's Contact</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->guardian_contact ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Apprehension Date/Time</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($report->curfewLog->apprehension_datetime)->format('M d, Y g:i A') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->apprehension_location }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prior Violations</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->prior_violations_count }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Logged By (Tanod)</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->tanod->name ?? '-' }}</dd>
                        </div>
                        @if ($report->curfewLog->notes)
                            <div class="py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No curfew details found for this report.</p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-maroon-700 dark:text-maroon-400 text-sm font-semibold hover:underline">
                        &larr; Back to Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

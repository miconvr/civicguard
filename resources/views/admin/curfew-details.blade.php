<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Curfew Violation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card">

                @if ($report->curfewLog)
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Minor's Name") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $report->curfewLog->minor_name }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Age") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->minor_age ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Guardian's Name") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->guardian_name ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Guardian's Contact") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->guardian_contact ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Guardian Notified") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->guardian_notified ? __('Yes') : __('No') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Referral / Follow-up") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->referral_action ? __($report->curfewLog->referral_action) : '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Apprehension Date/Time") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ \App\Support\LocalizedDate::format(\Carbon\Carbon::parse($report->curfewLog->apprehension_datetime), 'M d, Y g:i A') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Location") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->apprehension_location }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Prior Violations") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->prior_violations_count }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __("Logged By (Tanod)") }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->tanod->name ?? '-' }}</dd>
                        </div>
                        @if ($report->curfewLog->notes)
                            <div class="py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __("Notes") }}</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $report->curfewLog->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __("No curfew details found for this report.") }}</p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-maroon-700 dark:text-gray-200 text-sm font-semibold hover:underline">
                        &larr; {{ __('Back to Dashboard') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

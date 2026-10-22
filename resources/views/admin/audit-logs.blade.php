<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-end">
                <x-export-button :href="route('admin.auditLogs.exportPdf')" />
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 min-[360px]:grid-cols-3 gap-3 sm:gap-4" data-reveal>
                @foreach ([
                    [__('Total entries'), $stats['total']],
                    [__('Today'), $stats['today']],
                    [__('Last 7 days'), $stats['week']],
                ] as [$label, $count])
                    <div class="cg-card p-3 min-[360px]:p-4">
                        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __($label) }}</div>
                        <div class="text-2xl sm:text-3xl font-semibold cg-kpi">{{ $count }}</div>
                    </div>
                @endforeach
            </div>

            <div class="cg-card p-6">
                {{-- Filter bar --}}
                <form method="GET" class="mb-4 flex flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by actor, action, or description…') }}"
                           class="cg-select w-full min-w-0 sm:min-w-[18rem] sm:flex-1" aria-label="{{ __('Search audit logs') }}">
                    <select name="action" onchange="this.form.submit()" class="cg-select w-full min-w-0 sm:w-auto sm:max-w-[22rem]" aria-label="{{ __("Filter by action") }}">
                        <option value="">{{ __("All actions") }}</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ __(ucwords(str_replace('_', ' ', $action))) }}</option>
                        @endforeach
                    </select>
                    @if (request()->filled('search') || request()->filled('action'))
                        <a href="{{ route('admin.auditLogs') }}"
                           class="inline-flex min-h-11 items-center self-start rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">{{ __("Clear filters ✕") }}</a>
                    @endif
                </form>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table cg-mobile-table">
                        <thead>
                            <tr>
                                <th>{{ __("Date") }}</th>
                                <th>{{ __("Actor") }}</th>
                                <th>{{ __("Action") }}</th>
                                <th>{{ __("Description") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($auditLogs as $auditLog)
                                <tr>
                                    <td data-label="Date" class="whitespace-nowrap">{{ $auditLog->created_at->format('M d, Y g:i A') }}</td>
                                    <td data-label="Actor" class="font-medium">{{ $auditLog->user->name ?? 'System' }}</td>
                                    <td data-label="Action">
                                        <span class="inline-flex max-w-full whitespace-normal break-words rounded-full px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $auditLog->localized_action }}
                                        </span>
                                    </td>
                                    <td data-label="Description">{{ $auditLog->localized_description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ request()->filled('search') || request()->filled('action') ? __('No entries match your filters.') : __('No audit activity yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

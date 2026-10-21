<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Audit Logs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <x-export-button :href="route('admin.auditLogs.exportPdf')" />
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-3 gap-3 sm:gap-4" data-reveal>
                @foreach ([
                    ['Total entries', $stats['total']],
                    ['Today', $stats['today']],
                    ['Last 7 days', $stats['week']],
                ] as [$label, $count])
                    <div class="cg-card p-4">
                        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $label }}</div>
                        <div class="text-2xl sm:text-3xl font-semibold cg-kpi">{{ $count }}</div>
                    </div>
                @endforeach
            </div>

            @if (auth()->user()->role === 'admin')
                <section class="rounded-xl border border-red-200 bg-red-50/70 p-5 dark:border-red-900/70 dark:bg-red-950/20" aria-labelledby="reset-data-title">
                    <div class="max-w-3xl">
                        <h3 id="reset-data-title" class="text-base font-semibold text-red-900 dark:text-red-200">{{ __('Reset test data') }}</h3>
                        <p class="mt-1 text-sm text-red-800 dark:text-red-300">{{ __('This permanently deletes all incident reports, assignments, curfew logs, notifications, chatbot history, residence verification records, audit logs, report photos, and verification documents. User accounts, profile pictures, and report categories are kept.') }}</p>
                        <form method="POST" action="{{ route('admin.data.reset') }}" class="mt-4 grid gap-3 sm:grid-cols-2" onsubmit="return confirm('{{ __('This cannot be undone. Delete all test data now?') }}')">
                            @csrf
                            @method('DELETE')
                            <div>
                                <label for="reset-confirmation" class="mb-1 block text-sm font-medium text-red-900 dark:text-red-200">{{ __('Type DELETE ALL TEST DATA to confirm') }}</label>
                                <input id="reset-confirmation" name="confirmation" type="text" required autocomplete="off" class="cg-select w-full" placeholder="DELETE ALL TEST DATA">
                                @error('confirmation')
                                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reset-password" class="mb-1 block text-sm font-medium text-red-900 dark:text-red-200">{{ __('Confirm your password') }}</label>
                                <input id="reset-password" name="password" type="password" required autocomplete="current-password" class="cg-select w-full">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">{{ __('Delete all test data') }}</button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif

            <div class="cg-card p-6">
                {{-- Filter bar --}}
                <form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by actor, action, or description…"
                           class="cg-select w-full sm:w-80 lg:w-96" aria-label="Search audit logs">
                    <select name="action" onchange="this.form.submit()" class="cg-select" aria-label="Filter by action">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                        @endforeach
                    </select>
                    @if (request()->filled('search') || request()->filled('action'))
                        <a href="{{ route('admin.auditLogs') }}"
                           class="text-xs rounded-full bg-amber-100 text-amber-800 px-3 py-1 dark:bg-amber-900/40 dark:text-amber-300">Clear filters ✕</a>
                    @endif
                </form>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table cg-mobile-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Actor</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($auditLogs as $auditLog)
                                <tr>
                                    <td data-label="Date" class="whitespace-nowrap">{{ $auditLog->created_at->format('M d, Y g:i A') }}</td>
                                    <td data-label="Actor" class="font-medium">{{ $auditLog->user->name ?? 'System' }}</td>
                                    <td data-label="Action">
                                        <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ match ($auditLog->action) {
                                                'report_status_changed' => __('Status changed'),
                                                'report_confirmed_fixed' => __('Confirmed fixed'),
                                                default => ucwords(str_replace('_', ' ', $auditLog->action)),
                                            } }}
                                        </span>
                                    </td>
                                    <td data-label="Description">{{ $auditLog->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ request()->filled('search') || request()->filled('action') ? 'No entries match your filters.' : 'No audit activity yet.' }}
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

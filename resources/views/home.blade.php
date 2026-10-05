<x-app-layout>
    <x-slot name="headerWidth">max-w-4xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Home') }}</h2>
    </x-slot>

    @php $first = \Illuminate\Support\Str::before($user->name, ' '); @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Good day,') }} {{ $first }}</p>
                <p class="cg-muted text-sm">Barangay Maimpis</p>
            </div>

            @if ($user->role === 'resident')

                <a href="{{ route('reports.create') }}" class="flex items-center justify-between gap-4 rounded-xl bg-maroon-700 p-6 text-white shadow-md transition hover:bg-maroon-800 focus:outline-none">
                    <span>
                        <span class="block text-xl font-semibold">{{ __('Report an incident') }}</span>
                        <span class="block text-sm text-maroon-100">{{ __('It takes about a minute.') }}</span>
                    </span>
                    <span class="text-3xl" aria-hidden="true">&rsaquo;</span>
                </a>

                @if ($needsFeedback > 0)
                    <a href="{{ route('reports.index') }}" class="block rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 transition hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                        <strong>{{ __('Was this fixed?') }}</strong>
                        {{ $needsFeedback }} {{ __('resolved report(s) are waiting for your answer.') }}
                    </a>
                @endif

                <div class="cg-card">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Latest reports') }}</h3>
                        <a href="{{ route('reports.index') }}" class="text-sm font-medium text-maroon-700 hover:underline dark:text-maroon-300">{{ __('View all') }}</a>
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($recent as $report)
                            <li>
                                <a href="{{ route('reports.index') }}#report-{{ $report->id }}" class="flex min-h-[56px] items-center justify-between gap-3 py-3">
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-gray-900 dark:text-gray-100">{{ $report->category->name }} <span class="font-normal cg-muted">#{{ $report->id }}</span></span>
                                        <span class="block truncate text-sm cg-muted">{{ $report->location_text }}</span>
                                    </span>
                                    <span class="cg-pill cg-pill-{{ $report->status }}">{{ __(ucwords(str_replace('_', ' ', $report->status))) }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm cg-muted">{{ __('You haven\'t filed any reports yet.') }}</li>
                        @endforelse
                    </ul>
                </div>

                <a href="{{ route('chatbot.widget') }}" class="cg-btn-secondary w-full sm:w-auto">{{ __('Prefer to just talk it through?') }}</a>

            @elseif ($user->role === 'tanod')

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('curfew.create') }}" class="cg-btn">{{ __('Log Curfew') }}</a>
                    <a href="{{ route('admin.dashboard') }}" class="cg-btn-secondary">{{ __('Incident Queue') }}</a>
                </div>

                <div class="cg-card">
                    <h3 class="mb-3 font-semibold text-gray-900 dark:text-gray-100">{{ __('My assigned reports') }} ({{ $assigned->count() }})</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($assigned as $report)
                            <li>
                                <a href="{{ route('admin.reports.details', $report) }}" class="flex min-h-[64px] items-center justify-between gap-3 py-3">
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-gray-900 dark:text-gray-100">{{ $report->category->name }}</span>
                                        <span class="block truncate text-sm cg-muted">{{ \Illuminate\Support\Str::title($report->location_text) }} &middot; {{ $report->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="cg-sev cg-sev-{{ $report->severity }}">{{ __(ucfirst($report->severity)) }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm cg-muted">{{ __('Nothing assigned to you right now.') }}</li>
                        @endforelse
                    </ul>
                </div>

            @else

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ([
                        ['Critical open', $stats['critical'], ['tab' => 'active', 'severity' => 'critical'], 'cg-kpi'],
                        ['Unassigned', $stats['unassigned'], ['tab' => 'active', 'assignee' => 'none'], 'cg-kpi'],
                        ['Pending 48h+', $stats['overdue'], ['tab' => 'pending'], 'cg-kpi'],
                    ] as [$label, $count, $params, $color])
                        <a href="{{ route('admin.dashboard', $params) }}" class="cg-card block transition hover:shadow-md">
                            <p class="text-sm cg-muted">{{ $label }}</p>
                            <p class="text-3xl font-semibold {{ $color }}">{{ $count }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="cg-card">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Needs attention') }}</h3>
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-maroon-700 hover:underline dark:text-maroon-300">{{ __('Open incident queue') }}</a>
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($urgent as $report)
                            <li>
                                <a href="{{ route('admin.reports.details', $report) }}" class="flex min-h-[56px] items-center justify-between gap-3 py-3">
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-gray-900 dark:text-gray-100">{{ $report->category->name }}</span>
                                        <span class="block truncate text-sm cg-muted">{{ \Illuminate\Support\Str::title($report->location_text) }} &middot; {{ $report->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="flex items-center gap-3">
                                        <span class="cg-sev cg-sev-{{ $report->severity }}">{{ __(ucfirst($report->severity)) }}</span>
                                        @unless ($report->assigned_to)<span class="rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">{{ __('Unassigned') }}</span>@endunless
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm cg-muted">{{ __('No open reports.') }}</li>
                        @endforelse
                    </ul>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>

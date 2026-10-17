<x-app-layout>
    <x-slot name="headerWidth">max-w-5xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    @php $first = \Illuminate\Support\Str::before($user->name, ' '); @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="cg-dashboard-welcome rounded-xl border px-5 py-6 sm:px-7 sm:py-7" data-reveal>
                <p class="cg-kicker">{{ __('Barangay Maimpis') }} <span class="mx-1 text-stone-400 dark:text-gray-500" aria-hidden="true">/</span> {{ __(ucfirst($user->role)) }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950 dark:text-white sm:text-4xl">{{ __('Good day,') }} {{ $first }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-700 dark:text-gray-300 sm:text-base">
                    @if ($user->role === 'resident')
                        {{ __('Track your reports and see the latest updates.') }}
                    @elseif ($user->role === 'tanod')
                        {{ __('Review your assignments and help keep your community safe.') }}
                    @else
                        {{ __('Stay on top of incidents and help your team respond with care.') }}
                    @endif
                </p>
            </div>

            @if ($user->role === 'resident')

                <a data-reveal href="{{ route('reports.create') }}" class="group flex min-h-24 items-center justify-between gap-4 rounded-xl cg-surface-raised p-5 ring-1 ring-stone-200 transition-colors hover:bg-[#e9e3da] focus-visible:outline-offset-4 sm:p-6 dark:bg-[#262624] dark:ring-[#3d3d3a] dark:hover:bg-[#30302e]" data-reveal>
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-wider text-maroon-700 dark:text-[#c27872]">{{ __('Need help from the barangay?') }}</span>
                        <span class="mt-1 block text-lg font-bold text-stone-950 dark:text-white sm:text-xl">{{ __('Report an incident') }}</span>
                        <span class="mt-0.5 block text-sm text-stone-600 dark:text-gray-300">{{ __('It takes about a minute. You can follow the response here.') }}</span>
                    </span>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-maroon-700 text-xl text-white transition-colors group-hover:bg-maroon-800" aria-hidden="true">&rarr;</span>
                </a>

                @if ($needsFeedback > 0)
                    <a href="{{ route('reports.index') }}" class="block rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 transition hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                        <strong>{{ __('Was this fixed?') }}</strong>
                        {{ $needsFeedback }} {{ __('resolved report(s) are waiting for your answer.') }}
                    </a>
                @endif

                <div class="cg-card" data-reveal>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Latest reports') }}</h3>
                        <a href="{{ route('reports.index') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-maroon-700 hover:underline dark:text-[#c27872]">{{ __('View all') }}</a>
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

                <a data-reveal href="{{ route('reports.create') }}" class="cg-btn-secondary self-start sm:self-auto">{{ __('Prefer to just talk it through?') }} <span aria-hidden="true">&rarr;</span></a>

            @elseif ($user->role === 'tanod')

                <div class="flex flex-wrap gap-3" data-reveal>
                    <a href="{{ route('curfew.create') }}" class="cg-btn">{{ __('Log Curfew') }}</a>
                    <a href="{{ route('admin.dashboard') }}" class="cg-btn-secondary">{{ __('Incident Queue') }}</a>
                </div>

                <div class="cg-card" data-reveal>
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
                        <a href="{{ route('admin.dashboard', $params) }}" class="cg-card block transition-colors" data-reveal>
                            <p class="text-sm cg-muted">{{ $label }}</p>
                            <p class="text-3xl font-semibold {{ $color }}">{{ $count }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="cg-card" data-reveal>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Needs attention') }}</h3>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-maroon-700 hover:underline dark:text-[#c27872]">{{ __('Open incident queue') }}</a>
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

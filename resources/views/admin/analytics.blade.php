<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('admin.partials.report-tabs', ['activeTab' => 'analytics'])

            <div class="flex justify-end">
                <x-export-button :href="route('admin.analytics.exportPdf')" />
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $avg = $avgResolve;
                    $kpis = [
                        [__('Total Reports'), $totalReports, 'all', 'cg-kpi', null],
                        [__('Pending'), $pendingCount, 'pending', 'cg-kpi', null],
                        [__('In Progress'), $inProgressCount, 'in_progress', 'cg-kpi', null],
                        [__('Resolved'), $resolvedCount, 'resolved', 'cg-kpi', __('Avg. :duration to resolve', ['duration' => $avg])],
                    ];
                @endphp
                @foreach ($kpis as [$label, $value, $tab, $color, $note])
                    <a href="{{ route('admin.dashboard', ['tab' => $tab]) }}" class="cg-card block text-center focus:outline-none focus:ring-2 focus:ring-maroon-600">
                        <p class="text-3xl font-bold {{ $color }}">{{ $value }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ __(ucfirst($label)) }}</p>
                        @if ($note)<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $note }}</p>@endif
                    </a>
                @endforeach
            </div>
            @php
                $categoryMax = max(1, (int) $byCategory->max('total'));
                $severityTotal = (int) $bySeverity->sum('total');
                $severityCircumference = 251.327;
                $severityOffset = 0;
                $severityColors = ['low' => '#9ca3af', 'moderate' => '#eab308', 'high' => '#f97316', 'critical' => '#dc2626'];
                $dailyMax = max(1, (int) $last14Days->max('total'));
                $trendPointList = $last14Days->values()->map(function ($day, $index) use ($last14Days, $dailyMax) {
                    $x = 40 + ($index / max(1, $last14Days->count() - 1)) * 920;
                    $y = 160 - ($day->total / $dailyMax) * 120;
                    return round($x, 1) . ',' . round($y, 1);
                })->implode(' ');
                $trendAreaPoints = "40,160 {$trendPointList} 960,160";
            @endphp
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <section class="cg-card" aria-labelledby="analytics-category-title">
                    <h3 id="analytics-category-title" class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __("Reports by Category") }}</h3>
                    @if ($byCategory->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __("No reports available.") }}</p>
                    @else
                        <ul class="space-y-4">
                            @foreach ($byCategory as $category)
                                <li>
                                    <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                        <span class="truncate text-gray-700 dark:text-gray-300">{{ __($category->name) }}</span>
                                        <span class="shrink-0 font-semibold text-gray-700 dark:text-gray-200">{{ $category->total }}</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10" role="meter" aria-label="{{ __(':name reports', ['name' => __($category->name)]) }}" aria-valuemin="0" aria-valuemax="{{ $categoryMax }}" aria-valuenow="{{ $category->total }}">
                                        <div class="h-full rounded-full bg-maroon-700 dark:bg-[#b56764]" style="width: {{ round($category->total / $categoryMax * 100) }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
                <section class="cg-card" aria-labelledby="analytics-severity-title">
                    <h3 id="analytics-severity-title" class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __("Reports by Severity") }}</h3>
                    <div class="flex flex-col items-center gap-5 sm:flex-row">
                        <svg class="h-32 w-32 shrink-0" viewBox="0 0 100 100" role="img" aria-label="{{ __(':count reports by severity', ['count' => $severityTotal]) }}">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#e7e5e4" stroke-width="14" class="dark:stroke-gray-700" transform="rotate(-90 50 50)" />
                            @foreach ($bySeverity as $severity => $summary)
                                @php
                                    $segmentLength = $severityTotal > 0 ? $summary->total / $severityTotal * $severityCircumference : 0;
                                    $segmentGap = $severityCircumference - $segmentLength;
                                    $segmentStart = $severityOffset;
                                    $severityOffset += $segmentLength;
                                @endphp
                                <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $severityColors[$severity] ?? '#951f3d' }}" stroke-width="14" stroke-dasharray="{{ number_format($segmentLength, 3, '.', '') }} {{ number_format($segmentGap, 3, '.', '') }}" stroke-dashoffset="{{ number_format(-$segmentStart, 3, '.', '') }}" transform="rotate(-90 50 50)" />
                            @endforeach
                            <text x="50" y="50" text-anchor="middle" dominant-baseline="middle" fill="currentColor" class="text-gray-900 dark:text-white" font-size="16" font-weight="700">{{ $severityTotal }}</text>
                        </svg>
                        <ul class="w-full space-y-2 text-sm">
                            @foreach (['low' => 'Low', 'moderate' => 'Moderate', 'high' => 'High', 'critical' => 'Critical'] as $severity => $label)
                                <li class="flex items-center justify-between gap-3">
                                    <span class="inline-flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $severityColors[$severity] }}" aria-hidden="true"></span>
                                        {{ __(ucfirst($label)) }}
                                    </span>
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $bySeverity->get($severity)->total ?? 0 }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>

            <section class="cg-card" aria-labelledby="analytics-trend-title">
                <h3 id="analytics-trend-title" class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __("Reports Over Last 14 Days") }}</h3>
                <svg class="h-48 w-full overflow-visible" viewBox="0 0 1000 200" preserveAspectRatio="none" role="img" aria-label="{{ __('Daily report totals over the last 14 days') }}">
                    <title>{{ __('Daily reports from :start through :end', ['start' => $last14Days->first()->day, 'end' => $last14Days->last()->day]) }}</title>
                    <line x1="40" y1="40" x2="960" y2="40" stroke="currentColor" stroke-opacity=".12" />
                    <line x1="40" y1="100" x2="960" y2="100" stroke="currentColor" stroke-opacity=".12" />
                    <line x1="40" y1="160" x2="960" y2="160" stroke="currentColor" stroke-opacity=".2" />
                    <text x="2" y="44" class="fill-gray-500 dark:fill-gray-400" font-size="14">{{ $dailyMax }}</text>
                    <text x="2" y="104" class="fill-gray-500 dark:fill-gray-400" font-size="14">{{ round($dailyMax / 2) }}</text>
                    <text x="2" y="164" class="fill-gray-500 dark:fill-gray-400" font-size="14">0</text>
                    <polygon points="{{ $trendAreaPoints }}" fill="#951f3d" fill-opacity=".1" />
                    <polyline points="{{ $trendPointList }}" fill="none" stroke="#951f3d" stroke-width="4" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                    @foreach ($last14Days as $day)
                        @php
                            $pointX = 40 + ($loop->index / max(1, $last14Days->count() - 1)) * 920;
                            $pointY = 160 - ($day->total / $dailyMax) * 120;
                        @endphp
                        <circle cx="{{ round($pointX, 1) }}" cy="{{ round($pointY, 1) }}" r="4" fill="#951f3d">
                            <title>{{ \App\Support\LocalizedDate::format(\Illuminate\Support\Carbon::parse($day->day), 'M j') }}: {{ __(':count reports', ['count' => $day->total]) }}</title>
                        </circle>
                    @endforeach
                </svg>
                <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ \App\Support\LocalizedDate::format(\Illuminate\Support\Carbon::parse($last14Days->first()->day), 'M j') }}</span>
                    <span>{{ \App\Support\LocalizedDate::format(\Illuminate\Support\Carbon::parse($last14Days->get(6)->day), 'M j') }}</span>
                    <span>{{ \App\Support\LocalizedDate::format(\Illuminate\Support\Carbon::parse($last14Days->last()->day), 'M j') }}</span>
                </div>
            </section>

            <div class="cg-card">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __("Severity Rated By") }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __("A report counts under each model that rated it, so a multi-model report can appear more than once.") }}</p>
                @php $usageMax = max(1, max(collect($modelUsage)->except('Before tracking')->all() ?: [0])); @endphp
                <ul class="space-y-3">
                    @foreach ($modelUsage as $label => $count)
                        @continue($label === 'Before tracking')
                        <li>
                            <div class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300 mb-1">
                                <span class="inline-flex items-center gap-1.5">
                                    @if ($label === 'Gemini')
                                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <defs>
                                                <linearGradient id="cg-gemini-logo-{{ $loop->index }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#4E87F8"/>
                                                    <stop offset="100%" stop-color="#9B51E0"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12Z" fill="url(#cg-gemini-logo-{{ $loop->index }})"/>
                                        </svg>
                                    @elseif ($label === 'Groq')
                                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <rect width="24" height="24" rx="5" fill="#F55036"/>
                                            <path d="M15.5 12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm0 0v4a2 2 0 0 1-2 2h-1" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    @elseif ($label === 'Keyword rules')
                                        <svg class="h-3.5 w-3.5 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 12h9.75M10.5 18h9.75M3.75 6H6m-2.25 6H6m-2.25 6H6"/>
                                        </svg>
                                    @elseif ($label === 'Model vote')
                                        <svg class="h-3.5 w-3.5 shrink-0 text-maroon-700 dark:text-[#c27872]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h11m0 0-3-3m3 3-3 3M16 17H5m0 0 3 3m-3-3 3-3"/>
                                        </svg>
                                    @endif
                                    <span>{{ __(ucfirst($label)) }}</span>
                                </span>
                                <span class="font-semibold">{{ $count }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 dark:bg-white/10">
                                <div class="h-2 rounded-full bg-maroon-700 dark:bg-[#b56764]" style="width: {{ round($count / $usageMax * 100) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div id="ai-insights-section" class="cg-card">
                <div id="ai-insights-prompt" class="text-center py-6">
                    <svg class="h-8 w-8 text-maroon-700 dark:text-[#c27872] mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __("Get AI-generated patterns, risks, and recommendations based on this data.") }}</p>
                    <button id="load-insights-btn" class="cg-btn">
                        <span id="load-insights-label">{{ __("Load AI Insights") }}</span>
                        <svg id="load-insights-spinner" class="hidden animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>

                <div id="ai-insights-results" class="hidden space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __("AI Summary") }}</h3>
                        <p id="ai-summary-text" class="text-sm text-gray-700 dark:text-gray-300"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __("Recommended Actions") }}</h3>
                            <ul id="ai-recommendations-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __("Observed Patterns") }}</h3>
                            <ul id="ai-patterns-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __("Risk Flags") }}</h3>
                            <ul id="ai-risks-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                    </div>
                    <p id="ai-model-note" class="text-xs text-gray-500 dark:text-gray-400 mt-4 flex items-center gap-1.5"></p>
                </div>
            </div>

        </div>
    </div>

    <script nonce="{{ Vite::cspNonce() }}">
        function fillList(id, items, bulletColor) {

            const list = document.getElementById(id);
            list.innerHTML = '';
            if (!items || items.length === 0) {
                list.innerHTML = '<li class="text-gray-500 dark:text-gray-400">None detected.</li>';
                return;
            }
            items.forEach(text => {
                const li = document.createElement('li');
                li.className = 'flex gap-2';
                li.innerHTML = `<span class="${bulletColor}">&bull;</span><span></span>`;
                li.querySelector('span:last-child').textContent = text;
                list.appendChild(li);
            });
        }

        document.getElementById('load-insights-btn')?.addEventListener('click', async () => {
            const btn = document.getElementById('load-insights-btn');
            btn.disabled = true;
            document.getElementById('load-insights-label').textContent = @js(__('Analyzing Data...'));
            document.getElementById('load-insights-spinner').classList.remove('hidden');

            try {
                const response = await fetch("{{ route('admin.analytics.insights') }}");
                const data = await response.json();

                document.getElementById('ai-summary-text').textContent = data.analysisSummary;
                fillList('ai-recommendations-list', data.recommendations, 'text-maroon-700');
                fillList('ai-patterns-list', data.patterns, 'text-maroon-700');
                fillList('ai-risks-list', data.riskFlags, 'text-red-600');

                const modelName = data.model || '';
                let modelIcon = '';
                if (/gemini/i.test(modelName)) {
                    modelIcon = '<svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><defs><linearGradient id="ai-gemini-logo" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#4E87F8"/><stop offset="100%" stop-color="#9B51E0"/></linearGradient></defs><path d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12Z" fill="url(#ai-gemini-logo)"/></svg>';
                } else if (/groq/i.test(modelName)) {
                    modelIcon = '<svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#F55036"/><path d="M15.5 12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm0 0v4a2 2 0 0 1-2 2h-1" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/></svg>';
                }

                const modelNoteEl = document.getElementById('ai-model-note');
                const modelText = data.model
                    ? @js(__('Insights written by :model.', ['model' => '__MODEL__'])).replace('__MODEL__', data.model)
                    : @js(__('Insights built from the data with built-in rules (AI unavailable).'));
                modelNoteEl.replaceChildren();
                if (modelIcon) modelNoteEl.insertAdjacentHTML('beforeend', modelIcon);
                const noteText = document.createElement('span');
                noteText.textContent = modelText;
                modelNoteEl.append(noteText);

                document.getElementById('ai-insights-prompt').classList.add('hidden');
                document.getElementById('ai-insights-results').classList.remove('hidden');
            } catch (err) {
                document.getElementById('load-insights-label').textContent = @js(__('Failed — Try Again'));
                document.getElementById('load-insights-spinner').classList.add('hidden');
                btn.disabled = false;
            }
        });
    </script>
</x-app-layout>

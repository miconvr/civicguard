<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __("Incident Queue") }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ open: false, r: null }" @keydown.escape.window="open = false">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div role="status" class="font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Summary cards (each one is a filter) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['critical', __('Critical open'), $stats['critical'], ['tab' => 'active', 'severity' => 'critical'], 'cg-kpi'],
                    ['unassigned', __('Unassigned'), $stats['unassigned'], ['tab' => 'active', 'assignee' => 'none'], 'cg-kpi'],
                    ['overdue', __('Pending 48h+'), $stats['overdue'], ['tab' => 'pending'], 'cg-kpi'],
                    ['resolved', __('Resolved this week'), $stats['resolved'], ['tab' => 'resolved'], 'cg-kpi'],
                ] as [$key, $label, $count, $params, $color])
                    <a href="{{ route('admin.dashboard', $params) }}" class="cg-card p-4 focus:outline-none focus:ring-2 focus:ring-maroon-600" data-reveal>
                        <div class="text-sm text-ink dark:text-gray-300">{{ __($label) }}</div>
                        <div class="text-3xl font-semibold {{ $color }}" data-testid="queue-stat-value-{{ $key }}">{{ $count }}</div>
                    </a>
                @endforeach
            </div>

            <div class="cg-card p-6" data-reveal>
                {{-- Single filter bar --}}
                <form method="GET" class="mb-4 flex flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    @if (request('assignee') === 'none')
                        <input type="hidden" name="assignee" value="none">
                    @endif
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by category, location, or report details…') }}"
                           class="cg-select w-full sm:w-80 lg:w-96" aria-label="{{ __('Search reports') }}">
                    <select name="severity" onchange="this.form.submit()" class="cg-select w-full sm:w-auto" aria-label="{{ __("Filter by severity") }}">
                        <option value="">{{ __("All severities") }}</option>
                        @foreach (['critical', 'high', 'moderate', 'low'] as $s)
                            <option value="{{ $s }}" @selected(request('severity') === $s)>{{ __(ucfirst($s)) }}</option>
                        @endforeach
                    </select>
                    @if (request('assignee') === 'none')
                        <a href="{{ route('admin.dashboard', request()->except('assignee', 'page')) }}"
                           class="text-xs rounded-full bg-amber-100 text-amber-800 px-3 py-1 dark:bg-amber-900/40 dark:text-amber-300">{{ __("Unassigned only ✕") }}</a>
                    @endif
                    <x-export-button class="ml-auto" :href="route('admin.reports.exportPdf', array_filter(['group' => $tab === 'resolved' ? 'resolved' : 'active', 'severity' => request('severity')]))" />
                </form>

                {{-- Status tabs --}}
                <nav class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700 text-sm mb-4" aria-label="{{ __('Status') }}">
                    @foreach (['active' => 'Active', 'pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved', 'all' => 'All'] as $key => $label)
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('page'), ['tab' => $key])) }}"
                           @if ($tab === $key) aria-current="page" @endif
                           class="inline-flex min-h-11 items-center px-3 sm:px-4 -mb-px {{ $tab === $key ? 'border-b-2 border-maroon-700 font-semibold text-maroon-700 dark:text-white dark:border-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __($label) }}
                        </a>
                    @endforeach
                </nav>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table cg-queue">
                        <thead>
                            <tr>
                                <th>{{ __("Severity") }}</th>
                                <th>{{ __("Report") }}</th>
                                <th>{{ __("Age") }}</th>
                                <th>{{ __("Status") }}</th>
                                <th>{{ __("Assigned") }}</th>
                                <th><span class="sr-only">{{ __("Open") }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                @php
                                    $stale = $report->status === 'pending' && $report->created_at->lt(now()->subHours(48));
                                    $statusPill = [
                                        'pending' => 'bg-amber-100 text-amber-950 dark:bg-amber-900/40 dark:text-amber-200',
                                        'in_progress' => 'bg-blue-100 text-blue-950 dark:bg-blue-900/40 dark:text-blue-200',
                                        'resolved' => 'bg-green-100 text-green-950 dark:bg-green-900/40 dark:text-green-200',
                                    ][$report->status];
                                    $reporterVerified = $report->user->hasVerifiedResidence();
                                    $payload = [
                                        'id' => $report->id,
                                        'category' => __($report->category->name),
                                        'user' => $report->user->name,
                                        'reporterVerified' => $reporterVerified,
                                        'location' => $report->location_text,
                                        'description' => $report->description,
                                        'severity' => __(ucfirst($report->severity)),
                                        'sev' => $report->severity,
                                        'canStatus' => auth()->user()->role !== 'tanod' || (int) $report->assigned_to === (int) auth()->id(),
                                        'sevClass' => 'cg-sev-' . $report->severity,
                                        'status' => __(ucwords(str_replace('_', ' ', $report->status))),
                                        'assigned_to' => (string) $report->assigned_to,
                                        'filed' => \App\Support\LocalizedDate::format($report->created_at, 'M d, Y g:i A'),
                                        'photo' => $report->photo_path ? route('admin.reports.photo', $report) : null,
                                        'lat' => $report->latitude,
                                        'lng' => $report->longitude,
                                        'details' => route('admin.reports.details', $report),
                                    ];
                                @endphp
                                <tr class="cursor-pointer hover:bg-black/5 dark:hover:bg-white/5" tabindex="0"
                                    @click="r = {{ Js::from($payload) }}; open = true"
                                    @keydown.enter="r = {{ Js::from($payload) }}; open = true">
                                    <td data-label="Severity"><span class="cg-sev cg-sev-{{ $report->severity }}">{{ __(ucfirst($report->severity)) }}</span></td>
                                    <td data-label="Report">
                                        <div class="font-medium">{{ __($report->category->name) }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ Str::title($report->location_text) }}</div>
                                        <div class="mt-1 flex min-w-0 items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                            <span class="min-w-0 truncate" title="{{ __('Reported by') }} {{ $report->user->name }}">{{ __('Reported by') }} {{ $report->user->name }}</span>
                                            @if ($reporterVerified)
                                                <span title="{{ __('Verified resident') }}" class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
                                                    {{ __('Verified') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Age" class="whitespace-nowrap {{ $stale ? 'font-semibold text-red-700 dark:text-red-400' : '' }}">
                                        {{ \App\Support\LocalizedDate::relative($report->created_at) }}
                                    </td>
                                    <td data-label="Status">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusPill }}">
                                            {{ __(ucwords(str_replace('_', ' ', $report->status))) }}
                                        </span>
                                    </td>
                                    <td data-label="Assigned">
                                        @if ($report->assignedTo)
                                            {{ $report->assignedTo->name }}
                                        @elseif ($report->status !== 'resolved')
                                            <span class="rounded bg-amber-50 text-amber-800 px-2 py-0.5 text-xs dark:bg-amber-900/40 dark:text-amber-300">{{ __("Unassigned") }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right text-gray-400">›</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">{{ __("No reports match these filters.") }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $reports->links() }}</div>
            </div>
        </div>

        {{-- Drawer: all editing lives here --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-[var(--cg-ease-in)] duration-[var(--cg-motion-exit)]" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/40" @click="open = false"></div>
        <aside x-show="open" x-cloak
               x-transition:enter="transform transition ease-[var(--cg-ease-out)] duration-[var(--cg-motion-enter)]" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
               x-transition:leave="transform transition ease-[var(--cg-ease-in)] duration-[var(--cg-motion-exit)]" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
               role="dialog" aria-modal="true" aria-label="{{ __("Report details") }}"
               class="fixed inset-y-0 right-0 z-50 w-full max-w-md overflow-y-auto cg-surface-raised p-4 sm:p-6 shadow-xl">
            <template x-if="r">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="r.category"></h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="r.location"></p>
                        </div>
                        <button type="button" @click="open = false" aria-label="Close" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-gray-400 hover:bg-black/5 hover:text-gray-700 dark:hover:bg-white/5 dark:hover:text-white text-xl leading-none">✕</button>
                    </div>

                    <div class="flex min-w-0 items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <span class="cg-sev shrink-0" :class="r.sevClass" x-text="r.severity"></span>
                        <span class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                            <span class="min-w-0 truncate">{{ __('Reported by') }} <strong x-text="r.user"></strong></span>
                            <span x-show="r.reporterVerified" class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold leading-none text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
                                {{ __('Verified') }}
                            </span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-700 dark:text-gray-300" x-text="'Filed ' + r.filed"></p>

                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="r.description"></p>

                    <template x-if="r.photo">
                        <img :src="r.photo" alt="Incident photo" class="rounded-lg max-w-full h-auto border border-gray-200 dark:border-gray-700">
                    </template>

                    <template x-if="r.lat && r.lng">
                        <a :href="`https://www.google.com/maps?q=${r.lat},${r.lng}`" target="_blank" rel="noopener"
                           class="text-sm text-maroon-700 dark:text-[#c27872] font-semibold hover:underline">{{ __("Open in Maps →") }}</a>
                    </template>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <form method="POST" :action="'{{ url('/admin/reports') }}/' + r.id" x-show="r.canStatus" class="space-y-4">
                        @csrf @method('PATCH')

                        {{-- Severity --}}
                        @if (in_array(auth()->user()->role, ['admin', 'official']))
                            <div class="space-y-2">
                                <label for="report-severity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __("Severity") }}</label>
                                <select id="report-severity" name="severity" x-model="r.sev" class="cg-select w-full">
                                    <option value="low">{{ __("Low") }}</option>
                                    <option value="moderate">{{ __("Moderate") }}</option>
                                    <option value="high">{{ __("High") }}</option>
                                    <option value="critical">{{ __("Critical") }}</option>
                                </select>
                            </div>
                        @endif

                        {{-- Status --}}
                        <div class="space-y-2">
                            <label for="report-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __("Status") }}</label>
                            <select id="report-status" name="status" x-model="r.status" :disabled="!r.canStatus" class="cg-select w-full">
                                <option value="pending">{{ __("Pending") }}</option>
                                <option value="in_progress">{{ __("In progress") }}</option>
                                <option value="resolved">{{ __("Resolved") }}</option>
                            </select>
                        </div>

                        {{-- Assignment --}}
                        @if (in_array(auth()->user()->role, ['admin', 'official']))
                            <div class="space-y-2">
                                <label for="report-assigned-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __("Assigned tanod") }}</label>
                                <select id="report-assigned-to" name="assigned_to" x-model="r.assigned_to" class="cg-select w-full">
                                    <option value="" disabled :hidden="r.assigned_to !== ''">{{ __("Select a tanod") }}</option>
                                    @foreach ($tanods as $tanod)
                                        <option value="{{ $tanod->id }}">{{ $tanod->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="submit" class="min-h-11 w-full rounded-lg bg-maroon-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-maroon-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-maroon-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                            Save changes
                        </button>
                    </form>

                    <p x-show="!r.canStatus" x-cloak class="text-sm text-gray-500 dark:text-gray-400">{{ __("Only the assigned tanod or an admin can update this report.") }}</p>

                    <a :href="r.details" class="block text-sm text-gray-700 dark:text-gray-300 hover:underline">{{ __("Open full details page") }}</a>
                </div>
            </template>
        </aside>
    </div>
</x-app-layout>

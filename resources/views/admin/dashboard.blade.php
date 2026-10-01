<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Incident Queue</h2>
    </x-slot>

    <div class="py-12" x-data="{ open: false, r: null }" @keydown.escape.window="open = false">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Summary cards (each one is a filter) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['Critical open', $stats['critical'], ['tab' => 'active', 'severity' => 'critical'], 'text-red-700 dark:text-red-400'],
                    ['Unassigned', $stats['unassigned'], ['tab' => 'active', 'assignee' => 'none'], 'text-amber-700 dark:text-amber-400'],
                    ['Pending 48h+', $stats['overdue'], ['tab' => 'pending'], 'text-orange-700 dark:text-orange-400'],
                    ['Resolved this week', $stats['resolved'], ['tab' => 'resolved'], 'text-green-700 dark:text-green-400'],
                ] as [$label, $count, $params, $color])
                    <a href="{{ route('admin.dashboard', $params) }}" class="cg-card p-4 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-maroon-600">
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</div>
                        <div class="text-3xl font-semibold {{ $color }}">{{ $count }}</div>
                    </a>
                @endforeach
            </div>

            <div class="cg-card p-6">
                {{-- Single filter bar --}}
                <form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    @if (request('assignee') === 'none')
                        <input type="hidden" name="assignee" value="none">
                    @endif
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search category or location…"
                           class="cg-select w-64" aria-label="Search reports">
                    <select name="severity" onchange="this.form.submit()" class="cg-select" aria-label="Filter by severity">
                        <option value="">All severities</option>
                        @foreach (['critical', 'high', 'moderate', 'low'] as $s)
                            <option value="{{ $s }}" @selected(request('severity') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @if (request('assignee') === 'none')
                        <a href="{{ route('admin.dashboard', request()->except('assignee', 'page')) }}"
                           class="text-xs rounded-full bg-amber-100 text-amber-800 px-3 py-1">Unassigned only ✕</a>
                    @endif
                    <a href="{{ route('admin.reports.exportPdf', array_filter(['group' => $tab === 'resolved' ? 'resolved' : 'active', 'severity' => request('severity')])) }}"
                       class="ml-auto text-sm text-gray-600 dark:text-gray-300 underline hover:text-maroon-700">Export PDF</a>
                </form>

                {{-- Status tabs --}}
                <nav class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700 text-sm mb-4" aria-label="Status">
                    @foreach (['active' => 'Active', 'pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved', 'all' => 'All'] as $key => $label)
                        <a href="{{ route('admin.dashboard', array_merge(request()->except('page'), ['tab' => $key])) }}"
                           @if ($tab === $key) aria-current="page" @endif
                           class="px-4 py-2 -mb-px {{ $tab === $key ? 'border-b-2 border-maroon-700 font-semibold text-maroon-700 dark:text-white dark:border-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead>
                            <tr>
                                <th>Severity</th>
                                <th>Report</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Assigned</th>
                                <th><span class="sr-only">Open</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                @php
                                    $stale = $report->status === 'pending' && $report->created_at->lt(now()->subHours(48));
                                    $statusPill = [
                                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                    ][$report->status];
                                    $payload = [
                                        'id' => $report->id,
                                        'category' => $report->category->name,
                                        'user' => $report->user->name,
                                        'location' => $report->location_text,
                                        'description' => $report->description,
                                        'severity' => ucfirst($report->severity),
                                        'sev' => $report->severity,
                                        'sevClass' => 'cg-sev-' . $report->severity,
                                        'status' => $report->status,
                                        'assigned_to' => (string) $report->assigned_to,
                                        'filed' => $report->created_at->format('M d, Y g:i A'),
                                        'photo' => $report->photo_path ? asset('storage/' . $report->photo_path) : null,
                                        'lat' => $report->latitude,
                                        'lng' => $report->longitude,
                                        'details' => route('admin.reports.details', $report),
                                    ];
                                @endphp
                                <tr class="cursor-pointer hover:bg-black/5 dark:hover:bg-white/5" tabindex="0"
                                    @click="r = {{ Js::from($payload) }}; open = true"
                                    @keydown.enter="r = {{ Js::from($payload) }}; open = true">
                                    <td><span class="cg-sev cg-sev-{{ $report->severity }}">{{ ucfirst($report->severity) }}</span></td>
                                    <td>
                                        <div class="font-medium">{{ $report->category->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::title($report->location_text) }}</div>
                                    </td>
                                    <td class="whitespace-nowrap {{ $stale ? 'font-semibold text-red-700 dark:text-red-400' : '' }}">
                                        {{ $report->created_at->diffForHumans() }}
                                    </td>
                                    <td>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusPill }}">
                                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($report->assignedTo)
                                            {{ $report->assignedTo->name }}
                                        @elseif ($report->status !== 'resolved')
                                            <span class="rounded bg-amber-50 text-amber-800 px-2 py-0.5 text-xs">Unassigned</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right text-gray-400">›</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">No reports match these filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $reports->links() }}</div>
            </div>
        </div>

        {{-- Drawer: all editing lives here --}}
        <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-black/40" @click="open = false"></div>
        <aside x-show="open" x-cloak
               x-transition:enter="transition duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
               role="dialog" aria-modal="true" aria-label="Report details"
               class="fixed inset-y-0 right-0 z-50 w-full max-w-md overflow-y-auto bg-white dark:bg-gray-900 p-6 shadow-xl">
            <template x-if="r">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="r.category"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="r.location"></p>
                        </div>
                        <button type="button" @click="open = false" aria-label="Close" class="text-gray-400 hover:text-gray-700 dark:hover:text-white text-xl leading-none">✕</button>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <span class="cg-sev" :class="r.sevClass" x-text="r.severity"></span>
                        <span>Reported by <strong x-text="r.user"></strong></span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Filed ' + r.filed"></p>

                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="r.description"></p>

                    <template x-if="r.photo">
                        <img :src="r.photo" alt="Incident photo" class="rounded-lg max-w-full h-auto border border-gray-200 dark:border-gray-700">
                    </template>

                    <template x-if="r.lat && r.lng">
                        <a :href="`https://www.google.com/maps?q=${r.lat},${r.lng}`" target="_blank" rel="noopener"
                           class="text-sm text-maroon-700 dark:text-maroon-300 font-semibold hover:underline">Open in Maps →</a>
                    </template>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Severity --}}
                    @if (in_array(auth()->user()->role, ['admin', 'official']))
                        <form method="POST" :action="'{{ url('/admin/reports') }}/' + r.id + '/severity'" class="space-y-2">
                            @csrf @method('PATCH')
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Severity</label>
                            <div class="flex gap-2">
                                <select name="severity" x-model="r.sev" class="cg-select flex-1">
                                    <option value="low">Low</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                                <button class="bg-maroon-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-maroon-800">Save</button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Set automatically from the report text. Correct it if it is wrong.</p>
                        </form>
                    @endif

                    {{-- Status --}}
                    <form method="POST" :action="'{{ url('/admin/reports') }}/' + r.id + '/status'" class="space-y-2">
                        @csrf @method('PATCH')
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="flex gap-2">
                            <select name="status" x-model="r.status" class="cg-select flex-1">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In progress</option>
                                <option value="resolved">Resolved</option>
                            </select>
                            <button class="bg-maroon-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-maroon-800">Save</button>
                        </div>
                    </form>

                    {{-- Assign --}}
                    <form method="POST" :action="'{{ url('/admin/reports') }}/' + r.id + '/assign'" class="space-y-2">
                        @csrf @method('PATCH')
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned tanod</label>
                        <div class="flex gap-2">
                            <select name="assigned_to" x-model="r.assigned_to" class="cg-select flex-1">
                                <option value="" disabled>Select a tanod…</option>
                                @foreach ($tanods as $tanod)
                                    <option value="{{ $tanod->id }}">{{ $tanod->name }}</option>
                                @endforeach
                            </select>
                            <button class="bg-maroon-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-maroon-800">Assign</button>
                        </div>
                    </form>

                    <a :href="r.details" class="block text-sm text-gray-500 dark:text-gray-400 hover:underline">Open full details page</a>
                </div>
            </template>
        </aside>
    </div>
</x-app-layout>

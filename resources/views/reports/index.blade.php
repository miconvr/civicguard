<x-app-layout>
    <x-slot name="headerWidth">max-w-5xl</x-slot>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('My Reports') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Follow each report’s status and updates here.') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 flex items-center gap-2 font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg" role="status">
                    <span class="t-success-check shrink-0" data-state="in" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m5 12.5 4 4L19 7" />
                        </svg>
                    </span>
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Your reports are private to you and barangay staff.') }}</p>
                <a href="{{ route('reports.create') }}" class="cg-btn self-start whitespace-nowrap px-4 text-sm sm:self-auto">
                    {{ __('+ Report New Incident') }}
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($reports as $report)
                    @php
                        $pill = [
                            'pending' => 'tone-amber',
                            'in_progress' => 'tone-blue',
                            'resolved' => 'tone-green',
                        ][$report->status];
                    @endphp
                    <article id="report-{{ $report->id }}" x-data="{ detailsOpen: false }" class="cg-card scroll-mt-20 border border-gray-200 p-5 shadow-sm transition-colors sm:p-6 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $report->category->name }}
                                    <span class="font-normal text-gray-500 dark:text-gray-400">#{{ $report->resident_number }}</span>
                                </h3>
                                <p class="truncate text-sm text-gray-600 dark:text-gray-400">{{ $report->location_text }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $pill }}">
                                {{ __(ucwords(str_replace('_', ' ', $report->status))) }}
                            </span>
                        </div>

                        <p class="mt-2 line-clamp-2 text-sm text-gray-700 dark:text-gray-300">{{ $report->description }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Filed') }} {{ \App\Support\LocalizedDate::format($report->created_at, 'M d, Y g:i A') }}</p>

                        <button type="button" @click="detailsOpen = !detailsOpen" :aria-expanded="detailsOpen.toString()"
                                class="mt-3 inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-medium text-maroon-700 transition hover:bg-maroon-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-maroon-600 dark:text-[#d59690] dark:hover:bg-white/5">
                            <span x-text="detailsOpen ? @js(__('Hide details')) : @js(__('View report details'))"></span>
                            <svg class="h-4 w-4 transition-transform duration-[var(--cg-motion-interaction)]" :class="detailsOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                        </button>

                        <div x-show="detailsOpen" x-cloak x-transition.opacity class="mt-4 space-y-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('What happened') }}</h4>
                                <p class="mt-1 whitespace-pre-line text-sm text-gray-800 dark:text-gray-200">{{ $report->description }}</p>
                            </div>

                            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Location') }}</dt>
                                    <dd class="mt-0.5 text-gray-800 dark:text-gray-200">{{ $report->location_text }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Severity') }}</dt>
                                    <dd class="mt-0.5 text-gray-800 dark:text-gray-200">{{ __(ucfirst($report->severity)) }}</dd>
                                </div>
                            </dl>

                            @if ($report->photo_path)
                                <div>
                                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attached photo') }}</h4>
                                    <a href="{{ route('reports.photo', $report) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-black/20">
                                        <img src="{{ route('reports.photo', $report) }}" alt="{{ __('Photo attached to report #:id', ['id' => $report->id]) }}" loading="lazy" class="max-h-[28rem] w-full object-contain">
                                    </a>
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Select the photo to open it at full size.') }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No photo was attached to this report.') }}</p>
                            @endif
                        </div>

                        @if ($report->assignedTo)
                            @php($tanodAvatar = $report->assignedTo->avatarPath())
                            <div class="mt-4 flex items-center gap-3 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 dark:border-gray-700 dark:bg-white/5">
                                @if ($tanodAvatar)
                                    <img src="{{ route('reports.assignedTanod.photo', ['report' => $report, 'v' => basename($tanodAvatar)]) }}" alt="{{ __('Profile picture of :name', ['name' => $report->assignedTo->name]) }}" width="40" height="40" loading="lazy" decoding="async" class="h-10 w-10 shrink-0 rounded-full object-cover ring-1 ring-stone-200 dark:ring-gray-700">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#fffaf5] text-sm font-semibold text-maroon-800 ring-1 ring-stone-200" aria-hidden="true">{{ strtoupper(mb_substr($report->assignedTo->name, 0, 1)) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Assigned to') }}</p>
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $report->assignedTo->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Barangay Tanod') }}</p>
                                </div>
                            </div>
                        @endif

                        <ol class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" aria-label="{{ __('Progress') }}">
                            @foreach ($report->timeline as $step)
                                <li>
                                    <div class="h-1.5 rounded-full {{ $step['done'] ? 'cg-bar-fill bg-maroon-700 dark:bg-[#b56764]' : 'bg-gray-200 dark:bg-gray-700' }}" @if ($step['done']) style="--cg-bar-delay: {{ $loop->index * 140 + 250 }}ms" @endif></div>
                                    <p class="mt-1.5 text-xs {{ $step['done'] ? 'font-semibold text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ __($step['label']) }}
                                        <span class="sr-only">{{ $step['done'] ? __('(done)') : __('(not yet)') }}</span>
                                    </p>
                                    @if ($step['at'])
                                        <p class="text-[11px] leading-tight text-gray-500 dark:text-gray-400">{{ \App\Support\LocalizedDate::format($step['at'], 'M d') }}<br>{{ \App\Support\LocalizedDate::format($step['at'], 'g:i A') }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                        @if ($report->status === 'resolved' && $report->resolved_at)
                            @if ($report->confirmed_at)
                                <p class="mt-4 rounded-lg bg-green-50 dark:bg-green-900/30 px-3 py-2 text-sm text-green-800 dark:text-green-300">{{ __('Confirmed fixed') }} &middot; {{ \App\Support\LocalizedDate::format($report->confirmed_at, 'M d, Y') }}</p>
                            @elseif ($report->resolved_at->gt(now()->subDays(7)))
                                <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Was this fixed?') }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('You can respond until') }} {{ \App\Support\LocalizedDate::format($report->resolved_at->copy()->addDays(7), 'M d') }}.</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('reports.confirm', $report) }}">
                                            @csrf
                                            <button class="cg-btn">{{ __("Yes, it's fixed") }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('reports.reopen', $report) }}">
                                            @csrf
                                            <button class="cg-btn-secondary">{{ __("No, it's still a problem") }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </article>
                @empty
                    <div class="cg-card border border-gray-200 py-10 text-center shadow-sm dark:border-gray-700">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ __('You haven\'t filed any reports yet.') }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('File your first incident so barangay staff can follow up.') }}</p>
                    </div>
                @endforelse
            </div>
            @if ($reports->hasPages())
                <div class="mt-5">{{ $reports->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

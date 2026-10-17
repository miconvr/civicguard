<x-app-layout>
    <x-slot name="headerWidth">max-w-3xl</x-slot>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('My Reports') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Follow each report’s status and updates here.') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
                <a href="{{ route('reports.create') }}" class="cg-btn self-start sm:self-auto">
                    {{ __('+ Report New Incident') }}
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($reports as $report)
                    @php
                        $pill = [
                            'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                            'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                        ][$report->status];
                    @endphp
                    <article id="report-{{ $report->id }}" class="cg-card scroll-mt-20 border border-gray-200 p-5 shadow-sm transition-colors sm:p-6 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $report->category->name }}
                                    <span class="font-normal text-gray-500 dark:text-gray-400">#{{ $report->id }}</span>
                                </h3>
                                <p class="truncate text-sm text-gray-600 dark:text-gray-400">{{ $report->location_text }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $pill }}">
                                {{ __(ucwords(str_replace('_', ' ', $report->status))) }}
                            </span>
                        </div>

                        <p class="mt-2 line-clamp-2 text-sm text-gray-700 dark:text-gray-300">{{ $report->description }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Filed') }} {{ $report->created_at->format('M d, Y g:i A') }}</p>

                        <ol class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4" aria-label="{{ __('Progress') }}">
                            @foreach ($report->timeline as $step)
                                <li>
                                    <div class="h-1.5 rounded-full {{ $step['done'] ? 'cg-bar-fill bg-maroon-700 dark:bg-[#b56764]' : 'bg-gray-200 dark:bg-gray-700' }}" @if ($step['done']) style="--cg-bar-delay: {{ $loop->index * 140 + 250 }}ms" @endif></div>
                                    <p class="mt-1.5 text-xs {{ $step['done'] ? 'font-semibold text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ __($step['label']) }}
                                        <span class="sr-only">{{ $step['done'] ? __('(done)') : __('(not yet)') }}</span>
                                    </p>
                                    @if ($step['at'])
                                        <p class="text-[11px] leading-tight text-gray-500 dark:text-gray-400">{{ $step['at']->format('M d') }}<br>{{ $step['at']->format('g:i A') }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                        @if ($report->status === 'resolved' && $report->resolved_at)
                            @if ($report->confirmed_at)
                                <p class="mt-4 rounded-lg bg-green-50 dark:bg-green-900/30 px-3 py-2 text-sm text-green-800 dark:text-green-300">{{ __('Confirmed fixed') }} &middot; {{ $report->confirmed_at->format('M d, Y') }}</p>
                            @elseif ($report->resolved_at->gt(now()->subDays(7)))
                                <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Was this fixed?') }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('You can respond until') }} {{ $report->resolved_at->copy()->addDays(7)->format('M d') }}.</p>
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
                        <a href="{{ route('reports.create') }}" class="cg-btn mt-4">
                            {{ __('Report an Incident') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('My Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-6 sm:p-8">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-5">
                    <a href="{{ route('reports.create') }}" class="inline-flex min-h-[44px] items-center bg-maroon-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                        {{ __('+ Report New Incident') }}
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($reports as $report)
                        @php
                            $pill = [
                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                            ][$report->status];
                        @endphp
                        <article id="report-{{ $report->id }}" class="scroll-mt-20 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
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

                            <ol class="mt-4 grid grid-cols-4 gap-2" aria-label="{{ __('Progress') }}">
                                @foreach ($report->timeline as $step)
                                    <li>
                                        <div class="h-1.5 rounded-full {{ $step['done'] ? 'bg-maroon-700 dark:bg-maroon-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
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
                                    <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Was this fixed?') }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('You can respond until') }} {{ $report->resolved_at->copy()->addDays(7)->format('M d') }}.</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('reports.confirm', $report) }}">
                                                @csrf
                                                <button class="min-h-[44px] rounded-lg bg-maroon-700 px-4 py-2 text-sm font-medium text-white hover:bg-maroon-800">{{ __("Yes, it's fixed") }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('reports.reopen', $report) }}">
                                                @csrf
                                                <button class="min-h-[44px] rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __("No, it's still a problem") }}</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </article>
                    @empty
                        <div class="py-10 text-center">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('You haven\'t filed any reports yet.') }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('File your first incident so barangay staff can follow up.') }}</p>
                            <a href="{{ route('reports.create') }}" class="mt-4 inline-flex min-h-[44px] items-center text-sm font-medium text-maroon-700 hover:text-maroon-800 dark:text-maroon-300">
                                {{ __('Report an Incident') }}
                            </a>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="headerWidth">max-w-3xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Resident Verification Result') }}</h2>
    </x-slot>

    @php
        $status = $verification?->status;
        $isVerified = $status === \App\Models\ResidentVerification::VERIFIED;
        $isDeclined = $status === \App\Models\ResidentVerification::REJECTED;
        $isPending = $status === \App\Models\ResidentVerification::PENDING;
        $tone = match (true) {
            $isVerified => [
                'icon' => 'bg-green-600 text-white',
                'title' => 'text-green-800 dark:text-green-300',
                'border' => 'border-green-200 dark:border-green-900',
            ],
            $isDeclined => [
                'icon' => 'bg-red-600 text-white',
                'title' => 'text-red-800 dark:text-red-300',
                'border' => 'border-red-200 dark:border-red-900',
            ],
            $isPending => [
                'icon' => 'bg-amber-500 text-white',
                'title' => 'text-amber-800 dark:text-amber-300',
                'border' => 'border-amber-200 dark:border-amber-900',
            ],
            default => [
                'icon' => 'bg-stone-200 text-stone-600 dark:bg-white/10 dark:text-gray-300',
                'title' => 'text-gray-800 dark:text-gray-200',
                'border' => 'border-[var(--cg-border)]',
            ],
        };
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto space-y-5 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('profile.edit') }}" class="group inline-flex min-h-11 items-center gap-1.5 text-sm font-semibold text-maroon-700 hover:underline dark:text-[#e3aaa4]">
                <span class="inline-block transition-transform duration-[var(--cg-motion-interaction)] ease-[var(--cg-ease-out)] group-hover:-translate-x-0.5" aria-hidden="true">&larr;</span>
                {{ __('Back to profile') }}
            </a>

            <section class="cg-card border-l-4 {{ $tone['border'] }}" aria-labelledby="verification-result-title" data-reveal>
                <div class="flex items-start gap-4">
                    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $tone['icon'] }}" aria-hidden="true">
                        @if ($isVerified)
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
                        @elseif ($isDeclined)
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L8.94 10l-4.72 4.72a.75.75 0 1 0 1.06 1.06L10 11.06l4.72 4.72a.75.75 0 1 0 1.06-1.06L11.06 10l4.72-4.72a.75.75 0 0 0-1.06-1.06L10 8.94 5.28 4.22Z"/></svg>
                        @elseif ($isPending)
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-12.25a.75.75 0 0 0-1.5 0v4.5c0 .27.14.52.37.65l3 1.75a.75.75 0 0 0 .76-1.3l-2.63-1.53V5.75Z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.5a.75.75 0 0 0-1.5 0v.5a.75.75 0 0 0 1.5 0v-.5ZM10 9a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0v-4A.75.75 0 0 1 10 9Z" clip-rule="evenodd"/></svg>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 id="verification-result-title" class="text-sm font-medium cg-muted">{{ __('Your Maimpis residency verification') }}</h3>
                        @if ($isVerified)
                            <p class="mt-1 text-xl font-semibold {{ $tone['title'] }}">{{ __('Approved') }}</p>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ __('Your residency has been verified.') }}</p>
                        @elseif ($isDeclined)
                            <p class="mt-1 text-xl font-semibold {{ $tone['title'] }}">{{ __('Declined') }}</p>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ __('Review the reason below; you may submit a new image from your profile.') }}</p>
                        @elseif ($isPending)
                            <p class="mt-1 text-xl font-semibold {{ $tone['title'] }}">{{ __('Pending review') }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ __('You have not submitted a residency proof image yet.') }}</p>
                        @endif
                    </div>
                </div>
            </section>

            @if ($isDeclined)
                <section class="cg-card border-red-200 bg-red-50 dark:border-red-900 dark:bg-red-950/30" style="--cg-reveal-delay: 80ms" aria-labelledby="decline-reason-title" data-reveal>
                    <h4 id="decline-reason-title" class="text-sm font-semibold text-red-900 dark:text-red-200">{{ __('Reason for decline') }}</h4>
                    <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-red-800 dark:text-red-300">{{ $verification->rejection_reason ?: __('No specific reason was provided. Please contact the barangay office before resubmitting.') }}</p>
                </section>
            @endif

            @if ($isVerified || $isDeclined)
                <div class="flex items-start gap-3 rounded-xl bg-stone-50 p-4 text-sm text-gray-700 dark:bg-white/5 dark:text-gray-300" style="--cg-reveal-delay: 140ms" data-reveal>
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-stone-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
                    <p>{{ __('The proof image was deleted after review to protect your privacy.') }}</p>
                </div>
            @endif

            @if ($isDeclined)
                <div class="flex flex-col gap-3 sm:flex-row" style="--cg-reveal-delay: 200ms" data-reveal>
                    <a href="{{ route('profile.edit') }}#residence-verification" class="cg-btn w-full sm:w-auto">{{ __('Submit another proof image') }}</a>
                    <a href="{{ route('profile.edit') }}" class="cg-btn-secondary inline-flex min-h-11 w-full items-center justify-center px-4 sm:w-auto">{{ __('Back to profile') }}</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

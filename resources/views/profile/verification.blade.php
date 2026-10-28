<x-app-layout>
    <x-slot name="headerWidth">max-w-3xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Residence verification result') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto space-y-5 sm:px-6 lg:px-8">
            <a href="{{ route('profile.edit') }}" class="inline-flex min-h-10 items-center text-sm font-semibold text-maroon-700 hover:underline dark:text-[#e3aaa4]">&larr; {{ __('Back to profile') }}</a>
            <section class="cg-card space-y-5 p-5 sm:p-7" aria-labelledby="verification-result-title">
                <div>
                    <h3 id="verification-result-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Your Maimpis residency verification') }}</h3>
                    @if ($verification?->status === \App\Models\ResidentVerification::VERIFIED)
                        <p class="mt-2 text-sm font-medium text-green-800 dark:text-green-300">{{ __('Approved') }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Your residency has been verified.') }}</p>
                    @elseif ($verification?->status === \App\Models\ResidentVerification::REJECTED)
                        <p class="mt-2 text-sm font-medium text-red-800 dark:text-red-300">{{ __('Declined') }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Review the reason below; you may submit a new image from your profile.') }}</p>
                    @elseif ($verification?->status === \App\Models\ResidentVerification::PENDING)
                        <p class="mt-2 text-sm font-medium text-amber-800 dark:text-amber-300">{{ __('Pending review') }}</p>
                    @else
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('You have not submitted a residency proof image yet.') }}</p>
                    @endif
                </div>

                @if ($verification?->status === \App\Models\ResidentVerification::REJECTED)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950/30">
                        <h4 class="text-sm font-semibold text-red-900 dark:text-red-200">{{ __('Reason for decline') }}</h4>
                        <p class="mt-1 whitespace-pre-line break-words text-sm text-red-800 dark:text-red-300">{{ $verification->rejection_reason ?: __('No specific reason was provided. Please contact the barangay office before resubmitting.') }}</p>
                    </div>
                @endif

                @if (in_array($verification?->status, [\App\Models\ResidentVerification::VERIFIED, \App\Models\ResidentVerification::REJECTED], true))
                    <p class="rounded-xl bg-stone-50 p-4 text-sm text-gray-700 dark:bg-white/5 dark:text-gray-300">{{ __('The proof image was deleted after review to protect your privacy.') }}</p>
                @endif

                @if ($verification?->status === \App\Models\ResidentVerification::REJECTED)
                    <a href="{{ route('profile.edit') }}#residence-verification" class="cg-btn-primary inline-flex min-h-11 items-center justify-center px-4">{{ __('Submit another proof image') }}</a>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>

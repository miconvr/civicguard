<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Forgot your password?') }}</h2>
        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
            {{ __('CivicGuard does not send password-reset emails. Please contact a barangay administrator or official and ask them to set a temporary password for you.') }}
        </p>
    </div>

    <div class="rounded-lg border border-maroon-200 bg-maroon-50 px-4 py-3 text-sm leading-6 text-maroon-900 dark:border-maroon-800 dark:bg-maroon-950/40 dark:text-maroon-100">
        {{ __('For your security, staff will confirm your identity before resetting your password.') }}
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="font-semibold text-maroon-700 underline-offset-2 hover:underline dark:text-gray-200">
            {{ __('Return to login') }}
        </a>
    </div>
</x-guest-layout>

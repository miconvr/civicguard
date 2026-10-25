<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Welcome back') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Log in to report or follow up on an incident.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full min-h-[44px]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full min-h-[44px]" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-maroon-600 shadow-sm focus:ring-maroon-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

        </div>

        <x-primary-button class="mt-6 w-full justify-center min-h-[44px]" ::disabled="submitting" ::aria-busy="submitting">
            <span x-show="!submitting">{{ __('Log in') }}</span>
            <span x-cloak x-show="submitting" class="inline-flex items-center gap-2" role="status">
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                {{ __('Logging in...') }}
            </span>
        </x-primary-button>
    </form>

    <p class="cg-forgot-note mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('password.request') }}" class="font-semibold text-maroon-700 underline-offset-2 hover:underline dark:text-gray-200">
            {{ __('Forgot your password?') }}
        </a>
    </p>

    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('New to CivicGuard?') }}
            <a href="{{ route('register') }}" class="font-semibold text-maroon-700 dark:text-gray-200 hover:underline">
                {{ __('Create an account') }}
            </a>
        </p>
    </div>
</x-guest-layout>

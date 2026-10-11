<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Create your account') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Sign up to report incidents and follow their progress.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full min-h-[44px]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full min-h-[44px]" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full min-h-[44px]" type="password" name="password" required autocomplete="new-password" aria-describedby="password-hint" />
            <p id="password-hint" class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Use at least 8 characters.') }}</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full min-h-[44px]" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="mt-6 w-full justify-center min-h-[44px]" ::disabled="submitting" ::aria-busy="submitting">
            <span x-show="!submitting">{{ __('Register') }}</span>
            <span x-cloak x-show="submitting" class="inline-flex items-center gap-2" role="status">
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                {{ __('Creating account...') }}
            </span>
        </x-primary-button>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="font-semibold text-maroon-700 dark:text-gray-200 hover:underline">
                {{ __('Log in here') }}
            </a>
        </p>
    </div>
</x-guest-layout>

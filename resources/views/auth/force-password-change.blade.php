<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Change your password') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('You must choose a new password before you can continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.force.update') }}">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="current_password" :value="__('Current password')" />
            <x-text-input id="current_password" class="block mt-1 w-full min-h-[44px]" type="password" name="current_password" required autofocus autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('New password')" />
            <x-text-input id="password" class="block mt-1 w-full min-h-[44px]" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full min-h-[44px]" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <x-primary-button class="mt-6 w-full justify-center min-h-[44px]">{{ __('Change password') }}</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm font-semibold text-maroon-700 dark:text-gray-200 hover:underline">{{ __('Log out') }}</button>
    </form>
</x-guest-layout>

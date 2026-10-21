<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" x-data="profilePhotoEditor" @submit.prevent="submit($event)" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Profile picture')" />
            <div class="mt-2 flex items-center gap-4">
                @if ($user->avatarPath())
                    <img src="{{ route('profile.photo', ['v' => basename($user->avatarPath())]) }}" alt="{{ __('Profile picture') }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-stone-200 dark:ring-gray-600">
                @else
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-100 text-xl font-semibold text-maroon-800 dark:bg-white/10 dark:text-white" aria-hidden="true">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                @endif
                <div class="min-w-0 flex-1">
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" x-ref="input" @change="select($event)" class="block min-h-11 w-full text-sm text-gray-700 file:mr-3 file:min-h-11 file:rounded-lg file:border-0 file:bg-maroon-50 file:px-3 file:font-semibold file:text-maroon-800 hover:file:bg-maroon-100 dark:text-gray-300 dark:file:bg-white/10 dark:file:text-gray-100">
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('JPG, PNG, or WebP. Maximum 2 MB.') }}</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            <div x-show="previewUrl" x-cloak class="mt-4 rounded-xl border border-stone-200 p-4 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ __('Preview profile picture') }}</p>
                <div class="mt-3 flex flex-col items-center gap-4 sm:flex-row">
                    <div class="h-40 w-40 shrink-0 overflow-hidden rounded-full bg-stone-100 ring-2 ring-stone-200 dark:bg-gray-800 dark:ring-gray-600">
                        <img :src="previewUrl" alt="{{ __('Preview profile picture') }}" class="h-full w-full object-cover" :style="`transform: scale(${zoom})`">
                    </div>
                    <div class="w-full min-w-0">
                        <label for="avatar_zoom" class="flex items-center justify-between gap-3 text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ __('Adjust crop') }}</span>
                            <span x-text="`${Math.round(zoom * 100)}%`" class="tabular-nums"></span>
                        </label>
                        <input id="avatar_zoom" type="range" min="1" max="2.5" step="0.05" x-model.number="zoom" class="mt-2 min-h-11 w-full accent-maroon-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Drag the slider to zoom in or out. The circle shows what will be saved.') }}</p>
                    </div>
                </div>
                <p x-show="previewError" role="alert" class="mt-3 text-sm text-red-700 dark:text-red-400">{{ __('We could not prepare this image. Please choose another one.') }}</p>
            </div>
            @if ($user->avatarPath())
                <label for="remove_avatar" class="mt-3 inline-flex min-h-11 items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input id="remove_avatar" name="remove_avatar" type="checkbox" value="1" class="rounded border-gray-300 text-maroon-700 shadow-sm focus:ring-maroon-500 dark:border-gray-600 dark:bg-gray-900">
                    {{ __('Remove profile picture') }}
                </label>
            @endif
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="inline-flex min-h-11 items-center text-left underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button x-bind:disabled="processing">
                <span x-show="!processing">{{ __('Save') }}</span>
                <span x-show="processing" x-cloak>{{ __('Preparing image...') }}</span>
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

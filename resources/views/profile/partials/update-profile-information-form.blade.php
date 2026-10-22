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
            <input type="hidden" name="remove_avatar" value="1" disabled x-bind:disabled="!removeExisting">
            <div class="mt-2 flex items-center gap-4">
                <div class="relative inline-flex shrink-0" :class="shakePreview ? 'cg-pending-shake' : ''">
                @if ($user->avatarPath())
                    <img x-show="!removeExisting" src="{{ route('profile.photo', ['v' => basename($user->avatarPath())]) }}" alt="{{ __('Profile picture') }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-stone-200 dark:ring-gray-600">
                    <span x-show="removeExisting" x-cloak class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-100 text-xl font-semibold text-maroon-800 ring-2 ring-stone-200 dark:bg-white/10 dark:text-white dark:ring-gray-600" aria-hidden="true">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <button x-show="!removeExisting && !previewUrl" type="button" @click="markForRemoval()" aria-label="{{ __('Remove profile picture') }}" title="{{ __('Remove profile picture') }}" class="absolute -right-1 -top-1 inline-flex h-8 w-8 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-500 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-800 dark:hover:bg-red-950/40 dark:hover:text-red-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m5 5 10 10M15 5 5 15"/></svg>
                    </button>
                @else
                    <span x-show="!previewUrl" class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-100 text-xl font-semibold text-maroon-800 dark:bg-white/10 dark:text-white" aria-hidden="true">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                @endif
                </div>
                <div class="min-w-0 flex-1">
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" x-ref="input" @change="select($event)" class="block min-h-11 w-full text-sm text-gray-700 file:mr-3 file:min-h-11 file:rounded-lg file:border-0 file:bg-maroon-50 file:px-3 file:font-semibold file:text-maroon-800 file:transition-all file:duration-[var(--cg-motion-interaction)] file:ease-out hover:file:-translate-y-px hover:file:bg-maroon-100 hover:file:text-maroon-900 hover:file:shadow-sm active:file:translate-y-0 active:file:scale-[0.99] dark:text-gray-300 dark:file:bg-white/10 dark:file:text-gray-100 dark:hover:file:bg-white/20 dark:hover:file:text-white">
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('JPG, PNG, or WebP. Maximum 2 MB.') }}</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            <div x-show="previewUrl" x-cloak class="relative mt-4 rounded-xl border border-stone-200 p-4 dark:border-gray-700" :class="shakePreview ? 'cg-pending-shake' : ''">
                <button type="button" @click="clearSelection()" aria-label="{{ __('Remove selected image') }}" title="{{ __('Remove selected image') }}" class="absolute right-2 top-2 inline-flex h-8 w-8 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-500 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-800 dark:hover:bg-red-950/40 dark:hover:text-red-300">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m5 5 10 10M15 5 5 15"/></svg>
                </button>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ __('Preview profile picture') }}</p>
                <div class="mt-3 flex flex-col items-center gap-4 sm:flex-row">
                    <div class="h-40 w-40 shrink-0 overflow-hidden rounded-full bg-stone-100 ring-2 ring-stone-200 dark:bg-gray-700 dark:ring-gray-600">
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
            <div x-show="pendingPhotoChange" x-cloak role="status" class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-stone-600 dark:text-gray-300">
                <p>{{ __('Photo changes are not saved yet. Click Save to apply them.') }}</p>
                <button x-show="removeExisting" type="button" @click="undoRemoval()" class="font-semibold text-maroon-700 underline underline-offset-2 hover:text-maroon-900 dark:text-[#e7a8b3] dark:hover:text-white">{{ __('Undo') }}</button>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
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

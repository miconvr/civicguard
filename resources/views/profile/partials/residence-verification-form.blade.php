<section id="residence-verification">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Verify Maimpis residency') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Submit proof that you live in Barangay Maimpis. A barangay ID or a document showing your name and Maimpis address can be used.') }}</p>
    </header>

    @if ($verification?->status === \App\Models\ResidentVerification::VERIFIED)
        <div class="mt-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-900/20">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-600 text-white" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.07 7.13a1 1 0 0 1-1.42.006L3.29 8.71a1 1 0 1 1 1.42-1.407l4.217 4.26 6.363-6.414a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd"/></svg>
            </span>
            <div>
                <p class="font-semibold text-green-900 dark:text-green-200">{{ __('Your Maimpis residency is verified.') }}</p>
                <p class="mt-1 text-sm text-green-800 dark:text-green-300">{{ __('Your verified badge will stay on your account. Your submitted proof remains private and is available on your verification result page.') }}</p>
            </div>
        </div>
    @elseif ($verification?->status === \App\Models\ResidentVerification::PENDING)
        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-900/20 dark:text-amber-200">
            <p class="font-semibold">{{ __('Your proof is waiting for review.') }}</p>
            <p class="mt-1">{{ __('Barangay officials or administrators will review it. The image stays private and can be viewed by you and authorized reviewers.') }}</p>
        </div>
    @else
        @if ($verification?->status === \App\Models\ResidentVerification::REJECTED)
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900 dark:border-red-900 dark:bg-red-900/20 dark:text-red-200">
                {{ __('The previous proof was not approved. You can submit a different image.') }}
                <a href="{{ route('profile.verification.show') }}" class="ml-1 font-semibold underline">{{ __('View decision and reason') }}</a>
            </div>
        @endif

        <form method="post" action="{{ route('profile.verification.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4"
              x-data="{ previewUrl: null, previewError: false, shakePreview: false, shakeTimer: null, triggerShake() { this.shakePreview = false; cancelAnimationFrame(this.shakeFrame); this.shakeFrame = requestAnimationFrame(() => { this.shakePreview = true; }); clearTimeout(this.shakeTimer); this.shakeTimer = setTimeout(() => { this.shakePreview = false; }, 700); }, select(event) { if (this.previewUrl) URL.revokeObjectURL(this.previewUrl); const file = event.target.files?.[0]; this.previewUrl = file ? URL.createObjectURL(file) : null; this.previewError = false; if (file) this.triggerShake(); }, clearSelection() { if (this.previewUrl) URL.revokeObjectURL(this.previewUrl); this.previewUrl = null; this.previewError = false; this.$refs.proofInput.value = ''; this.shakePreview = false; }, destroy() { if (this.previewUrl) URL.revokeObjectURL(this.previewUrl); clearTimeout(this.shakeTimer); cancelAnimationFrame(this.shakeFrame); } }">
            @csrf
            <div>
                <x-input-label for="verification_proof" :value="__('Proof image')" />
                <input id="verification_proof" name="proof" type="file" accept="image/jpeg,image/png,image/webp" x-ref="proofInput" required x-on:change="select($event)" class="mt-1 block min-h-11 w-full text-sm text-gray-700 file:mr-3 file:min-h-11 file:rounded-lg file:border-0 file:bg-maroon-50 file:px-3 file:font-semibold file:text-maroon-800 file:transition-all file:duration-[var(--cg-motion-interaction)] file:ease-out hover:file:-translate-y-px hover:file:bg-maroon-100 hover:file:shadow-sm dark:text-gray-300 dark:file:bg-white/10 dark:file:text-gray-100 dark:hover:file:bg-white/20">
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('JPG, PNG, or WebP. Maximum 5 MB.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('proof')" />
            </div>

            <div x-show="previewUrl" x-cloak class="relative rounded-xl border border-stone-200 bg-stone-50 p-3 dark:border-gray-700 dark:bg-white/5" :class="shakePreview ? 'cg-pending-shake' : ''">
                <x-image-remove-button x-on:click="clearSelection()" />
                <p class="mb-2 text-sm font-medium text-gray-800 dark:text-gray-100">{{ __('Proof image preview') }}</p>
                <img x-show="!previewError" :src="previewUrl" x-on:error="previewError = true" alt="{{ __('Preview of your proof image') }}" class="max-h-72 w-full rounded-lg object-contain">
                <p x-show="previewError" role="alert" class="text-sm text-red-700 dark:text-red-300">{{ __('This image could not be previewed. Please choose another one.') }}</p>
                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('This is the image that will be sent for review.') }}</p>
                <p class="mt-1 text-xs text-stone-600 dark:text-gray-400">{{ __('Your proof image is ready. Submit it for review.') }}</p>
            </div>

            <p class="rounded-lg bg-stone-50 p-3 text-xs leading-5 text-stone-700 dark:bg-white/5 dark:text-gray-300">{{ __('You and authorized admins and barangay officials can view this private image for residency review. We retain it with the decision until you replace it or delete your account. Cover ID numbers, QR codes, birth dates, and details not needed to show your name and Maimpis residence.') }}</p>

            <label for="verification_consent" class="flex min-h-11 items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
                <input id="verification_consent" name="consent" type="checkbox" value="1" required @checked(old('consent')) class="mt-1 rounded border-gray-300 text-maroon-700 shadow-sm focus:ring-maroon-500 dark:border-gray-600">
                <span>{{ __('I agree that admins and barangay officials may review this image only to verify my Maimpis residency, and that it will be retained with the decision until I replace it or delete my account.') }}</span>
            </label>
            <x-input-error :messages="$errors->get('consent')" />

            <x-primary-button>{{ __('Submit for verification') }}</x-primary-button>
        </form>
    @endif
</section>

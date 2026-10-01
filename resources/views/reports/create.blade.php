<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Report an Incident') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                <div class="mb-5 rounded-lg border-2 border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4 text-sm text-red-800 dark:text-red-200" role="note">
                    <strong>{{ __('In immediate danger?') }}</strong>
                    <strong>{{ __('Call 911') }}</strong> {{ __('first. Reports are reviewed by staff and are not answered instantly.') }}
                </div>

                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Describe what happened and where. Barangay staff will review it and update the status on My Reports.') }}
                </p>

                <a href="{{ route('chatbot.widget') }}" class="mb-6 flex items-center gap-3 rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="h-6 w-6 text-maroon-700 dark:text-maroon-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-maroon-800 dark:text-maroon-300">{{ __('Prefer to just talk it through?') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Use the AI Assistant to describe what happened in your own words.') }}</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6"
                      x-data="{ cat: '{{ old('category_id') }}', descs: {{ Js::from($categories->pluck('description', 'id')) }}, len: {{ strlen(old('description', '')) }}, preview: null, tooBig: false,
                          pick(e) { const f = e.target.files[0]; this.tooBig = f ? f.size > 5242880 : false; this.preview = f && !this.tooBig ? URL.createObjectURL(f) : null; if (this.tooBig) e.target.value = ''; } }">
                    @csrf

                    <div>
                        <label for="category_id" class="cg-label">{{ __('Category') }}</label>
                        <select id="category_id" name="category_id" x-model="cat" required class="cg-input">
                            <option value="" disabled>{{ __('Choose a category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-show="cat && descs[cat]" x-text="descs[cat]"></p>
                        @error('category_id') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="cg-label">{{ __('What happened?') }}</label>
                        <textarea id="description" name="description" rows="5" required maxlength="2000" x-on:input="len = $event.target.value.length" class="cg-input" placeholder="{{ __('Who was involved, what happened, and when. The more detail, the faster staff can act.') }}">{{ old('description') }}</textarea>
                        <p class="mt-1 text-right text-xs text-gray-500 dark:text-gray-400"><span x-text="len"></span> / 2000</p>
                        @error('description') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location_text" class="cg-label">{{ __('Location') }}</label>
                        <input id="location_text" type="text" name="location_text" value="{{ old('location_text') }}" required placeholder="{{ __('e.g. Purok 3, near the basketball court') }}" class="cg-input">
                        @error('location_text') <p class="cg-error">{{ $message }}</p> @enderror
                        <input type="hidden" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude') }}">
                        <button type="button" id="use-location" class="mt-2 inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-60 transition">
                            <span id="use-location-label">{{ __('Pin my current location') }}</span>
                        </button>
                        <p id="location-status" class="mt-1 text-xs text-gray-500 dark:text-gray-400" aria-live="polite"></p>
                        @error('latitude') <p class="cg-error">{{ $message }}</p> @enderror
                        @error('longitude') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="photo" class="cg-label">{{ __('Photo (optional)') }}</label>
                        <input id="photo" type="file" name="photo" accept="image/*" x-on:change="pick($event)" class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100 transition">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Images only, up to 5 MB.') }}</p>
                        <p class="cg-error" x-show="tooBig" x-cloak>{{ __('That photo is larger than 5 MB. Please choose a smaller one.') }}</p>
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Photo preview') }}" class="mt-3 max-h-48 rounded-lg border border-gray-200 dark:border-gray-700">
                        @error('photo') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" id="submit-report-btn" class="cg-btn">
                        <span id="submit-report-label">{{ __('Submit Report') }}</span>
                        <svg id="submit-report-spinner" class="hidden animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        const msg = {{ Js::from([
            'unsupported' => __('Location is not supported by this browser. Please type the location above.'),
            'asking' => __('Finding your location...'),
            'ok' => __('Location pinned. Please still describe the place above so staff can find it.'),
            'denied' => __('We could not get your location. Please type it in the box above instead.'),
            'again' => __('Pin again'),
        ]) }};

        document.getElementById('use-location')?.addEventListener('click', (e) => {
            const btn = e.currentTarget;
            const status = document.getElementById('location-status');
            const label = document.getElementById('use-location-label');
            if (!navigator.geolocation) { status.textContent = msg.unsupported; return; }
            btn.disabled = true;
            status.textContent = msg.asking;
            navigator.geolocation.getCurrentPosition(
                (p) => {
                    document.querySelector('input[name="latitude"]').value = p.coords.latitude.toFixed(7);
                    document.querySelector('input[name="longitude"]').value = p.coords.longitude.toFixed(7);
                    status.textContent = msg.ok;
                    label.textContent = msg.again;
                    btn.disabled = false;
                },
                () => { status.textContent = msg.denied; btn.disabled = false; },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    </script>
</x-app-layout>

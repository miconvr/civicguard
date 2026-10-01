<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Report an Incident') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-6 sm:p-8">

                <div class="mb-6 flex gap-3 rounded-lg border-2 border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4 text-sm text-red-800 dark:text-red-200" role="note">
                    <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <p><strong>{{ __('In immediate danger? Call 911 first.') }}</strong> {{ __('Reports are reviewed by staff and are not answered instantly.') }}</p>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Tell us what happened') }}</h3>
                <p class="mt-1 mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Barangay staff will review your report and update its status on My Reports.') }}
                </p>

                <a href="{{ route('chatbot.widget') }}" class="mb-6 flex items-center gap-3 rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="h-6 w-6 text-maroon-700 dark:text-maroon-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Prefer to just talk it through?') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Use the AI Assistant to describe what happened in your own words.') }}</p>
                    </div>
                </a>

                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">{{ __('Fields marked') }} <span class="text-red-600">*</span> {{ __('are required.') }}</p>

                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6"
                      x-data="{ cat: '{{ old('category_id') }}', descs: {{ Js::from($categories->pluck('description', 'id')) }}, len: {{ strlen(old('description', '')) }}, preview: null, tooBig: false, suggested: false, timer: null,
                          pick(e) { const f = e.target.files[0]; this.tooBig = f ? f.size > 5242880 : false; this.preview = f && !this.tooBig ? URL.createObjectURL(f) : null; if (this.tooBig) e.target.value = ''; },
                          suggest(text) { if (this.cat || text.length < 20) return; clearTimeout(this.timer); this.timer = setTimeout(async () => { try { const r = await fetch('{{ route('reports.suggestCategory') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ description: text }) }); const d = await r.json(); if (d.category_id && !this.cat) { this.cat = String(d.category_id); this.suggested = true; } } catch (e) {} }, 1200); } }">
                    @csrf

                    <div>
                        <label for="description" class="cg-label">{{ __('What happened?') }} <span class="text-red-600" aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" required maxlength="2000" x-on:input="len = $event.target.value.length; suggest($event.target.value)" class="cg-input" placeholder="{{ __('Who was involved, what happened, and when. The more detail, the faster staff can act.') }}">{{ old('description') }}</textarea>
                        <p class="mt-1 text-right text-sm text-gray-600 dark:text-gray-400"><span x-text="len"></span> / 2000</p>
                        @error('description') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="cg-label">{{ __('Category') }} <span class="text-red-600" aria-hidden="true">*</span></label>
                        <select id="category_id" name="category_id" x-model="cat" x-on:change="suggested = false" required class="cg-input min-h-[44px]">
                            <option value="" disabled>{{ __('Choose a category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-show="cat && descs[cat]" x-text="descs[cat]"></p>
                        <p class="mt-1 text-sm font-medium text-maroon-700 dark:text-maroon-300" x-show="suggested" x-cloak>{{ __('Suggested from your description. Change it if it is not right.') }}</p>
                        @error('category_id') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location_text" class="cg-label">{{ __('Location') }} <span class="text-red-600" aria-hidden="true">*</span></label>
                        <input id="location_text" type="text" name="location_text" value="{{ old('location_text') }}" required placeholder="{{ __('e.g. Purok 3, near the basketball court') }}" class="cg-input min-h-[44px]">
                        @error('location_text') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                        <input type="hidden" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude') }}">
                        <button type="button" id="use-location" class="mt-2 inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-60 transition">
                            <span id="use-location-label">{{ __('Pin my current location') }}</span>
                        </button>
                        <p id="location-status" class="mt-1 text-sm text-gray-600 dark:text-gray-400" aria-live="polite"></p>
                        @error('latitude') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                        @error('longitude') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <span class="cg-label">{{ __('Photo (optional)') }}</span>
                        <label for="photo" class="mt-1 flex min-h-[96px] cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-4 text-center text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 focus-within:ring-2 focus-within:ring-maroon-600 transition">
                            <svg class="h-7 w-7 text-maroon-700 dark:text-maroon-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ __('Tap to add a photo') }}</span>
                            <span>{{ __('Images only, up to 5 MB') }}</span>
                            <input id="photo" type="file" name="photo" accept="image/*" x-on:change="pick($event)" class="sr-only">
                        </label>
                        <p class="cg-error" role="alert" x-show="tooBig" x-cloak>{{ __('That photo is larger than 5 MB. Please choose a smaller one.') }}</p>
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Photo preview') }}" class="mt-3 max-h-48 rounded-lg border border-gray-200 dark:border-gray-700">
                        @error('photo') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit" id="submit-report-btn" class="cg-btn min-h-[48px] w-full sm:w-auto">
                            <span id="submit-report-label">{{ __('Submit Report') }}</span>
                            <svg id="submit-report-spinner" class="hidden animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Only barangay staff can see your report. You will be notified when its status changes.') }}</p>
                    </div>
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

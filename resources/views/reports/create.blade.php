<x-app-layout>
    @php
        $initialReportMode = $errors->any()
            ? 'form'
            : (in_array(request()->query('report_mode'), ['assistant', 'form'], true) ? request()->query('report_mode') : null);
    @endphp

    <x-slot name="headerWidth">max-w-2xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Report an Incident') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-6 sm:p-8" x-data="{ reportMode: {{ Js::from($initialReportMode) }}, chatOpen: {{ Js::from($initialReportMode === 'assistant') }}, setReportMode(mode) { this.reportMode = mode; const url = new URL(window.location.href); if (mode) url.searchParams.set('report_mode', mode); else url.searchParams.delete('report_mode'); window.history.replaceState({}, '', url); } }" x-init="if (reportMode && !new URLSearchParams(window.location.search).has('report_mode')) setReportMode(reportMode)" @report-draft-applied.window="setReportMode('form')">

                <section x-show="!reportMode" x-cloak aria-labelledby="report-method-heading">
                    <h3 id="report-method-heading" class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('How would you like to report?') }}</h3>
                    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">{{ __('Choose the option that feels easiest. You can review everything before submitting.') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <button type="button" @click="setReportMode('assistant'); chatOpen = true; $nextTick(() => document.getElementById('chat-input')?.focus())" class="flex min-h-28 w-full items-start gap-3 rounded-xl border-2 border-maroon-700 bg-maroon-50 p-4 text-left transition hover:bg-maroon-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:border-[#b98286] dark:bg-white/5 dark:hover:bg-white/10">
                            <span class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-[#ddc9b7] bg-[#f7eee5]" aria-hidden="true">@include('chatbot.mascot')</span>
                            <span><span class="block font-semibold text-gray-900 dark:text-gray-100">{{ __('Get help from CivicGuard AI') }}</span><span class="mt-1 block text-sm leading-5 text-gray-600 dark:text-gray-300">{{ __('Chat about what happened. The assistant prepares your details for you to review and submit.') }}</span></span>
                        </button>
                        <button type="button" @click="setReportMode('form')" class="flex min-h-28 w-full items-start gap-3 rounded-xl border border-gray-300 bg-white p-4 text-left transition hover:border-maroon-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:border-gray-600 dark:bg-[#262624] dark:hover:bg-white/5">
                            <svg class="mt-0.5 h-6 w-6 shrink-0 text-maroon-700 dark:text-[#c27872]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h12M8 12h12M8 18h12M3.5 6h.01M3.5 12h.01M3.5 18h.01"/></svg>
                            <span><span class="block font-semibold text-gray-900 dark:text-gray-100">{{ __('Fill out the form myself') }}</span><span class="mt-1 block text-sm leading-5 text-gray-600 dark:text-gray-300">{{ __('Enter the details directly, add a photo, or pin your location.') }}</span></span>
                        </button>
                    </div>
                </section>

                <section x-show="reportMode === 'assistant'" x-cloak aria-label="{{ __('AI report assistant') }}">
                    <button type="button" @click="setReportMode(null)" class="mb-3 inline-flex min-h-11 items-center gap-2 text-sm font-medium text-maroon-700 hover:underline dark:text-[#c27872]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/></svg>
                        {{ __('Choose another way to report') }}
                    </button>
                    @include('chatbot.inline')
                </section>

                <section x-show="reportMode === 'form'" x-cloak x-transition:enter="cg-page-enter" x-transition:leave="cg-page-exit" aria-label="{{ __('Incident report form') }}">
                    <button type="button" @click="setReportMode(null)" class="mb-3 inline-flex min-h-11 items-center gap-2 text-sm font-medium text-maroon-700 hover:underline dark:text-[#c27872]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/></svg>
                        {{ __('Choose another way to report') }}
                    </button>
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6"
                      x-data="{ cat: '{{ old('category_id') }}', descs: {{ Js::from($categories->pluck('description', 'id')) }}, len: {{ strlen(old('description', '')) }}, preview: null, tooBig: false, suggested: false, timer: null,
                          pick(e) { const f = e.target.files[0]; this.tooBig = f ? f.size > 5242880 : false; this.preview = f && !this.tooBig ? URL.createObjectURL(f) : null; if (this.tooBig) e.target.value = ''; },
                          suggest(text) { if (this.cat || text.length < 20) return; clearTimeout(this.timer); this.timer = setTimeout(async () => { try { const r = await fetch('{{ route('reports.suggestCategory') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ description: text }) }); const d = await r.json(); if (d.category_id && !this.cat) { this.cat = String(d.category_id); this.suggested = true; } } catch (e) {} }, 1200); } }">
                    @csrf

                    <div>
                        <label for="description" class="cg-label">{{ __('What happened?') }} <span class="text-red-600" aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" required maxlength="2000" x-on:input="len = $event.target.value.length; suggest($event.target.value)" class="cg-input" placeholder="{{ __('Say what happened, and when it happened.') }}">{{ old('description') }}</textarea>
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
                        <p class="mt-1 text-sm font-medium text-maroon-700 dark:text-[#c27872]" x-show="suggested" x-cloak>{{ __('Suggested from your description. Change it if it is not right.') }}</p>
                        @error('category_id') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location_text" class="cg-label">{{ __('Location') }} <span class="text-red-600" aria-hidden="true">*</span></label>
                        <input id="location_text" type="text" name="location_text" value="{{ old('location_text') }}" required placeholder="{{ __('Enter the purok, street, or a nearby landmark') }}" class="cg-input min-h-[44px] w-full">
                        @error('location_text') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                        <input type="hidden" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude') }}">
                        <button type="button" id="use-location" class="mt-2 inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-60 transition">
                            <span id="use-location-label">{{ __('Pin my current location') }}</span>
                        </button>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Optional. Helps staff find the spot.') }}</p>
                        <p id="location-status" class="mt-1 text-sm text-gray-600 dark:text-gray-400" aria-live="polite"></p>
                        @error('latitude') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                        @error('longitude') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <span class="cg-label">{{ __('Photo (optional)') }}</span>
                        <label for="photo" class="mt-1 flex min-h-[72px] cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-3 text-center text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 focus-within:ring-2 focus-within:ring-maroon-600 transition">
                            <svg class="h-5 w-5 text-maroon-700 dark:text-[#c27872]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ __('Tap to add a photo') }}</span>
                            <span>{{ __('Images only, up to 5 MB') }}</span>
                            <input id="photo" type="file" name="photo" accept="image/*" x-on:change="pick($event)" class="sr-only">
                        </label>
                        <p class="cg-error" role="alert" x-show="tooBig" x-cloak>{{ __('That photo is larger than 5 MB. Please choose a smaller one.') }}</p>
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Photo preview') }}" class="mt-3 max-h-48 rounded-lg border border-gray-200 dark:border-gray-700">
                        @error('photo') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Only barangay staff can see your report. You will be notified when its status changes.') }}</p>
                    <div class="pt-2">
                        <button type="submit" id="submit-report-btn" class="cg-btn min-h-[48px] w-full sm:w-auto" data-loading-btn data-loading-text="{{ __('Submitting...') }}">
                            <span id="submit-report-label">{{ __('Submit Report') }}</span>
                            <svg id="submit-report-spinner" class="hidden animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                        
                    </div>
                </form>
                </section>

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

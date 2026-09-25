<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Report an Incident') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Describe what happened and where. Barangay staff will review it and update the status on My Reports.') }}
                </p>

                <a href="{{ route('chatbot.widget') }}" class="mb-6 flex items-center gap-3 rounded-lg border-2 border-maroon-700/30 bg-maroon-50 dark:bg-gray-900 dark:border-maroon-700/50 p-4 hover:bg-maroon-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-6 w-6 text-maroon-700 dark:text-maroon-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-maroon-800 dark:text-maroon-300">{{ __('Prefer to just talk it through?') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Use the AI Assistant to describe what happened in your own words.') }}</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="category_id" class="cg-label">{{ __('Category') }}</label>
                        <select id="category_id" name="category_id" required class="cg-input">
                            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>{{ __('Choose a category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="cg-label">{{ __('Description') }}</label>
                        <textarea id="description" name="description" rows="4" required class="cg-input" placeholder="{{ __('What happened, who was involved, and any details staff should know.') }}">{{ old('description') }}</textarea>
                        @error('description') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location_text" class="cg-label">{{ __('Location') }}</label>
                        <input id="location_text" type="text" name="location_text" value="{{ old('location_text') }}" required placeholder="e.g. Purok 3, near the basketball court" class="cg-input">
                        @error('location_text') <p class="cg-error">{{ $message }}</p> @enderror
                        <input type="hidden" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude') }}">
                        <button type="button" id="use-location" class="mt-2 inline-flex select-none items-center rounded-md px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-800">{{ __('Use My Current Location (Optional)') }}</button>
                        <p id="location-status" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        @error('latitude') <p class="cg-error">{{ $message }}</p> @enderror
                        @error('longitude') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">{{ __('Photo (optional)') }}</label>
                        <input type="file" name="photo" class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100 transition">
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
        document.getElementById('use-location')?.addEventListener('click', () => {
            const status = document.getElementById('location-status');

            if (!navigator.geolocation) {
                status.textContent = 'Location is not supported by this browser.';
                return;
            }

            status.textContent = 'Requesting location...';
            navigator.geolocation.getCurrentPosition(
                position => {
                    document.querySelector('input[name="latitude"]').value = position.coords.latitude.toFixed(7);
                    document.querySelector('input[name="longitude"]').value = position.coords.longitude.toFixed(7);
                    status.textContent = 'Location captured. You can still describe it above.';
                },
                () => {
                    status.textContent = 'Unable to access location. You can enter coordinates manually.';
                }
            );
        });
    </script>
</x-app-layout>

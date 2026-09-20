<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Report an Incident
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="cg-label">Category</label>
                        <select name="category_id" class="cg-input">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Description</label>
                        <textarea name="description" rows="4" class="cg-input">{{ old('description') }}</textarea>
                        @error('description') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Location</label>
                        <input type="text" name="location_text" value="{{ old('location_text') }}" placeholder="e.g. Purok 3, near the basketball court" class="cg-input">
                        @error('location_text') <p class="cg-error">{{ $message }}</p> @enderror
                        <input type="hidden" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude') }}">
                        <button type="button" id="use-location" class="mt-2 text-sm text-maroon-700 font-semibold hover:underline">Use My Current Location (Optional)</button>
                        <p id="location-status" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        @error('latitude') <p class="cg-error">{{ $message }}</p> @enderror
                        @error('longitude') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="cg-label">Photo (optional)</label>
                        <input type="file" name="photo" class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100 transition">
                        @error('photo') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="cg-btn">Submit Report</button>
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

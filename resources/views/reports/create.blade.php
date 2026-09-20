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
</x-app-layout>

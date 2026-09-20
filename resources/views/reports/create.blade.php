<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Report an Incident
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-md rounded-xl border border-gray-100">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location_text" value="{{ old('location_text') }}" placeholder="e.g. Purok 3, near the basketball court" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('location_text') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Photo (optional)</label>
                        <input type="file" name="photo" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100 transition">
                        @error('photo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="bg-maroon-700 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-maroon-800 hover:shadow-md transition font-medium">
                        Submit Report
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
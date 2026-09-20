<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Log Curfew Violation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-md rounded-xl border border-gray-100">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('curfew.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minor's Name</label>
                        <input type="text" name="minor_name" value="{{ old('minor_name') }}" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('minor_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="minor_age" value="{{ old('minor_age') }}" min="0" max="17" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('minor_age') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian's Contact Number</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time of Apprehension</label>
                        <input type="datetime-local" name="apprehension_datetime" value="{{ old('apprehension_datetime') }}" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('apprehension_datetime') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="apprehension_location" value="{{ old('apprehension_location') }}" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('apprehension_location') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Prior Violations</label>
                        <input type="number" name="prior_violations_count" value="{{ old('prior_violations_count', 0) }}" min="0" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">
                        @error('prior_violations_count') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="block w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500 transition">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="bg-maroon-700 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-maroon-800 hover:shadow-md transition font-medium">
                        Log Violation
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
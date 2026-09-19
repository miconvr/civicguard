<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Tanod / Official Account
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.staff.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="tanod">Tanod</option>
                            <option value="official">Barangay Official</option>
                        </select>
                        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Temporary Password</label>
                        <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                        Create Account
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
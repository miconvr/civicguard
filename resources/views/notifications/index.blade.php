<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                <div class="space-y-3">
                    @forelse ($notifications as $notification)
                        <div class="p-3 rounded-md {{ $notification->is_read ? 'bg-gray-50' : 'bg-maroon-50' }}">
                            <p class="text-sm text-gray-800">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No notifications yet.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

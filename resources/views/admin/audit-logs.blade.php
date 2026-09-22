<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Audit Logs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-8">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.auditLogs.exportPdf') }}" class="inline-flex items-center bg-maroon-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                        Export PDF
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                    <table class="cg-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Actor</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($auditLogs as $auditLog)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $auditLog->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $auditLog->user->name ?? 'System' }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $auditLog->action)) }}</td>
                                    <td>{{ $auditLog->description ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">No audit activity yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

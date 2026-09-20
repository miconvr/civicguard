<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            CivicGuard Assistant
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                <div id="chat-log" class="space-y-3 mb-4 max-h-96 overflow-y-auto">
                    <div class="text-sm text-gray-500">
                        Hi! Tell me what happened and I can help you put together a clear incident report.
                    </div>
                </div>

                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="chat-input" placeholder="Type your message..." class="flex-1 border-gray-300 rounded-md shadow-sm" autocomplete="off">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Send</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        let sessionId = null;
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        const log = document.getElementById('chat-log');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            log.innerHTML += `<div class="text-sm text-right"><span class="bg-indigo-100 px-3 py-1 rounded-lg inline-block">${message}</span></div>`;
            input.value = '';
            log.scrollTop = log.scrollHeight;

            const response = await fetch("{{ route('chatbot.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ message, session_id: sessionId })
            });

            const data = await response.json();
            sessionId = data.session_id;

            log.innerHTML += `<div class="text-sm text-left"><span class="bg-gray-100 px-3 py-1 rounded-lg inline-block">${data.reply}</span></div>`;
            log.scrollTop = log.scrollHeight;
        });
    </script>
</x-app-layout>
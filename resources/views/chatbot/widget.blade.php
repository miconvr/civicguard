<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            CivicGuard Assistant
        </h2>
    </x-slot>

    <style>
        .typing-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: currentColor;
            display: inline-block;
            animation: typing-bounce 1.2s infinite ease-in-out;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.15s; }
        .typing-dot:nth-child(3) { animation-delay: 0.3s; }
        @keyframes typing-bounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-4px); opacity: 1; }
        }
    </style>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-6">

                <div id="chat-log" class="space-y-3 mb-4 h-96 overflow-y-auto pr-1">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Hi! Tell me what happened and I can help you put together a clear incident report.
                    </div>
                </div>

                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="chat-input" placeholder="Type your message..." class="cg-input flex-1" autocomplete="off">
                    <button type="submit" id="chat-send" class="cg-btn">Send</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        let sessionId = null;
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send');
        const log = document.getElementById('chat-log');

        function addMessage(text, who) {
            const row = document.createElement('div');
            row.className = 'text-sm ' + (who === 'user' ? 'text-right' : 'text-left');
            const bubble = document.createElement('span');
            bubble.className = 'px-3 py-2 rounded-lg inline-block max-w-[85%] text-left whitespace-pre-wrap ' +
                (who === 'user'
                    ? 'bg-maroon-700 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100');
            bubble.textContent = text;
            row.appendChild(bubble);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        function showTyping() {
            const row = document.createElement('div');
            row.id = 'typing-row';
            row.className = 'text-sm text-left';
            row.innerHTML =
                '<span class="px-3 py-3 rounded-lg inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300">' +
                '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>' +
                '</span>';
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        function hideTyping() {
            document.getElementById('typing-row')?.remove();
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;
            showTyping();

            try {
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
                hideTyping();
                addMessage(data.reply, 'bot');
            } catch (err) {
                hideTyping();
                addMessage('Sorry, something went wrong. Please try again.', 'bot');
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        });
    </script>
</x-app-layout>

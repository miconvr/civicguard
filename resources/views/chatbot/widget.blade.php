<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            CivicGuard AI Assistant
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
        let conversationHistory = '';
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

        function showReviewCard(data) {
            const row = document.createElement('div');
            row.className = 'text-left';

            const card = document.createElement('div');
            card.className = 'border-2 border-maroon-700 rounded-lg p-4 bg-maroon-50 dark:bg-gray-900 space-y-2';

            const title = document.createElement('p');
            title.className = 'text-xs font-semibold uppercase tracking-wider text-maroon-700 dark:text-gray-200';
            title.textContent = 'Review Before Submitting';
            card.appendChild(title);

            const fields = [
                ['Category', data.category_name],
                ['What happened', data.description],
                ['Location', data.location],
            ];
            fields.forEach(([label, value]) => {
                const p = document.createElement('p');
                p.className = 'text-sm text-gray-800 dark:text-gray-100';
                const strong = document.createElement('strong');
                strong.textContent = label + ': ';
                p.appendChild(strong);
                p.appendChild(document.createTextNode(value));
                card.appendChild(p);
            });

            const btnRow = document.createElement('div');
            btnRow.className = 'flex gap-2 pt-2';

            const submitBtn = document.createElement('button');
            submitBtn.className = 'cg-btn text-sm';
            submitBtn.textContent = 'Submit Report';
            submitBtn.onclick = () => submitReport(data, submitBtn);

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'text-sm text-gray-500 dark:text-gray-400 hover:underline';
            cancelBtn.textContent = 'Keep chatting instead';
            cancelBtn.onclick = () => card.closest('div.text-left').remove();

            btnRow.appendChild(submitBtn);
            btnRow.appendChild(cancelBtn);
            card.appendChild(btnRow);

            row.appendChild(card);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        async function submitReport(data, btn) {
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content);
            formData.append('category_id', data.category_id);
            formData.append('description', data.description);
            formData.append('location_text', data.location);

            try {
                const response = await fetch("{{ route('reports.store') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                if (response.ok || response.redirected) {
                    addMessage('Your report has been submitted successfully. You can check its status under "My Reports."', 'bot');
                } else {
                    addMessage('Something went wrong submitting the report. Please try the incident report form directly.', 'bot');
                }
            } catch (err) {
                addMessage('Something went wrong submitting the report. Please try the incident report form directly.', 'bot');
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            conversationHistory += 'Resident: ' + message + '\n';
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
                    body: JSON.stringify({ message, session_id: sessionId, history: conversationHistory })
                });

                const data = await response.json();
                sessionId = data.session_id;
                conversationHistory += 'Assistant: ' + data.reply + '\n';
                hideTyping();
                addMessage(data.reply, 'bot');

                if (data.report_data) {
                    showReviewCard(data.report_data);
                }
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
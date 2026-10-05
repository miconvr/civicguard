<x-app-layout>
    <x-slot name="headerWidth">max-w-2xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('CivicGuard AI Assistant') }}
        </h2>
    </x-slot>

    <style>
        .typing-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #6b7280;
            display: inline-block;
            opacity: 0.35;
            animation: typing-bounce 1.2s infinite ease-in-out both;
        }
        .dark .typing-dot { background: #d1d5db; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
            40% { transform: translateY(-5px); opacity: 1; }
        }
        @keyframes typing-fade {
            0%, 100% { opacity: 0.25; }
            50% { opacity: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            .typing-dot { animation: typing-fade 1.6s infinite ease-in-out both; }
        }
    </style>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card p-4 sm:p-6">

                <div id="chat-log" role="log" aria-live="polite" aria-label="{{ __('Conversation') }}" class="space-y-3 mb-4 overflow-y-auto pr-1" style="height:min(60vh,32rem)">
                    <div class="text-sm text-left">
                        <span class="px-3 py-2 rounded-lg inline-block max-w-[85%] text-left bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                            {{ __('Hi! Tell me what happened and I can help you put together a clear incident report.') }}
                        </span>
                    </div>
                </div>

                <div id="chat-chips" class="mb-3 flex flex-wrap gap-2">
                    @foreach ([__('I want to report an incident'), __('How do I track my report?'), __('What counts as an emergency?'), __('What do the urgency levels mean?')] as $chip)
                        <button type="button" class="min-h-[44px] rounded-full border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-800 dark:text-gray-200 transition hover:border-maroon-700 hover:text-maroon-700 dark:hover:text-white disabled:opacity-50">{{ $chip }}</button>
                    @endforeach
                </div>

                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="chat-input" maxlength="1000" enterkeyhint="send" aria-label="{{ __('Type your message...') }}" placeholder="{{ __('Type your message...') }}" class="cg-input flex-1 min-h-[44px]" autocomplete="off">
                    <button type="submit" id="chat-send" class="cg-btn min-h-[44px] disabled:opacity-60">{{ __('Send') }}</button>
                </form>

                <p class="mt-3 text-sm cg-muted">
                    <strong>{{ __('In immediate danger? Call 911 first.') }}</strong>
                    {{ __('Reports are reviewed by staff and are not answered instantly.') }}
                </p>

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
        function showSuccessCard() {
            const row = document.createElement('div');
            row.className = 'text-left';

            const card = document.createElement('div');
            card.className = 'border-2 border-green-600 rounded-lg p-4 bg-green-50 dark:bg-gray-900 flex items-start gap-3';

            const icon = document.createElement('div');
            icon.innerHTML = '<svg class="h-6 w-6 text-green-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';

            const textWrap = document.createElement('div');
            const title = document.createElement('p');
            title.className = 'text-sm font-semibold text-green-800 dark:text-green-300';
            title.textContent = 'Report Submitted';
            const desc = document.createElement('p');
            desc.className = 'text-sm text-gray-700 dark:text-gray-200 mt-1';
            desc.textContent = 'You can check its status under "My Reports."';

            textWrap.appendChild(title);
            textWrap.appendChild(desc);
            card.appendChild(icon);
            card.appendChild(textWrap);
            row.appendChild(card);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }
        async function submitReport(data, btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-flex items-center gap-2">Submitting<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></span>';

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
                    btn.innerHTML = '<span class="inline-flex items-center gap-2">Submitted<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></span>';
                    showSuccessCard();
                } else {
                    addMessage('Something went wrong submitting the report. Please try the incident report form directly.', 'bot');
                }
            } catch (err) {
                addMessage('Something went wrong submitting the report. Please try the incident report form directly.', 'bot');
            }
        }

        const t = {{ Js::from([
            'error' => __('Sorry, something went wrong. Please try again.'),
            'slow' => __('That took too long. Please try again.'),
            'rate' => __('You are sending messages too quickly. Please wait a moment.'),
            'retry' => __('Try again'),
        ]) }};
        const chips = document.getElementById('chat-chips');
        let lastMessage = '';

        function setBusy(busy) {
            input.disabled = busy;
            sendBtn.disabled = busy;
            chips.querySelectorAll('button').forEach(b => b.disabled = busy);
        }

        function showError(text) {
            const row = document.createElement('div');
            row.className = 'text-sm text-left';
            const bubble = document.createElement('span');
            bubble.className = 'px-3 py-2 rounded-lg inline-flex flex-wrap items-center gap-3 max-w-[85%] bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200';
            const msg = document.createElement('span');
            msg.textContent = text;
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'font-semibold underline min-h-[44px]';
            retry.textContent = t.retry;
            retry.onclick = () => { row.remove(); sendMessage(lastMessage, true); };
            bubble.append(msg, retry);
            row.appendChild(bubble);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        async function sendMessage(message, isRetry = false) {
            message = message.trim();
            if (!message || input.disabled) return;
            lastMessage = message;
            chips.classList.add('hidden');

            if (!isRetry) {
                addMessage(message, 'user');
                conversationHistory += 'Resident: ' + message + '\n';
            }
            input.value = '';
            setBusy(true);
            showTyping();

            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 30000);

            try {
                const response = await fetch("{{ route('chatbot.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ message, session_id: sessionId, history: conversationHistory }),
                    signal: controller.signal
                });

                if (response.status === 429) throw new Error('rate');
                if (!response.ok) throw new Error('http');

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
                showError(err.message === 'rate' ? t.rate : (err.name === 'AbortError' ? t.slow : t.error));
            } finally {
                clearTimeout(timer);
                setBusy(false);
                input.focus();
            }
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            sendMessage(input.value);
        });
        chips.querySelectorAll('button').forEach(b => b.addEventListener('click', () => sendMessage(b.textContent)));
    </script>
</x-app-layout>
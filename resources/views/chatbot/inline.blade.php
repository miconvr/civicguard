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

    <div class="mb-6">
        <div class="cg-card p-3 sm:p-6">

                <div class="mb-3 flex items-center gap-3 border-b border-gray-100 pb-3 dark:border-gray-700 sm:mb-4 sm:pb-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-maroon-50 shadow-sm ring-1 ring-maroon-100 dark:bg-maroon-900/30 dark:ring-maroon-800" aria-hidden="true">
                        <svg class="h-10 w-10" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 4 40 10v12c0 10-6.6 17.8-16 22-9.4-4.2-16-12-16-22V10L24 4Z" fill="#7F1D1D"/>
                            <path d="M15 20c0-5 4-8 9-8s9 3 9 8v8c0 5-4 8-9 8s-9-3-9-8v-8Z" fill="#FFF7ED"/>
                            <circle cx="21" cy="23" r="1.7" fill="#292524"/><circle cx="27" cy="23" r="1.7" fill="#292524"/>
                            <path d="M20 29c1.1 1.2 2.4 1.8 4 1.8s2.9-.6 4-1.8" stroke="#7F1D1D" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M34 12v7m-3.5-3.5h7" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-base font-bold leading-tight text-maroon-900 dark:text-gray-100">{{ __('Report helper') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('CivicGuard AI Chatbot') }}</p>
                    </div>
                </div>

                <div id="chat-log" role="log" aria-live="polite" aria-label="{{ __('Conversation') }}" class="space-y-3 overflow-y-auto p-4 sm:p-6" style="height:min(52dvh,32rem)">
                    <div class="text-sm text-left">
                        <span class="px-4 py-3 rounded-2xl rounded-tl-sm inline-block max-w-[90%] text-left bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            {{ __('Hi! Tell me what happened and I can help you put together a clear incident report.') }}
                        </span>
                    </div>
                </div>

                <div id="chat-chips" class="flex flex-nowrap gap-2 overflow-x-auto border-t border-gray-100 px-4 pb-3 pt-4 dark:border-gray-700 sm:flex-wrap sm:overflow-visible sm:px-6">
                    @foreach ([__('I want to report an incident'), __('How do I track my report?'), __('What counts as an emergency?'), __('What do the urgency levels mean?')] as $chip)
                        <button type="button" class="shrink-0 whitespace-nowrap min-h-[40px] sm:min-h-[44px] rounded-full border border-gray-300 dark:border-gray-600 px-3.5 sm:px-4 py-1.5 sm:py-2 text-[13px] sm:text-sm font-medium text-gray-800 dark:text-gray-200 transition hover:border-maroon-700 hover:text-maroon-700 dark:hover:text-white disabled:opacity-50">{{ $chip }}</button>
                    @endforeach
                </div>

                <form id="chat-form" class="flex gap-2 border-t border-gray-100 p-4 dark:border-gray-700 sm:px-6">
                    <input type="text" id="chat-input" maxlength="1000" enterkeyhint="send" aria-label="{{ __('Describe your concern or ask a question') }}" placeholder="{{ __('Describe your concern or ask a question…') }}" class="cg-input min-w-0 flex-1 min-h-[44px]" autocomplete="off">
                    <button type="submit" id="chat-send" class="cg-btn min-h-[44px] disabled:cursor-wait disabled:opacity-60" aria-busy="false">
                        <span id="chat-send-label">{{ __('Send') }}</span>
                        <span id="chat-send-spinner" class="hidden"><span class="inline-flex items-center gap-2" role="status"><svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>{{ __('Sending...') }}</span></span>
                    </button>
                </form>

                <p class="mt-2 sm:mt-3 text-xs sm:text-sm cg-muted">
                    <strong>{{ __('In immediate danger? Call 911 first.') }}</strong>
                    {{ __('Reports are reviewed by staff and are not answered instantly.') }}
                </p>

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
            bubble.className = 'px-4 py-3 rounded-2xl inline-block max-w-[90%] text-left whitespace-pre-wrap shadow-sm ' +
                (who === 'user'
                    ? 'bg-maroon-700 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100');
            bubble.textContent = text;
            row.appendChild(bubble);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        function addModelNote(text) {
            const note = document.createElement('p');
            note.className = 'text-xs text-gray-500 dark:text-gray-400 pl-1 flex items-center gap-1.5';

            let icon = '';
            if (/gemini/i.test(text)) {
                icon = '<svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><defs><linearGradient id="chat-gemini-logo" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#4E87F8"/><stop offset="100%" stop-color="#9B51E0"/></linearGradient></defs><path d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12Z" fill="url(#chat-gemini-logo)"/></svg>';
            } else if (/groq/i.test(text)) {
                icon = '<svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="24" height="24" rx="5" fill="#F55036"/><path d="M15.5 12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm0 0v4a2 2 0 0 1-2 2h-1" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/></svg>';
            }

            const span = document.createElement('span');
            span.textContent = text;
            if (icon) {
                note.innerHTML = icon;
                note.appendChild(span);
            } else {
                note.appendChild(span);
            }
            log.appendChild(note);
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
            const originalLabel = btn.textContent;
            let submitted = false;
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
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (response.ok || response.redirected) {
                    btn.innerHTML = '<span class="inline-flex items-center gap-2">Submitted<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></span>';
                    submitted = true;
                    showSuccessCard();
                } else {
                    addMessage('Something went wrong submitting the report. Please try the incident report form directly.', 'bot');
                }
            } catch (err) {
                addMessage(navigator.onLine ? 'Something went wrong submitting the report. Please try the incident report form directly.' : t.offline, 'bot');
            } finally {
                if (!submitted) {
                    btn.disabled = false;
                    btn.textContent = originalLabel;
                }
            }
        }

        const t = {{ Js::from([
            'error' => __('Sorry, something went wrong. Please try again.'),
            'offline' => __("You're offline. Please check your connection and try again."),
            'expired' => __('Your session expired. Please refresh the page and try again.'),
            'slow' => __('That took too long. Please try again.'),
            'rate' => __('You are sending messages too quickly. Please wait a moment.'),
            'retry' => __('Try again'),
            'call911' => __('Call 911'),
            'safeContinue' => __("I'm safe, continue my report"),
            'safeMessage' => __("I'm safe, and I would like to continue with a report."),
        ]) }};
        const chips = document.getElementById('chat-chips');
        let lastMessage = '';
        let emergencyAcked = false;

        function showEmergencyActions() {
            const row = document.createElement('div');
            row.setAttribute('data-emergency-actions', '');
            row.className = 'flex flex-wrap gap-2';

            const call = document.createElement('a');
            call.href = 'tel:911';
            call.className = 'cg-btn inline-flex min-h-[44px] items-center justify-center';
            call.textContent = t.call911;

            const proceed = document.createElement('button');
            proceed.type = 'button';
            proceed.className = 'min-h-[44px] rounded-full border border-gray-300 px-4 py-2 text-sm font-medium text-gray-800 transition hover:border-maroon-700 dark:border-gray-600 dark:text-gray-200';
            proceed.textContent = t.safeContinue;
            proceed.onclick = () => {
                emergencyAcked = true;
                sendMessage(t.safeMessage);
            };

            row.append(call, proceed);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        function setBusy(busy) {
            input.disabled = busy;
            sendBtn.disabled = busy;
            sendBtn.setAttribute('aria-busy', busy ? 'true' : 'false');
            document.getElementById('chat-send-label').classList.toggle('hidden', busy);
            document.getElementById('chat-send-spinner').classList.toggle('hidden', !busy);
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
            document.querySelectorAll('[data-emergency-actions]').forEach(el => el.remove());

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
                    body: JSON.stringify({ message, session_id: sessionId, history: conversationHistory, emergency_ack: emergencyAcked }),
                    signal: controller.signal
                });

                if (response.status === 429) throw new Error('rate');
                if (response.status === 419) throw new Error('expired');
                if (!response.ok) throw new Error('http');

                const data = await response.json();
                sessionId = data.session_id;
                conversationHistory += 'Chatbot: ' + data.reply + '\n';
                hideTyping();
                addMessage(data.reply, 'bot');
                if (data.model_note) addModelNote(data.model_note);

                if (data.emergency) {
                    showEmergencyActions();
                }

                if (data.report_data) {
                    showReviewCard(data.report_data);
                }
            } catch (err) {
                hideTyping();
                showError(!navigator.onLine ? t.offline : err.message === 'rate' ? t.rate : err.message === 'expired' ? t.expired : (err.name === 'AbortError' ? t.slow : t.error));
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

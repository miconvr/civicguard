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

    <div @keydown.escape.window="if (chatOpen) { chatOpen = false; $nextTick(() => $refs.chatToggle.focus()); }" class="my-6 overflow-hidden rounded-xl border border-stone-200 bg-[#fbf8f3] dark:border-[#3d3d3a] dark:bg-[#211f1e]">
        <div x-show="!chatOpen" x-cloak class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:px-8">
            <div class="flex min-w-0 flex-1 items-center gap-3">
                <span class="h-14 w-14 shrink-0 overflow-hidden rounded-full border border-[#ddc9b7] bg-[#f7eee5]" aria-hidden="true">
                    @include('chatbot.mascot')
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Need a hand with your report?') }}</span>
                    <span class="mt-0.5 block text-sm leading-5 text-gray-600 dark:text-gray-400">{{ __('CivicGuard AI can help you organize the details.') }}</span>
                </span>
            </div>
            <button type="button" x-ref="chatToggle" @click="chatOpen = !chatOpen; if (chatOpen) $nextTick(() => document.getElementById('chat-input')?.focus())" :aria-expanded="chatOpen.toString()" aria-controls="report-ai-chat" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-maroon-700 px-4 py-2 text-sm font-semibold text-maroon-800 transition-colors hover:bg-maroon-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:border-[#b98286] dark:text-gray-100 dark:hover:bg-white/5 sm:w-auto">
                <span x-text="chatOpen ? @js(__('Close chat')) : @js(__('Open chat'))">{{ __('Open chat') }}</span>
                <svg class="h-4 w-4 transition-transform" :class="chatOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
            </button>
        </div>

        <div x-show="chatOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" id="report-ai-chat" role="region" aria-label="{{ __('CivicGuard AI') }}" class="border-t border-stone-200 bg-white dark:border-[#3d3d3a] dark:bg-[#262624]">
                <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('CivicGuard AI') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Your report-writing assistant') }}</p>
                    </div>
                    <button type="button" @click="chatOpen = false; $nextTick(() => $refs.chatToggle.focus())" aria-label="{{ __('Close chat') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-600 transition hover:bg-black/5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-maroon-700 dark:text-gray-300 dark:hover:bg-white/10"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg></button>
                </div>

                <div id="chat-log" role="log" aria-live="polite" aria-label="{{ __('Conversation') }}" class="min-h-36 space-y-3 overflow-y-auto border-y border-gray-100 px-4 py-4 dark:border-gray-700 sm:px-6" style="height:min(38dvh,22rem)">
                    <div class="text-sm text-left">
                        <span class="px-4 py-3 rounded-2xl rounded-tl-sm inline-block max-w-[90%] text-left bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            {{ __('Hi! Tell me what happened and I can help you put together a clear incident report.') }}
                        </span>
                    </div>
                </div>

                <div id="chat-chips" class="flex flex-nowrap gap-2 overflow-x-auto px-4 pb-3 pt-3 sm:flex-wrap sm:overflow-visible sm:px-6">
                    @foreach ([__('I want to report an incident'), __('How do I track my report?'), __('What counts as an emergency?'), __('What do the urgency levels mean?')] as $chip)
                        <button type="button" class="shrink-0 whitespace-nowrap min-h-[40px] sm:min-h-[44px] rounded-full border border-gray-300 dark:border-gray-600 px-3.5 sm:px-4 py-1.5 sm:py-2 text-[13px] sm:text-sm font-medium text-gray-800 dark:text-gray-200 transition hover:border-maroon-700 hover:text-maroon-700 dark:hover:text-white disabled:opacity-50">{{ $chip }}</button>
                    @endforeach
                </div>

                <form id="chat-form" class="flex gap-2 border-t border-gray-100 p-3 dark:border-gray-700 sm:px-6">
                    <input type="text" id="chat-input" maxlength="1000" enterkeyhint="send" aria-label="{{ __('Describe your concern or ask a question') }}" placeholder="{{ __('Describe your concern or ask a question…') }}" class="cg-input min-w-0 flex-1 min-h-[44px]" autocomplete="off">
                    <button type="submit" id="chat-send" class="cg-btn min-h-[44px] disabled:cursor-wait disabled:opacity-60" aria-busy="false">
                        <span id="chat-send-label">{{ __('Send') }}</span>
                        <span id="chat-send-spinner" class="hidden"><span class="inline-flex items-center gap-2" role="status"><svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>{{ __('Sending...') }}</span></span>
                    </button>
                </form>

                <p class="px-3 pb-3 text-xs cg-muted sm:px-6">
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

            const useBtn = document.createElement('button');
            useBtn.type = 'button';
            useBtn.className = 'cg-btn text-sm';
            useBtn.textContent = 'Use these details in the form';
            useBtn.onclick = () => applyReportDraft(data);

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'text-sm text-gray-500 dark:text-gray-400 hover:underline';
            cancelBtn.textContent = 'Keep chatting instead';
            cancelBtn.onclick = () => card.closest('div.text-left').remove();

            btnRow.appendChild(useBtn);
            btnRow.appendChild(cancelBtn);
            card.appendChild(btnRow);

            row.appendChild(card);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }
        function applyReportDraft(data) {
            const description = document.getElementById('description');
            const category = document.getElementById('category_id');
            const location = document.getElementById('location_text');

            if (description && data.description) {
                description.value = data.description;
                description.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (category && data.category_id) {
                category.value = String(data.category_id);
                category.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (location && data.location) {
                location.value = data.location;
                location.dispatchEvent(new Event('input', { bubbles: true }));
            }

            window.dispatchEvent(new CustomEvent('report-draft-applied'));
            description?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => description?.focus(), 250);
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

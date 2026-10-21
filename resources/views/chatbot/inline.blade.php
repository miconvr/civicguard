    <style>
        .typing-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #6b7280;
            display: inline-block;
            opacity: 0.35;
            animation: typing-bounce var(--cg-motion-typing-cycle) infinite ease-in-out both;
        }
        .dark .typing-dot { background: #d1d5db; }
        .typing-dot:nth-child(2) { animation-delay: 140ms; }
        .typing-dot:nth-child(3) { animation-delay: 280ms; }
        .send-loading-dot { width: 4px; height: 4px; border-radius: 9999px; background: currentColor; animation: typing-bounce var(--cg-motion-typing-cycle) infinite ease-in-out both; }
        .send-loading-dot:nth-child(2) { animation-delay: 140ms; }
        .send-loading-dot:nth-child(3) { animation-delay: 280ms; }
        .chat-message-enter-user { transform-origin: right bottom; animation: cg-chat-message-user var(--cg-motion-enter) var(--cg-ease-out) both; }
        .chat-message-enter-bot { transform-origin: left bottom; animation: cg-chat-message-bot var(--cg-motion-enter) var(--cg-ease-out) both; }
        .chat-typing-bubble { position: relative; transform-origin: left bottom; animation: cg-chat-bubble-float 2.4s ease-in-out infinite; }
        .chat-review-enter { transform-origin: left bottom; animation: cg-chat-review-in var(--cg-motion-page) var(--cg-ease-out) both; }
        .chat-photo-preview-enter { transform-origin: top left; animation: cg-chat-photo-in var(--cg-motion-interaction) var(--cg-ease-out) both; }
        .chat-photo-preview-leave { pointer-events: none; animation: cg-chat-photo-out var(--cg-motion-exit) var(--cg-ease-in) both; }
        .chat-photo-button-updated { animation: cg-chat-photo-button var(--cg-motion-interaction) var(--cg-ease-out) both; }
        .chat-edit-panel-enter { transform-origin: top center; animation: cg-chat-edit-in var(--cg-motion-enter) var(--cg-ease-out) both; }
        .chat-edit-panel-leave { pointer-events: none; animation: cg-chat-edit-out var(--cg-motion-exit) var(--cg-ease-in) both; }
        .chat-edit-panel-enter .chat-edit-field { animation: cg-chat-edit-field-in var(--cg-motion-interaction) var(--cg-ease-out) both; }
        .chat-edit-panel-enter .chat-edit-field:nth-child(2) { animation-delay: 35ms; }
        .chat-edit-panel-enter .chat-edit-field:nth-child(3) { animation-delay: 70ms; }
        .chat-review-details-enter { transform-origin: top center; animation: cg-chat-details-in var(--cg-motion-interaction) var(--cg-ease-out) both; }
        .chat-detail-updated { transform-origin: left center; animation: cg-chat-detail-updated var(--cg-motion-interaction) var(--cg-ease-out) both; }
        .chat-success-enter { transform-origin: left bottom; animation: cg-chat-success-in var(--cg-motion-page) var(--cg-ease-out) both; }
        .chat-success-icon { animation: cg-chat-success-pop var(--cg-motion-enter) var(--cg-ease-out) both; }
        .chat-success-check { stroke-dasharray: 24; stroke-dashoffset: 24; animation: cg-chat-check-draw var(--cg-motion-interaction) var(--cg-ease-out) 40ms forwards; }
        .chat-submit-loading { animation: cg-chat-submit-pulse 1.1s ease-in-out infinite; }
        .chat-submit-complete { background-color: #dcfce7 !important; color: #166534 !important; transition: background-color var(--cg-motion-enter) var(--cg-ease-out), color var(--cg-motion-enter) var(--cg-ease-out); }
        @keyframes cg-chat-message-user {
            from { opacity: 0; transform: translate(10px, 8px) scale(.96); }
            to { opacity: 1; transform: translate(0) scale(1); }
        }
        @keyframes cg-chat-message-bot {
            from { opacity: 0; transform: translate(-6px, 8px) scale(.98); }
            to { opacity: 1; transform: translate(0) scale(1); }
        }
        @keyframes cg-chat-bubble-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        @keyframes cg-chat-review-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cg-chat-photo-in {
            from { opacity: 0; transform: translateY(-6px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes cg-chat-photo-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-4px) scale(.985); }
        }
        @keyframes cg-chat-photo-button {
            0% { transform: scale(1); }
            45% { transform: scale(.96); }
            100% { transform: scale(1); }
        }
        @keyframes cg-chat-edit-in {
            from { opacity: 0; transform: translateY(-7px) scale(.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes cg-chat-edit-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-4px) scale(.99); }
        }
        @keyframes cg-chat-edit-field-in {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cg-chat-details-in {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cg-chat-detail-updated {
            from { opacity: .6; transform: translateY(3px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cg-chat-success-in {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes cg-chat-success-pop {
            from { opacity: 0; transform: scale(.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes cg-chat-check-draw { to { stroke-dashoffset: 0; } }
        @keyframes cg-chat-submit-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgb(149 31 61 / 0%); }
            50% { transform: scale(.985); box-shadow: 0 0 0 4px rgb(149 31 61 / 18%); }
        }
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
            40% { transform: translateY(-5px); opacity: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            .chat-message-enter-user, .chat-message-enter-bot, .chat-typing-bubble, .chat-review-enter, .chat-photo-preview-enter, .chat-photo-preview-leave, .chat-photo-button-updated, .chat-edit-panel-enter, .chat-edit-panel-leave, .chat-edit-panel-enter .chat-edit-field, .chat-review-details-enter, .chat-detail-updated, .chat-success-enter, .chat-success-icon, .chat-success-check, .chat-submit-loading, .send-loading-dot { animation: none; }
        }
    </style>

    <div id="chat-widget" @keydown.escape.window="if (chatOpen) { chatOpen = false; $nextTick(() => $refs.chatToggle.focus()); }" class="my-6 overflow-hidden rounded-xl border border-stone-200 cg-surface-raised dark:border-gray-700">
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
            <button type="button" x-ref="chatToggle" @click="chatOpen = !chatOpen" :aria-expanded="chatOpen.toString()" aria-controls="report-ai-chat" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-maroon-700 px-4 py-2 text-sm font-semibold text-maroon-800 transition-colors hover:bg-maroon-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 dark:border-[#b98286] dark:text-gray-100 dark:hover:bg-white/5 sm:w-auto">
                <span x-text="chatOpen ? @js(__('Close chat')) : @js(__('Open chat'))">{{ __('Open chat') }}</span>
                <svg class="h-4 w-4 transition-transform" :class="chatOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
            </button>
        </div>

        <div x-show="chatOpen" x-cloak id="report-ai-chat" role="region" aria-label="{{ __('CivicGuard AI') }}" class="border-t border-stone-200 cg-surface dark:border-gray-700">
                <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('CivicGuard AI') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Your report-writing assistant') }}</p>
                    </div>
                    <button id="chat-keyboard-dismiss" type="button" @click="chatOpen = false" aria-label="{{ __('Close chat') }}" class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-lg text-gray-600 hover:bg-black/5 dark:text-gray-300 dark:hover:bg-white/10">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                    </button>
                </div>

                <div id="chat-log" role="log" aria-live="polite" aria-label="{{ __('Conversation') }}" class="cg-chat-log min-h-36 space-y-3 border-y border-gray-100 px-3 py-4 dark:border-gray-700 sm:px-6">
                    <div class="text-sm text-left">
                        <span class="px-4 py-3 rounded-2xl rounded-tl-sm inline-block max-w-[90%] text-left bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            {{ __('Hi! Tell me what happened and I can help you put together a clear incident report.') }}
                        </span>
                    </div>
                </div>

                <form id="chat-form" class="flex items-end gap-2 border-t border-gray-100 p-3 dark:border-gray-700 sm:px-6">
                    <textarea id="chat-input" rows="1" maxlength="1000" enterkeyhint="send" aria-label="{{ __('Describe your concern or ask a question') }}" placeholder="{{ __('Type your message...') }}" class="cg-input min-h-[44px] max-h-32 min-w-0 flex-1 resize-none overflow-y-hidden py-2.5 leading-5" style="height:44px" autocomplete="off"></textarea>
                    <button type="submit" id="chat-send" class="inline-flex h-11 w-11 min-h-[44px] shrink-0 items-center justify-center rounded-lg bg-maroon-700 p-0 text-white shadow-sm transition duration-[var(--cg-motion-interaction)] hover:bg-maroon-800 active:scale-[.99] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-maroon-700 disabled:cursor-wait disabled:opacity-65" aria-label="{{ __('Send') }}" aria-busy="false">
                        <span id="chat-send-label" class="sr-only">{{ __('Send') }}</span>
                        <span id="chat-send-icon" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5m0 0-6 6m6-6 6 6"/></svg></span>
                        <span id="chat-send-spinner" class="hidden items-center justify-center gap-1" role="status"><span class="inline-flex items-center gap-1" aria-hidden="true"><span class="send-loading-dot"></span><span class="send-loading-dot"></span><span class="send-loading-dot"></span></span><span class="sr-only">{{ __('Sending...') }}</span></span>
                    </button>
                </form>

                <p class="px-3 pb-3 text-[13px] cg-muted sm:px-6">{{ __('Reports are reviewed by staff and are not answered instantly.') }}</p>
        </div>
    </div>

    <script>
        let sessionId = null;
        let conversationHistory = '';
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send');
        const sendButtonLabels = {{ Js::from(['send' => __('Send'), 'sending' => __('Sending...')]) }};
        const log = document.getElementById('chat-log');
        const chatWidget = document.getElementById('chat-widget');

        function syncMobileChatViewport() {
            if (!chatWidget.classList.contains('cg-chat-keyboard-mode')) return;
            const viewport = window.visualViewport;
            chatWidget.style.setProperty('--cg-chat-viewport-height', `${viewport?.height ?? window.innerHeight}px`);
            chatWidget.style.setProperty('--cg-chat-viewport-top', `${viewport?.offsetTop ?? 0}px`);
        }

        function enterMobileChatMode() {
            if (!window.matchMedia('(max-width: 639px)').matches) return;
            chatWidget.classList.add('cg-chat-keyboard-mode');
            document.documentElement.classList.add('cg-chat-keyboard-open');
            syncMobileChatViewport();
        }

        function leaveMobileChatMode() {
            chatWidget.classList.remove('cg-chat-keyboard-mode');
            document.documentElement.classList.remove('cg-chat-keyboard-open');
            chatWidget.style.removeProperty('--cg-chat-viewport-height');
            chatWidget.style.removeProperty('--cg-chat-viewport-top');
        }

        input.addEventListener('focus', enterMobileChatMode);
        input.addEventListener('blur', () => window.setTimeout(() => {
            if (document.activeElement !== input) leaveMobileChatMode();
        }, 0));
        sendBtn.addEventListener('pointerdown', (event) => event.preventDefault());
        window.visualViewport?.addEventListener('resize', syncMobileChatViewport);
        window.visualViewport?.addEventListener('scroll', syncMobileChatViewport);
        window.addEventListener('resize', () => {
            if (!window.matchMedia('(max-width: 639px)').matches) leaveMobileChatMode();
            else syncMobileChatViewport();
        });
        const reviewText = {{ Js::from([
            'title' => __('Review Before Submitting'),
            'category' => __('Category'),
            'editDetails' => __('Edit details'),
            'saveChanges' => __('Save changes'),
            'cancel' => __('Cancel'),
            'editRequired' => __('Enter a description and location to continue.'),
            'photoQuestion' => __('Add photo (optional)'),
            'addPhoto' => __('Choose photo'),
            'changePhoto' => __('Change photo'),
            'removePhoto' => __('Remove photo'),
            'photoSelected' => __('Photo selected'),
            'photoTypeError' => __('Choose a JPG, PNG, or WebP image.'),
            'photoSizeError' => __('The photo must be 5 MB or smaller.'),
            'categories' => $categories->map(fn ($category) => ['id' => $category->id, 'name' => $category->name])->values(),
            'description' => __('What happened?'),
            'location' => __('Location'),
            'submitReport' => __('Submit Report'),
            'submitting' => __('Submitting...'),
            'submitted' => __('Report submitted'),
            'success' => __('Your report is now with barangay staff. You can track its status on My Reports.'),
            'viewReports' => __('View My Reports'),
            'reportsUrl' => route('reports.index'),
            'submitError' => __('We could not submit your report. Please try again.'),
            'keepChatting' => __('Keep chatting instead'),
        ]) }};

        function resizeChatInput() {
            const maxHeight = 128;
            input.style.height = '44px';
            const height = Math.min(input.scrollHeight, maxHeight);
            input.style.height = height + 'px';
            input.style.overflowY = input.scrollHeight > maxHeight ? 'auto' : 'hidden';
        }

        function addMessage(text, who) {
            const row = document.createElement('div');
            row.className = 'text-sm chat-message-enter-' + (who === 'user' ? 'user text-right' : 'bot text-left');
            const bubble = document.createElement('span');
            bubble.className = 'px-4 py-3 rounded-2xl inline-block max-w-[90%] text-left whitespace-pre-wrap shadow-sm ' +
                (who === 'user'
                    ? 'bg-maroon-700 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100');
            if (who === 'bot') {
                const normalized = text.replace(/\\\*\\\*/g, '**');
                const parts = normalized.split(/(\*\*[^*\n]+?\*\*)/g);
                parts.forEach((part) => {
                    if (part.startsWith('**') && part.endsWith('**') && part.length > 4) {
                        const strong = document.createElement('strong');
                        strong.textContent = part.slice(2, -2);
                        bubble.appendChild(strong);
                    } else {
                        bubble.appendChild(document.createTextNode(part));
                    }
                });
            } else {
                bubble.textContent = text;
            }
            row.appendChild(bubble);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        function addModelNote(text) {
            const note = document.createElement('p');
            note.className = 'text-[13px] text-gray-500 dark:text-gray-400 pl-1 flex items-center gap-1.5';

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
                '<span class="chat-typing-bubble px-3 py-3 rounded-2xl rounded-bl-sm inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 text-gray-500 shadow-sm dark:text-gray-300">' +
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
            row.className = 'text-left chat-review-enter';

            const card = document.createElement('div');
            card.className = 'space-y-4 rounded-2xl border border-[#8d4355] bg-maroon-800 p-4 text-white shadow-md dark:border-[#9b5966] dark:bg-[#6f1b34] dark:text-white sm:p-5';

            const heading = document.createElement('div');
            heading.className = 'flex flex-wrap items-center justify-between gap-3';
            const title = document.createElement('p');
            title.className = 'text-base font-semibold tracking-tight text-white';
            title.textContent = reviewText.title;
            heading.appendChild(title);

            const detailsView = document.createElement('div');
            detailsView.className = 'grid gap-4 sm:grid-cols-[minmax(0,2fr)_minmax(10rem,1fr)]';
            const categoryLine = document.createElement('div');
            categoryLine.className = 'inline-flex items-center rounded-full bg-[#fffdf9] px-3 py-1';
            const categoryHeader = document.createElement('div');
            categoryHeader.className = 'flex items-center justify-between gap-3';
            const categoryText = document.createElement('div');
            const categoryValue = document.createElement('span');
            categoryValue.className = 'text-sm font-semibold text-maroon-900';
            categoryValue.textContent = data.category_name;
            const categoryLabel = document.createElement('span');
            categoryLabel.className = 'mr-2 text-xs font-medium text-maroon-700';
            categoryLabel.textContent = reviewText.category;
            categoryText.append(categoryLabel, categoryValue);
            categoryHeader.appendChild(categoryText);
            categoryLine.appendChild(categoryHeader);
            heading.appendChild(categoryLine);
            card.appendChild(heading);

            const detailValues = {};
            const fields = [
                [reviewText.description, data.description],
                [reviewText.location, data.location],
            ];
            fields.forEach(([label, value]) => {
                const field = document.createElement('div');
                field.className = 'min-w-0 space-y-1';
                const fieldLabel = document.createElement('p');
                fieldLabel.className = 'text-xs font-semibold uppercase tracking-wide text-white/70';
                fieldLabel.textContent = label;
                const fieldValue = document.createElement('p');
                fieldValue.className = 'break-words text-sm leading-6 text-white';
                fieldValue.textContent = value;
                detailValues[label] = fieldValue;
                field.append(fieldLabel, fieldValue);
                detailsView.appendChild(field);
            });

            const photoSection = document.createElement('div');
            photoSection.className = 'space-y-3 border-t border-white/20 pt-3';
            const photoControls = document.createElement('div');
            photoControls.className = 'flex flex-col items-start gap-2';
            const photoQuestion = document.createElement('p');
            photoQuestion.className = 'text-sm text-white/85';
            photoQuestion.textContent = reviewText.photoQuestion;
            const photoInput = document.createElement('input');
            photoInput.type = 'file';
            photoInput.accept = 'image/jpeg,image/png,image/webp';
            photoInput.className = 'sr-only';
            photoInput.tabIndex = -1;
            const photoActions = document.createElement('div');
            photoActions.className = 'flex items-center gap-3';
            const pickPhotoBtn = document.createElement('button');
            pickPhotoBtn.type = 'button';
            pickPhotoBtn.className = 'inline-flex min-h-10 items-center gap-2 rounded-lg border border-white/70 bg-[#fffdf9] px-3 text-sm font-semibold text-maroon-900 transition-colors hover:bg-[#f4e9e8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white';
            const pickPhotoIcon = document.createElement('span');
            pickPhotoIcon.className = 'inline-flex h-4 w-4 items-center justify-center';
            pickPhotoIcon.setAttribute('aria-hidden', 'true');
            pickPhotoIcon.innerHTML = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5"/></svg>';
            const pickPhotoLabel = document.createElement('span');
            pickPhotoLabel.textContent = reviewText.addPhoto;
            pickPhotoBtn.append(pickPhotoIcon, pickPhotoLabel);
            pickPhotoBtn.setAttribute('aria-label', reviewText.addPhoto);
            const setPickPhotoLabel = (label) => {
                pickPhotoLabel.textContent = label;
                pickPhotoBtn.setAttribute('aria-label', label);
            };
            pickPhotoBtn.onclick = () => photoInput.click();

            const photoError = document.createElement('p');
            photoError.className = 'text-[13px] font-medium text-rose-100';
            photoError.setAttribute('role', 'alert');
            photoError.hidden = true;

            const photoPreview = document.createElement('div');
            photoPreview.className = 'hidden w-full flex items-center gap-3 rounded-xl border border-white/15 bg-black/10 p-2 sm:w-fit sm:min-w-[19rem]';
            const photoStage = document.createElement('div');
            photoStage.className = 'h-20 w-24 shrink-0 overflow-hidden rounded-lg border border-white/25 bg-[#4e1427] sm:h-24 sm:w-32';
            const previewImage = document.createElement('img');
            previewImage.className = 'h-full w-full object-contain';
            previewImage.alt = '';
            let photoPreviewUrl = null;
            photoStage.appendChild(previewImage);
            const photoMeta = document.createElement('div');
            photoMeta.className = 'min-w-0 flex-1 space-y-1';
            const photoFileLabel = document.createElement('p');
            photoFileLabel.className = 'min-w-0 truncate text-[13px] text-white/80';
            photoFileLabel.title = '';
            const removePhotoBtn = document.createElement('button');
            removePhotoBtn.type = 'button';
            removePhotoBtn.className = 'min-h-9 text-[13px] font-medium text-white/80 underline underline-offset-2 hover:text-white';
            removePhotoBtn.textContent = reviewText.removePhoto;
            removePhotoBtn.onclick = () => {
                if (removePhotoBtn.disabled) return;
                removePhotoBtn.disabled = true;
                pickPhotoBtn.disabled = true;
                photoPreview.classList.remove('chat-photo-preview-enter');
                photoPreview.classList.add('chat-photo-preview-leave');
                photoInput.value = '';
                data.photo = null;
                photoError.hidden = true;
                window.setTimeout(() => {
                    if (photoPreviewUrl) URL.revokeObjectURL(photoPreviewUrl);
                    photoPreviewUrl = null;
                    previewImage.removeAttribute('src');
                    photoPreview.classList.add('hidden');
                    photoPreview.classList.remove('chat-photo-preview-leave');
                    setPickPhotoLabel(reviewText.addPhoto);
                    pickPhotoBtn.disabled = false;
                    removePhotoBtn.disabled = false;
                    pickPhotoBtn.classList.remove('chat-photo-button-updated');
                    void pickPhotoBtn.offsetWidth;
                    pickPhotoBtn.classList.add('chat-photo-button-updated');
                }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 170);
            };
            photoMeta.append(photoFileLabel, removePhotoBtn);
            photoPreview.append(photoStage, photoMeta);

            photoInput.addEventListener('change', () => {
                const file = photoInput.files?.[0];
                if (!file) return;
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    photoError.textContent = reviewText.photoTypeError;
                    photoError.hidden = false;
                    photoInput.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    photoError.textContent = reviewText.photoSizeError;
                    photoError.hidden = false;
                    photoInput.value = '';
                    return;
                }

                if (photoPreviewUrl) URL.revokeObjectURL(photoPreviewUrl);
                data.photo = file;
                photoPreviewUrl = URL.createObjectURL(file);
                previewImage.src = photoPreviewUrl;
                photoFileLabel.textContent = reviewText.photoSelected + ': ' + file.name;
                photoFileLabel.title = file.name;
                setPickPhotoLabel(reviewText.changePhoto);
                photoPreview.classList.remove('chat-photo-preview-leave', 'chat-photo-preview-enter');
                void photoPreview.offsetWidth;
                photoPreview.classList.remove('hidden');
                photoPreview.classList.add('chat-photo-preview-enter');
                pickPhotoBtn.classList.remove('chat-photo-button-updated');
                void pickPhotoBtn.offsetWidth;
                pickPhotoBtn.classList.add('chat-photo-button-updated');
                photoError.hidden = true;
            });

            photoActions.append(pickPhotoBtn, photoInput);
            photoControls.append(photoQuestion, photoActions);
            photoSection.append(photoControls, photoError, photoPreview);
            detailsView.appendChild(photoSection);

            const editDetailsBtn = document.createElement('button');
            editDetailsBtn.type = 'button';
            editDetailsBtn.className = 'ml-auto min-h-10 rounded-lg border border-white/50 px-3 text-[13px] font-semibold text-white transition-colors hover:border-white hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white';
            editDetailsBtn.textContent = reviewText.editDetails;
            editDetailsBtn.setAttribute('aria-expanded', 'false');
            card.appendChild(detailsView);

            const editPanel = document.createElement('div');
            editPanel.className = 'space-y-3 rounded-xl border border-white/20 bg-[#5c172d] p-4';
            editPanel.hidden = true;

            const editorFields = document.createElement('div');
            editorFields.className = 'space-y-3';

            const categoryEditorLabel = document.createElement('label');
            categoryEditorLabel.className = 'chat-edit-field block text-[13px] font-normal text-white/90';
            categoryEditorLabel.textContent = reviewText.category;
            const categorySelect = document.createElement('select');
            categorySelect.className = 'cg-input mt-1 min-h-10 w-full bg-[#fffdf9] py-1.5 font-sans text-sm font-normal text-gray-900';
            categorySelect.setAttribute('aria-label', reviewText.category);
            reviewText.categories.forEach((category) => {
                const option = document.createElement('option');
                option.value = String(category.id);
                option.textContent = category.name;
                option.selected = Number(data.category_id) === Number(category.id);
                categorySelect.appendChild(option);
            });
            categoryEditorLabel.appendChild(categorySelect);

            const descriptionEditorLabel = document.createElement('label');
            descriptionEditorLabel.className = 'chat-edit-field block text-[13px] font-normal text-white/90';
            descriptionEditorLabel.textContent = reviewText.description;
            const descriptionEditor = document.createElement('textarea');
            descriptionEditor.className = 'cg-input mt-1 min-h-20 w-full bg-[#fffdf9] py-2 font-sans text-sm font-normal text-gray-900';
            descriptionEditor.rows = 3;
            descriptionEditor.maxLength = 2000;
            descriptionEditor.value = data.description;
            descriptionEditorLabel.appendChild(descriptionEditor);

            const locationEditorLabel = document.createElement('label');
            locationEditorLabel.className = 'chat-edit-field block text-[13px] font-normal text-white/90';
            locationEditorLabel.textContent = reviewText.location;
            const locationEditor = document.createElement('input');
            locationEditor.type = 'text';
            locationEditor.className = 'cg-input mt-1 min-h-10 w-full bg-[#fffdf9] font-sans text-sm font-normal text-gray-900';
            locationEditor.maxLength = 255;
            locationEditor.value = data.location;
            locationEditorLabel.appendChild(locationEditor);

            editorFields.append(categoryEditorLabel, descriptionEditorLabel, locationEditorLabel);
            editPanel.appendChild(editorFields);

            const editError = document.createElement('p');
            editError.className = 'text-[13px] font-medium text-rose-100';
            editError.setAttribute('role', 'alert');
            editError.textContent = reviewText.editRequired;
            editError.hidden = true;
            editPanel.appendChild(editError);

            const editorActions = document.createElement('div');
            editorActions.className = 'flex justify-end gap-2';
            const cancelEditBtn = document.createElement('button');
            cancelEditBtn.type = 'button';
            cancelEditBtn.className = 'min-h-10 rounded-lg px-3 text-sm font-medium text-white/80 transition-colors hover:bg-white/10 hover:text-white';
            cancelEditBtn.textContent = reviewText.cancel;
            const saveEditBtn = document.createElement('button');
            saveEditBtn.type = 'button';
            saveEditBtn.className = 'min-h-10 rounded-lg bg-[#fffdf9] px-3 text-sm font-semibold text-maroon-900 transition-colors hover:bg-[#f4e9e8]';
            saveEditBtn.textContent = reviewText.saveChanges;
            editorActions.append(cancelEditBtn, saveEditBtn);
            editPanel.appendChild(editorActions);
            card.appendChild(editPanel);

            const btnRow = document.createElement('div');
            btnRow.className = 'space-y-3 border-t border-white/20 pt-3';

            let editPanelClosing = false;
            const finishEditing = () => {
                if (editPanelClosing) return;
                editPanelClosing = true;
                editPanel.classList.remove('chat-edit-panel-enter');
                editPanel.classList.add('chat-edit-panel-leave');
                cancelEditBtn.disabled = true;
                saveEditBtn.disabled = true;
                const closeDelay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 150;
                window.setTimeout(() => {
                    editPanel.hidden = true;
                    editPanel.classList.remove('chat-edit-panel-leave');
                    detailsView.hidden = false;
                    detailsView.classList.remove('chat-review-details-enter');
                    void detailsView.offsetWidth;
                    detailsView.classList.add('chat-review-details-enter');
                    editDetailsBtn.setAttribute('aria-expanded', 'false');
                    btnRow.hidden = false;
                    cancelEditBtn.disabled = false;
                    saveEditBtn.disabled = false;
                    editPanelClosing = false;
                    editDetailsBtn.focus();
                    window.setTimeout(() => detailsView.classList.remove('chat-review-details-enter'), 220);
                }, closeDelay);
            };

            editDetailsBtn.onclick = () => {
                categorySelect.value = String(data.category_id);
                descriptionEditor.value = data.description;
                locationEditor.value = data.location;
                editError.hidden = true;
                detailsView.hidden = true;
                editPanel.classList.remove('chat-edit-panel-leave', 'chat-edit-panel-enter');
                void editPanel.offsetWidth;
                editPanel.hidden = false;
                editPanel.classList.add('chat-edit-panel-enter');
                editDetailsBtn.setAttribute('aria-expanded', 'true');
                btnRow.hidden = true;
                categorySelect.focus();
            };
            cancelEditBtn.onclick = () => {
                finishEditing();
            };
            saveEditBtn.onclick = () => {
                const selectedCategory = reviewText.categories.find((category) => Number(category.id) === Number(categorySelect.value));
                if (!selectedCategory || !descriptionEditor.value.trim() || !locationEditor.value.trim()) {
                    editError.hidden = false;
                    if (!descriptionEditor.value.trim()) descriptionEditor.focus();
                    else if (!locationEditor.value.trim()) locationEditor.focus();
                    return;
                }
                editError.hidden = true;
                data.category_id = selectedCategory.id;
                data.category_name = selectedCategory.name;
                data.description = descriptionEditor.value.trim();
                data.location = locationEditor.value.trim();
                categoryValue.textContent = data.category_name;
                detailValues[reviewText.description].textContent = data.description;
                detailValues[reviewText.location].textContent = data.location;
                [categoryValue, detailValues[reviewText.description], detailValues[reviewText.location]].forEach((value) => {
                    value.classList.remove('chat-detail-updated');
                    void value.offsetWidth;
                    value.classList.add('chat-detail-updated');
                    window.setTimeout(() => value.classList.remove('chat-detail-updated'), 300);
                });
                finishEditing();
            };

            const actions = document.createElement('div');
            actions.className = 'flex flex-wrap items-center gap-3';

            const submitBtn = document.createElement('button');
            submitBtn.type = 'button';
            submitBtn.className = 'min-h-[44px] rounded-lg bg-[#fffdf9] px-4 py-2.5 text-sm font-semibold text-maroon-900 shadow-sm transition-colors hover:bg-[#f4e9e8] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:cursor-wait disabled:opacity-60';
            submitBtn.textContent = reviewText.submitReport;
            submitBtn.onclick = () => submitReport(data, submitBtn);

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'min-h-[44px] text-sm font-medium text-white/80 transition-colors hover:text-white hover:underline';
            cancelBtn.textContent = reviewText.keepChatting;
            cancelBtn.onclick = () => card.closest('div.text-left').remove();

            actions.append(submitBtn, cancelBtn, editDetailsBtn);
            btnRow.appendChild(actions);
            card.appendChild(btnRow);

            row.appendChild(card);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }
        function showSuccessCard() {
            const row = document.createElement('div');
            row.className = 'text-left chat-success-enter';

            const card = document.createElement('div');
            card.className = 'border border-green-700 rounded-lg p-4 bg-green-50 dark:border-green-800 dark:bg-green-950 flex items-start gap-3';
            card.setAttribute('role', 'status');
            card.setAttribute('aria-live', 'polite');

            const icon = document.createElement('span');
            icon.className = 'chat-success-icon text-green-700 dark:text-green-300';
            icon.setAttribute('aria-hidden', 'true');
            icon.innerHTML = '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path class="chat-success-check" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';

            const content = document.createElement('div');
            const title = document.createElement('p');
            title.className = 'text-sm font-semibold text-green-800 dark:text-green-300';
            title.textContent = reviewText.submitted;

            const description = document.createElement('p');
            description.className = 'mt-1 text-sm text-gray-700 dark:text-gray-200';
            description.textContent = reviewText.success;

            const link = document.createElement('a');
            link.className = 'mt-2 inline-flex min-h-[44px] items-center font-semibold text-maroon-700 underline dark:text-[#c27872]';
            link.href = reviewText.reportsUrl;
            link.textContent = reviewText.viewReports;

            content.append(title, description, link);
            card.append(icon, content);
            row.appendChild(card);
            log.appendChild(row);
            log.scrollTop = log.scrollHeight;
        }

        async function submitReport(data, btn) {
            const originalLabel = btn.textContent;
            btn.disabled = true;
            btn.classList.add('chat-submit-loading');
            btn.textContent = reviewText.submitting;

            const formData = new FormData();
            formData.append('category_id', data.category_id);
            formData.append('description', data.description);
            formData.append('location_text', data.location);
            if (data.photo) formData.append('photo', data.photo);

            try {
                const response = await fetch("{{ route('reports.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: formData,
                });
                const landedOnReports = response.redirected
                    && new URL(response.url).pathname === new URL(reviewText.reportsUrl, window.location.origin).pathname;

                if (!response.ok || !landedOnReports) {
                    throw new Error(response.status === 419 ? 'expired' : 'submit');
                }

                btn.classList.remove('chat-submit-loading');
                btn.classList.add('chat-submit-complete');
                btn.textContent = reviewText.submitted;
                showSuccessCard();
            } catch (error) {
                btn.disabled = false;
                btn.classList.remove('chat-submit-loading', 'chat-submit-complete');
                btn.textContent = originalLabel;
                addMessage(error.message === 'expired' ? t.expired : !navigator.onLine ? t.offline : reviewText.submitError, 'bot');
            }
        }

        const t = {{ Js::from([
            'error' => __('Sorry, something went wrong. Please try again.'),
            'offline' => __("You're offline. Please check your connection and try again."),
            'expired' => __('Your session expired. Please refresh the page and try again.'),
            'submitError' => __('We could not submit your report. Please try again.'),
            'slow' => __('That took too long. Please try again.'),
            'rate' => __('You are sending messages too quickly. Please wait a moment.'),
            'retry' => __('Try again'),
            'call911' => __('Call 911'),
            'safeContinue' => __("I'm safe, continue my report"),
            'safeMessage' => __("I'm safe, and I would like to continue with a report."),
        ]) }};
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
            sendBtn.setAttribute('aria-label', busy ? sendButtonLabels.sending : sendButtonLabels.send);
            document.getElementById('chat-send-label').classList.toggle('hidden', busy);
            document.getElementById('chat-send-icon').classList.toggle('hidden', busy);
            document.getElementById('chat-send-spinner').classList.toggle('hidden', !busy);
            document.getElementById('chat-send-spinner').classList.toggle('inline-flex', busy);
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
            document.querySelectorAll('[data-emergency-actions]').forEach(el => el.remove());

            if (!isRetry) {
                addMessage(message, 'user');
                conversationHistory += 'Resident: ' + message + '\n';
            }
            input.value = '';
            resizeChatInput();
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
        input.addEventListener('input', resizeChatInput);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    </script>

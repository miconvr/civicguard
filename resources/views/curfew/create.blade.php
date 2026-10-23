<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Log Curfew Violation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="cg-card">

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('curfew.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="cf_minor_name" class="cg-label">{{ __("Minor's Name") }}</label>
                        <input id="cf_minor_name" type="text" name="minor_name" required value="{{ old('minor_name') }}" class="cg-input">
                        @error('minor_name') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_minor_age" class="cg-label">{{ __("Age") }}</label>
                        <input id="cf_minor_age" type="number" name="minor_age" value="{{ old('minor_age') }}" min="0" max="17" class="cg-input">
                        @error('minor_age') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_guardian_name" class="cg-label">{{ __("Guardian's Name") }}</label>
                        <input id="cf_guardian_name" type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="cg-input">
                    </div>

                    <div>
                        <label for="cf_guardian_contact" class="cg-label">{{ __("Guardian's Contact Number") }}</label>
                        <input id="cf_guardian_contact" type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" class="cg-input">
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input
                            type="checkbox"
                            name="guardian_notified"
                            value="1"
                            {{ old('guardian_notified') ? 'checked' : '' }}
                            class="rounded border-gray-400 text-maroon-700 dark:border-gray-600"
                        >
                        {{ __('Guardian notified') }}
                    </label>

                    <div>
                        <label for="cf_referral_action" class="cg-label">{{ __("Referral or Follow-up Action") }}</label>
                        <select id="cf_referral_action" name="referral_action" class="cg-input">
                            <option value="">{{ __("Select action") }}</option>
                            <option value="Released to guardian" {{ old('referral_action') === 'Released to guardian' ? 'selected' : '' }}>{{ __("Released to guardian") }}</option>
                            <option value="Referred to social-welfare personnel" {{ old('referral_action') === 'Referred to social-welfare personnel' ? 'selected' : '' }}>{{ __("Referred to social-welfare personnel") }}</option>
                            <option value="Referred to barangay official" {{ old('referral_action') === 'Referred to barangay official' ? 'selected' : '' }}>{{ __("Referred to barangay official") }}</option>
                            <option value="Other" {{ old('referral_action') === 'Other' ? 'selected' : '' }}>{{ __("Other") }}</option>
                        </select>
                        @error('referral_action') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __("Record safeguarding actions factually and follow current barangay and social-welfare procedures for minors.") }}</p>

                    <fieldset class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <legend class="px-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __("Apprehension Details") }}</legend>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">{{ __("Enter when the incident occurred.") }}</p>

                        <input id="cf_apprehension_datetime" type="hidden" name="apprehension_datetime" value="{{ old('apprehension_datetime') }}">

                        <div class="grid grid-cols-1 gap-4">
                            <div class="relative">
                                <label for="cf-date-display" class="cg-label">{{ __("Date") }}</label>
                                <button type="button" id="cf-date-display" class="cg-input flex w-full items-center justify-between gap-2 text-left" aria-haspopup="dialog" aria-expanded="false">
                                    <span id="cf-date-label">{{ __("Choose date") }}</span>
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                                </button>
                                <div id="cf-calendar" class="cg-calendar-panel absolute left-0 top-full z-30 mt-2 hidden w-full min-w-[270px] rounded-xl border border-gray-200 bg-white p-3 shadow-xl" role="dialog" aria-label="{{ __("Choose apprehension date") }}">
                                    <div class="mb-3 flex items-center justify-between gap-2">
                                        <button type="button" id="cf-prev-month" class="cg-calendar-nav" aria-label="Previous month">‹</button>
                                        <div id="cf-calendar-month" class="text-sm font-semibold text-gray-900"></div>
                                        <button type="button" id="cf-next-month" class="cg-calendar-nav" aria-label="Next month">›</button>
                                    </div>
                                    <div class="cg-calendar-grid cg-calendar-weekdays">
                                        <span>{{ __('Su') }}</span><span>{{ __('Mo') }}</span><span>{{ __('Tu') }}</span><span>{{ __('We') }}</span><span>{{ __('Th') }}</span><span>{{ __('Fr') }}</span><span>{{ __('Sa') }}</span>
                                    </div>
                                    <div id="cf-calendar-days" class="cg-calendar-grid mt-1"></div>
                                    <button type="button" id="cf-calendar-today" class="mt-3 min-h-11 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:border-[#6b1d35] hover:text-[#6b1d35]">{{ __("Today") }}</button>
                                </div>
                                <input id="cf-apprehension-date" type="hidden" value="{{ old('apprehension_datetime') ? substr(old('apprehension_datetime'), 0, 10) : '' }}">
                            </div>

                            <div>
                                <span class="cg-label">{{ __("Time") }}</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="cg-time-picker">
                                        <label class="cg-label" for="cf-hour-button">{{ __("Hour") }}</label>
                                        <button type="button" id="cf-hour-button" class="cg-time-button" aria-haspopup="listbox" aria-expanded="false">{{ __("Hour") }} <span>⌄</span></button>
                                        <div id="cf-hour-menu" class="cg-time-menu hidden" role="listbox" aria-label="{{ __("Choose hour") }}">
                                            @foreach(range(1, 12) as $hour)
                                                <button type="button" class="cg-time-option" role="option" data-value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="cg-time-picker">
                                        <label class="cg-label" for="cf-minute-button">{{ __("Minute") }}</label>
                                        <button type="button" id="cf-minute-button" class="cg-time-button" aria-haspopup="listbox" aria-expanded="false">{{ __("Min") }} <span>⌄</span></button>
                                        <div id="cf-minute-menu" class="cg-time-menu hidden" role="listbox" aria-label="{{ __("Choose minute") }}">
                                            @foreach(range(0, 59) as $minute)
                                                <button type="button" class="cg-time-option" role="option" data-value="{{ sprintf('%02d', $minute) }}">{{ sprintf('%02d', $minute) }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="cg-time-picker">
                                        <label class="cg-label" for="cf-period-button">{{ __("AM / PM") }}</label>
                                        <button type="button" id="cf-period-button" class="cg-time-button" aria-haspopup="listbox" aria-expanded="false">{{ __("AM/PM") }} <span>⌄</span></button>
                                        <div id="cf-period-menu" class="cg-time-menu hidden" role="listbox" aria-label="{{ __("Choose AM or PM") }}">
                                            <button type="button" class="cg-time-option" role="option" data-value="AM">AM</button>
                                            <button type="button" class="cg-time-option" role="option" data-value="PM">PM</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('apprehension_datetime') <p class="cg-error" role="alert">{{ $message }}</p> @enderror
                    </fieldset>

                    <script nonce="{{ Vite::cspNonce() }}">
                    (() => {
                        const hidden = document.getElementById('cf_apprehension_datetime');
                        const dateButton = document.getElementById('cf-date-display');
                        const dateLabel = document.getElementById('cf-date-label');
                        const dateValue = document.getElementById('cf-apprehension-date');
                        const calendar = document.getElementById('cf-calendar');
                        const monthLabel = document.getElementById('cf-calendar-month');
                        const daysBox = document.getElementById('cf-calendar-days');
                        const hourButton = document.getElementById('cf-hour-button');
                        const minuteButton = document.getElementById('cf-minute-button');
                        const periodButton = document.getElementById('cf-period-button');
                        const timePickers = [...document.querySelectorAll('.cg-time-picker')];

                        if (!hidden || !dateButton || !calendar) return;

                        const initial = hidden.value || '';
                        let selectedDate = initial.match(/^\d{4}-\d{2}-\d{2}/)?.[0] || '';
                        let viewDate = selectedDate ? new Date(selectedDate + 'T12:00:00') : new Date();
                        let chosenHour = '';
                        let chosenMinute = '';
                        let chosenPeriod = '';

                        if (initial.length >= 16) {
                            const match = initial.slice(11, 16).match(/^(\d{2}):(\d{2})$/);
                            if (match) {
                                let h = Number(match[1]);
                                chosenPeriod = h >= 12 ? 'PM' : 'AM';
                                h = h % 12 || 12;
                                chosenHour = String(h).padStart(2, '0');
                                chosenMinute = match[2];
                                setButtonValue(hourButton, chosenHour);
                                setButtonValue(minuteButton, chosenMinute);
                                setButtonValue(periodButton, chosenPeriod);
                            }
                        }

                        const formatDate = value => {
                            if (!value) return 'Choose date';
                            const d = new Date(value + 'T12:00:00');
                            const locale = document.documentElement.lang === 'tl' ? 'fil-PH' : 'en-US';
                            return d.toLocaleDateString(locale, { month: 'short', day: 'numeric', year: 'numeric' });
                        };

                        const sync = () => {
                            dateValue.value = selectedDate;
                            dateLabel.textContent = formatDate(selectedDate);
                            if (!selectedDate || !chosenHour || !chosenMinute || !chosenPeriod) {
                                hidden.value = '';
                                return;
                            }
                            let h = Number(chosenHour) % 12;
                            if (chosenPeriod === 'PM') h += 12;
                            hidden.value = `${selectedDate}T${String(h).padStart(2, '0')}:${chosenMinute}`;
                        };

                        const renderCalendar = () => {
                            const year = viewDate.getFullYear();
                            const month = viewDate.getMonth();
                            monthLabel.textContent = viewDate.toLocaleDateString(document.documentElement.lang === 'tl' ? 'fil-PH' : 'en-US', { month: 'long', year: 'numeric' });
                            daysBox.replaceChildren();

                            const firstDay = new Date(year, month, 1).getDay();
                            const daysInMonth = new Date(year, month + 1, 0).getDate();

                            for (let i = 0; i < firstDay; i++) {
                                const blank = document.createElement('span');
                                blank.className = 'cg-calendar-empty';
                                blank.setAttribute('aria-hidden', 'true');
                                daysBox.append(blank);
                            }

                            for (let day = 1; day <= daysInMonth; day++) {
                                const value = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.textContent = day;
                                button.className = 'cg-calendar-day' + (value === selectedDate ? ' is-selected' : '');
                                button.setAttribute('aria-label', formatDate(value));
                                if (value === selectedDate) button.setAttribute('aria-pressed', 'true');
                                button.addEventListener('click', () => {
                                    selectedDate = value;
                                    viewDate = new Date(value + 'T12:00:00');
                                    sync();
                                    renderCalendar();
                                    calendar.classList.add('hidden');
                                    dateButton.setAttribute('aria-expanded', 'false');
                                });
                                daysBox.append(button);
                            }
                        };

                        dateButton.addEventListener('click', () => {
                            const opening = calendar.classList.contains('hidden');
                            calendar.classList.toggle('hidden');
                            dateButton.setAttribute('aria-expanded', String(opening));
                            if (opening) renderCalendar();
                        });

                        document.getElementById('cf-prev-month').addEventListener('click', () => {
                            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, 1);
                            renderCalendar();
                        });
                        document.getElementById('cf-next-month').addEventListener('click', () => {
                            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 1);
                            renderCalendar();
                        });
                        document.getElementById('cf-calendar-today').addEventListener('click', () => {
                            const now = new Date();
                            selectedDate = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
                            viewDate = now;
                            sync();
                            renderCalendar();
                            calendar.classList.add('hidden');
                            dateButton.setAttribute('aria-expanded', 'false');
                        });

                        const setButtonValue = (button, value) => {
                            if (!button) return;
                            const label = button.childNodes[0];
                            if (label) label.textContent = value + ' ';
                            button.dataset.value = value;
                        };

                        const closeMenus = () => {
                            timePickers.forEach(picker => {
                                const menu = picker.querySelector('.cg-time-menu');
                                const button = picker.querySelector('.cg-time-button');
                                if (menu) menu.classList.add('hidden');
                                if (button) button.setAttribute('aria-expanded', 'false');
                            });
                        };

                        timePickers.forEach(picker => {
                            const button = picker.querySelector('.cg-time-button');
                            const menu = picker.querySelector('.cg-time-menu');
                            if (!button || !menu) return;

                            button.addEventListener('click', () => {
                                const shouldOpen = menu.classList.contains('hidden');
                                closeMenus();
                                if (shouldOpen) {
                                    menu.classList.remove('hidden');
                                    button.setAttribute('aria-expanded', 'true');
                                }
                            });

                            menu.querySelectorAll('.cg-time-option').forEach(option => {
                                option.addEventListener('click', () => {
                                    const value = option.dataset.value;
                                    setButtonValue(button, value);
                                    menu.querySelectorAll('.cg-time-option').forEach(item => {
                                        item.classList.toggle('is-selected', item === option);
                                        item.setAttribute('aria-selected', String(item === option));
                                    });

                                    if (button.id === 'cf-hour-button') chosenHour = value;
                                    if (button.id === 'cf-minute-button') chosenMinute = value;
                                    if (button.id === 'cf-period-button') chosenPeriod = value;

                                    closeMenus();
                                    sync();
                                });
                            });
                        });

                        document.addEventListener('click', event => {
                            if (!calendar.contains(event.target) && !dateButton.contains(event.target)) {
                                calendar.classList.add('hidden');
                                dateButton.setAttribute('aria-expanded', 'false');
                            }
                            if (!event.target.closest('.cg-time-picker')) closeMenus();
                        });

                        const form = hidden.closest('form');
                        if (form) {
                            form.addEventListener('submit', event => {
                                sync();
                                if (!selectedDate || !chosenHour || !chosenMinute || !chosenPeriod) {
                                    event.preventDefault();
                                    if (!selectedDate) {
                                        calendar.classList.remove('hidden');
                                        dateButton.setAttribute('aria-expanded', 'true');
                                        renderCalendar();
                                    } else {
                                        (!chosenHour ? hourButton : !chosenMinute ? minuteButton : periodButton).focus();
                                    }
                                }
                            });
                        }

                        if (selectedDate) viewDate = new Date(selectedDate + 'T12:00:00');
                        sync();
                        renderCalendar();
                    })();
                    </script>

                    <div>
                        <label for="cf_apprehension_location" class="cg-label">{{ __("Location") }}</label>
                        <input id="cf_apprehension_location" type="text" name="apprehension_location" required value="{{ old('apprehension_location') }}" class="cg-input">
                        @error('apprehension_location') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_prior_violations_count" class="cg-label">{{ __("Number of Prior Violations") }}</label>
                        <input id="cf_prior_violations_count" type="number" name="prior_violations_count" required value="{{ old('prior_violations_count', 0) }}" min="0" class="cg-input">
                        @error('prior_violations_count') <p class="cg-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cf_notes" class="cg-label">{{ __("Notes") }}</label>
                        <textarea id="cf_notes" name="notes" rows="3" class="cg-input">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="cg-btn w-full sm:w-auto" data-loading-btn data-loading-text="{{ __('Logging...') }}">{{ __('Log Violation') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

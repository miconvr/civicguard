import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Keep the browser's install prompt so the landing page can offer it later.
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window.__cgInstallPrompt = e;
    window.dispatchEvent(new Event('cg-install-ready'));
});

function initializeScrollReveals() {
    const elements = document.querySelectorAll('[data-reveal]');

    if (!elements.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.documentElement.classList.add('cg-motion-ready');

    const observer = new IntersectionObserver((entries, currentObserver) => {
        let order = 0;
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Cards that appear together fade in one after another.
                if (!getComputedStyle(entry.target).getPropertyValue('--cg-reveal-delay').trim()) {
                    entry.target.style.setProperty('--cg-reveal-delay', Math.min(order * 70, 350) + 'ms');
                }
                order += 1;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -32px 0px', threshold: 0.08 });

    elements.forEach(element => observer.observe(element));

    document.addEventListener('focusin', event => {
        const element = event.target.closest('[data-reveal]');
        if (element) {
            element.classList.add('cg-motion-immediate');
            element.classList.add('is-visible');
            observer.unobserve(element);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeScrollReveals, { once: true });
} else {
    initializeScrollReveals();
}

// cg-dashboard-motion: report totals count up when they scroll into view
function initializeCountUps() {
    const nodes = document.querySelectorAll('.cg-kpi');

    if (!nodes.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const run = (el) => {
        const raw = el.textContent.trim();
        if (!/^\d[\d,]*$/.test(raw)) return;

        const target = parseInt(raw.replace(/,/g, ''), 10);
        if (!Number.isFinite(target) || target < 2 || target > 100000) return;

        const withCommas = raw.includes(',');
        const duration = 900;
        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const value = Math.round(target * (1 - Math.pow(1 - progress, 3)));
            el.textContent = withCommas ? value.toLocaleString('en-US') : String(value);
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = raw;
            }
        };

        el.textContent = '0';
        requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                run(entry.target);
            }
        });
    }, { threshold: 0.4 });

    nodes.forEach(node => io.observe(node));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCountUps, { once: true });
} else {
    initializeCountUps();
}

Alpine.data('appShell', () => ({
    collapsed: (() => {
        try {
            return localStorage.getItem('sidebarCollapsed') === 'true';
        } catch (error) {
            return false;
        }
    })(),
    toggleSidebar() {
        this.collapsed = !this.collapsed;
        try {
            localStorage.setItem('sidebarCollapsed', this.collapsed);
        } catch (error) {
            return;
        }
    },
}));

Alpine.data('verificationTabs', (initial) => ({
    status: initial.status,
    description: initial.description,
    loading: false,
    requestController: null,
    requestVersion: 0,
    popstateHandler: null,

    init() {
        this.popstateHandler = () => this.load(window.location.href, false);
        window.addEventListener('popstate', this.popstateHandler);
    },

    destroy() {
        window.removeEventListener('popstate', this.popstateHandler);
        this.requestController?.abort();
    },

    navigate(event) {
        const link = event.target.closest('a[data-verification-tab], #verification-results nav a');
        if (!link || link.target || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        this.load(link.href, true);
    },

    async load(url, addHistoryEntry) {
        this.requestController?.abort();
        const controller = new AbortController();
        const version = ++this.requestVersion;
        this.requestController = controller;
        this.loading = true;

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
                window.location.assign(url);
                return;
            }

            const payload = await response.json();
            const results = this.$root.querySelector('#verification-results');
            if (!results) {
                window.location.assign(url);
                return;
            }

            Array.from(results.children).forEach(child => window.Alpine.destroyTree(child));
            results.innerHTML = payload.html;
            Array.from(results.children).forEach(child => window.Alpine.initTree(child));

            this.status = payload.status;
            this.description = payload.description;
            const statusInput = this.$root.querySelector('input[name="status"]');
            if (statusInput) statusInput.value = payload.status;

            if (addHistoryEntry) window.history.pushState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') window.location.assign(url);
        } finally {
            if (version === this.requestVersion) {
                this.loading = false;
                this.requestController = null;
            }
        }
    },
}));

Alpine.data('profilePhotoEditor', () => ({
    previewUrl: null,
    image: null,
    zoom: 1,
    processing: false,
    previewError: false,
    removeExisting: false,
    pendingPhotoChange: false,
    shakePreview: false,
    shakeTimer: null,

    triggerShake() {
        this.shakePreview = false;
        cancelAnimationFrame(this.shakeFrame);
        this.shakeFrame = requestAnimationFrame(() => { this.shakePreview = true; });
        clearTimeout(this.shakeTimer);
        this.shakeTimer = setTimeout(() => { this.shakePreview = false; }, 700);
    },

    select(event) {
        const file = event.target.files?.[0];
        this.previewError = false;
        this.image = null;

        if (this.previewUrl) {
            URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = null;
        }

        if (!file) {
            this.pendingPhotoChange = this.removeExisting;
            return;
        }

        this.removeExisting = false;
        this.pendingPhotoChange = true;
        this.triggerShake();
        this.previewUrl = URL.createObjectURL(file);
        this.zoom = 1;

        const image = new Image();
        image.onload = () => { this.image = image; };
        image.onerror = () => { this.previewError = true; };
        image.src = this.previewUrl;
    },

    clearSelection() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl = null;
        this.image = null;
        this.previewError = false;
        this.$refs.input.value = '';
        this.pendingPhotoChange = this.removeExisting;
        this.shakePreview = false;
    },

    markForRemoval() {
        this.removeExisting = true;
        this.pendingPhotoChange = true;
        this.triggerShake();
    },

    undoRemoval() {
        this.removeExisting = false;
        this.pendingPhotoChange = false;
        this.shakePreview = false;
    },

    destroy() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        clearTimeout(this.shakeTimer);
        cancelAnimationFrame(this.shakeFrame);
    },

    submit(event) {
        const form = event.target;

        if (!this.image) {
            form.submit();
            return;
        }

        this.processing = true;
        const canvas = document.createElement('canvas');
        const size = 512;
        const sourceSize = Math.min(this.image.naturalWidth, this.image.naturalHeight) / this.zoom;
        const sourceX = (this.image.naturalWidth - sourceSize) / 2;
        const sourceY = (this.image.naturalHeight - sourceSize) / 2;
        canvas.width = size;
        canvas.height = size;
        canvas.getContext('2d').drawImage(
            this.image,
            sourceX,
            sourceY,
            sourceSize,
            sourceSize,
            0,
            0,
            size,
            size,
        );

        canvas.toBlob((blob) => {
            if (!blob) {
                this.processing = false;
                this.previewError = true;
                return;
            }

            const croppedFile = new File([blob], 'profile-picture.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(croppedFile);
            this.$refs.input.files = transfer.files;
            form.submit();
        }, 'image/jpeg', 0.9);
    },
}));

Alpine.data('landingPage', () => ({
    language: (() => {
        try {
            return localStorage.getItem('landingLanguage') === 'tl' ? 'tl' : 'en';
        } catch (error) {
            return 'en';
        }
    })(),
    copy: {
        en: {
            headline: 'Report an issue in',
            headlineAccent: ' Barangay Maimpis.',
            introduction: 'Report an incident, then follow its progress here. CivicGuard connects residents with barangay staff.',
            startTitle: 'What would you like to do?',
            residentPathTitle: 'I live in Maimpis',
            residentPathDescription: 'Create a resident account to report an issue and follow updates.',
            residentAction: 'Create a resident account',
            existingAccount: 'Already have an account? Sign in',
            staffPathTitle: 'I work for the barangay',
            staffPathDescription: 'Sign in to review reports and share updates with residents.',
            staffAction: 'Staff sign in',
            dashboard: 'Go to your dashboard',
            howItWorks: 'See how it works',
            howHeading: 'What happens after you report?',
            howDescription: 'You can follow each report from your account. Here is what to expect.',
            reportStep: 'Create a resident account',
            reportDescription: 'Sign up to send a report and see its updates.',
            staffStep: 'Tell us what happened',
            staffDescription: 'Choose a category, describe the issue, and enter its location. Adding a photo is optional.',
            progressStep: 'Check the report status',
            progressDescription: 'Barangay staff review the report and post updates for you to follow.',
            emergencyTitle: 'Is someone in immediate danger?',
            emergency: 'Call 911 for emergencies. CivicGuard reports are not monitored in real time.',
            call911: 'Call 911',
            processIntro: 'For residents',
            footerBrand: 'CivicGuard · Barangay Maimpis · Incident and Curfew Reporting System',
            featKicker: 'Why CivicGuard',
            featHeading: 'Reporting made simple',
            feat1Title: 'Add a photo and location',
            feat1Text: 'Attach a photo and drop a pin so staff can find the problem.',
            feat2Title: 'Follow every update',
            feat2Text: 'See when your report is received, assigned, in progress, and resolved.',
            feat3Title: 'Get notified',
            feat3Text: 'Receive a notification when the status of your report changes.',
            feat4Title: 'English and Tagalog',
            feat4Text: 'Use the site in the language you are most comfortable with.',
            staffKicker: 'For barangay staff',
            staffHeading: 'One place for every report',
            staff1: 'Review reports sorted by urgency',
            staff2: 'Assign reports and post updates for residents',
            staff3: 'Log curfew violations (for tanods)',
            faqHeading: 'Common questions',
            faq1Q: 'Do I need an account to report?',
            faq1A: 'Yes. Create a resident account so you can send a report and follow its updates.',
            faq2Q: 'Can I use CivicGuard in an emergency?',
            faq2A: 'No. Call 911 first. CivicGuard reports are not monitored in real time.',
            faq3Q: 'What if my issue gets fixed?',
            faq3A: 'When a report is marked resolved, you can confirm it or reopen it within 7 days.',
            faq4Q: 'Is a photo required?',
            faq4A: 'No. A photo is optional, but it helps staff understand the issue.',
            ctaHeading: 'Ready to report an issue?',
            ctaText: 'Create an account and follow your report from start to finish.',
            sampleIntro: 'After you send a report, you can follow its progress like this.',
            sampleTitle: 'Your report',
            sampleSent: 'Example · sent just now',
            sampleFoot: 'You can check this anytime from your account.',
            sampleLabel: 'Sample report',
            sampleCaption: 'This is what following a report looks like.',
            tlReceived: 'Received',
            tlReceivedDesc: 'Your report is logged.',
            tlAssigned: 'Assigned',
            tlAssignedDesc: 'A staff member takes it on.',
            tlProgress: 'In progress',
            tlProgressDesc: 'Work on it is underway.',
            tlResolved: 'Resolved',
            tlResolvedDesc: 'You confirm whether it is fixed.',
            staffQuestion: 'Barangay staff? Sign in',
            devKicker: 'Developed by',
            navSignIn: 'Sign in',
            navCreateAccount: 'Create account',
            footerEmergency: 'For emergencies, call 911.',
            footerPrivacy: 'Privacy Policy',
            footerTerms: 'Terms of Use',
            installNav: 'Install app',
            installHint: 'Add to your home screen',
            installChipSub: 'Open it like an app. No store needed.',
            flow1Title: 'You spot a problem',
            flow1Desc: 'Something in the barangay that needs attention.',
            flow2Title: 'You send a report',
            flow2Desc: 'Add a photo and a location pin.',
            flow3Title: 'Received by tanods and officials',
            flow3Desc: 'Staff see it, sorted by urgency.',
            flow4Title: 'In progress',
            flow4Desc: 'A tanod is assigned and works on it.',
            flow5Title: 'Check if it is resolved',
            flow5Desc: 'Confirm it is fixed, or reopen it.',
            pill1: 'Problem',
            pill2: 'Sent',
            pill3: 'Received',
            pill4: 'In progress',
            pill5: 'Resolved?',
            installKicker: 'Install the app',
            installHeading: 'Put CivicGuard on your home screen',
            installIntro: 'No app store needed. It takes about 20 seconds.',
            installNow: 'Install now',
            tabIos: 'iPhone',
            tabAndroid: 'Android',
            iosStep1: 'Tap the three dots (⋯)',
            iosStep1d: 'It is next to the address bar at the bottom of Safari.',
            iosStep2: 'Tap Share',
            iosStep2d: 'It is in the menu that opens.',
            iosStep3: 'Choose "Add to Home Screen"',
            iosStep3d: 'Scroll down the list if you do not see it.',
            iosStep4: 'Keep "Open as Web App" on, then tap Add',
            iosStep4d: 'The CivicGuard icon appears on your home screen.',
            iosNote: 'Use Safari. If you can already see a Share button in the toolbar, tap it and skip step 1. Browsers inside Facebook or Messenger cannot add apps to the home screen.',
            andStep1: 'Tap the menu button',
            andStep1d: 'It is the three dots at the top right of Chrome.',
            andStep2: 'Choose "Install app"',
            andStep2d: 'If you see "Add to Home screen" instead, choose that.',
            andStep3: 'Tap Install',
            andStep3d: 'The CivicGuard icon appears on your home screen.',
            andNote: 'Use Chrome. Menu names can differ slightly between phones.',
            navigation: 'Main navigation',
            startLabel: 'Choose how to continue',
            homeLink: 'CivicGuard, Barangay Maimpis home',
        },
        tl: {
            headline: 'Mag-ulat ng insidente',
            headlineAccent: ' sa Maimpis.',
            introduction: 'Mag-ulat ng insidente at subaybayan dito ang pag-usad nito. Iniuugnay ng CivicGuard ang mga residente sa mga kawani ng barangay.',
            startTitle: 'Ano ang nais ninyong gawin?',
            residentPathTitle: 'Nakatira ako sa Maimpis',
            residentPathDescription: 'Gumawa ng account ng residente upang mag-ulat ng problema at subaybayan ang mga update.',
            residentAction: 'Gumawa ng account ng residente',
            existingAccount: 'May account na? Mag-sign in',
            staffPathTitle: 'Kawani ako ng barangay',
            staffPathDescription: 'Mag-sign in upang suriin ang mga ulat at magbahagi ng mga update sa mga residente.',
            staffAction: 'Pagpasok ng kawani',
            dashboard: 'Buksan ang dashboard',
            howItWorks: 'Tingnan ang mga hakbang',
            howHeading: 'Ano ang mangyayari pagkatapos mag-ulat?',
            howDescription: 'Maaari ninyong subaybayan ang bawat ulat sa inyong account. Narito ang mga susunod na hakbang.',
            reportStep: 'Gumawa ng account ng residente',
            reportDescription: 'Mag-sign up upang makapag-ulat at makita ang mga update.',
            staffStep: 'Ilarawan ang nangyari',
            staffDescription: 'Pumili ng kategorya, ilarawan ang problema, at ilagay ang lokasyon. Opsyonal ang pagdagdag ng larawan.',
            progressStep: 'Tingnan ang kalagayan ng ulat',
            progressDescription: 'Susuriin ng mga kawani ng barangay ang ulat at maglalagay ng mga update para masubaybayan ninyo.',
            emergencyTitle: 'May taong nasa agarang panganib?',
            emergency: 'Tumawag sa 911 kapag may emergency. Hindi agad nababantayan ang mga ulat sa CivicGuard.',
            call911: 'Tumawag sa 911',
            processIntro: 'Para sa mga residente',
            footerBrand: 'CivicGuard · Sistema ng pag-uulat ng insidente at curfew sa Barangay Maimpis',
            featKicker: 'Bakit CivicGuard',
            featHeading: 'Simple at malinaw ang pag-uulat',
            feat1Title: 'Magdagdag ng larawan at lokasyon',
            feat1Text: 'Mag-attach ng larawan at maglagay ng pin para madaling mahanap ng mga kawani ang problema.',
            feat2Title: 'Subaybayan ang bawat update',
            feat2Text: 'Makikita mo kung natanggap, naitalaga, isinasagawa, o naresolba na ang iyong ulat.',
            feat3Title: 'Makatanggap ng abiso',
            feat3Text: 'Aabisuhan ka kapag nagbago ang status ng iyong ulat.',
            feat4Title: 'Ingles at Tagalog',
            feat4Text: 'Gamitin ang site sa wikang mas komportable ka.',
            staffKicker: 'Para sa mga kawani ng barangay',
            staffHeading: 'Isang lugar para sa lahat ng ulat',
            staff1: 'Suriin ang mga ulat ayon sa pagkaapurahan',
            staff2: 'Magtalaga ng mga ulat at magbahagi ng update sa mga residente',
            staff3: 'Itala ang mga paglabag sa curfew (para sa mga tanod)',
            faqHeading: 'Mga madalas itanong',
            faq1Q: 'Kailangan ko ba ng account para mag-ulat?',
            faq1A: 'Oo. Gumawa ng account bilang residente para makapagpadala ng ulat at masubaybayan ang mga update nito.',
            faq2Q: 'Maaari ko bang gamitin ang CivicGuard sa emergency?',
            faq2A: 'Hindi. Tumawag muna sa 911. Hindi real-time na binabantayan ang mga ulat sa CivicGuard.',
            faq3Q: 'Paano kung naayos na ang isyu?',
            faq3A: 'Kapag minarkahang naresolba ang ulat, maaari mo itong kumpirmahin o muling buksan sa loob ng 7 araw.',
            faq4Q: 'Kailangan ba ng larawan?',
            faq4A: 'Hindi. Opsyonal ang larawan, pero nakakatulong ito sa mga kawani na maintindihan ang isyu.',
            ctaHeading: 'Handa ka na bang mag-ulat?',
            ctaText: 'Gumawa ng account at subaybayan ang iyong ulat mula simula hanggang katapusan.',
            sampleIntro: 'Pagkatapos mong magpadala ng ulat, makikita mo ang takbo nito ng ganito.',
            sampleTitle: 'Ang inyong ulat',
            sampleSent: 'Halimbawa · kapapadala lang',
            sampleFoot: 'Maaari mo itong tingnan anumang oras sa iyong account.',
            sampleLabel: 'Halimbawang ulat',
            sampleCaption: 'Ganito ang itsura ng pagsubaybay sa iyong ulat.',
            tlReceived: 'Natanggap',
            tlReceivedDesc: 'Naitala na ang iyong ulat.',
            tlAssigned: 'Naitalaga',
            tlAssignedDesc: 'May kawani nang nakatalaga rito.',
            tlProgress: 'Isinasagawa',
            tlProgressDesc: 'Ginagawan na ito ng aksyon.',
            tlResolved: 'Naresolba',
            tlResolvedDesc: 'Kukumpirmahin mo kung naayos na.',
            staffQuestion: 'Kawani ng barangay? Mag-sign in',
            devKicker: 'Binuo nina',
            navSignIn: 'Mag-sign in',
            navCreateAccount: 'Gumawa ng account',
            footerEmergency: 'Sa oras ng kagipitan, tumawag sa 911.',
            footerPrivacy: 'Patakaran sa Privacy',
            footerTerms: 'Mga Tuntunin ng Paggamit',
            installNav: 'I-install ang app',
            installHint: 'Idagdag sa home screen',
            installChipSub: 'Buksan na parang app. Hindi kailangan ng store.',
            flow1Title: 'May napansin kayong problema',
            flow1Desc: 'Anumang kailangang tutukan sa barangay.',
            flow2Title: 'Magpadala ng ulat',
            flow2Desc: 'Magdagdag ng larawan at pin ng lokasyon.',
            flow3Title: 'Natanggap ng mga tanod at opisyal',
            flow3Desc: 'Nakikita ito ng mga kawani ayon sa pagkaapurahan.',
            flow4Title: 'Isinasagawa',
            flow4Desc: 'May tanod na nakatalaga at kumikilos dito.',
            flow5Title: 'Tingnan kung naresolba na',
            flow5Desc: 'Kumpirmahin kung naayos na, o buksan muli.',
            pill1: 'Problema',
            pill2: 'Naipadala',
            pill3: 'Natanggap',
            pill4: 'Isinasagawa',
            pill5: 'Naresolba na?',
            installKicker: 'I-install ang app',
            installHeading: 'Ilagay ang CivicGuard sa inyong home screen',
            installIntro: 'Hindi kailangan ng app store. Mga 20 segundo lang.',
            installNow: 'I-install ngayon',
            tabIos: 'iPhone',
            tabAndroid: 'Android',
            iosStep1: 'Pindutin ang tatlong tuldok (⋯)',
            iosStep1d: 'Katabi ito ng address bar sa ibaba ng Safari.',
            iosStep2: 'Pindutin ang Share',
            iosStep2d: 'Nasa menu na bubukas ito.',
            iosStep3: 'Piliin ang "Add to Home Screen"',
            iosStep3d: 'Mag-scroll pababa sa listahan kung hindi ito makita.',
            iosStep4: 'Iwanang naka-on ang "Open as Web App", saka pindutin ang Add',
            iosStep4d: 'Lalabas ang icon ng CivicGuard sa inyong home screen.',
            iosNote: 'Gamitin ang Safari. Kung may Share button na sa toolbar, pindutin ito at laktawan ang unang hakbang. Hindi makapagdagdag ng app sa home screen ang mga browser sa loob ng Facebook o Messenger.',
            andStep1: 'Pindutin ang menu button',
            andStep1d: 'Ito ang tatlong tuldok sa kanang itaas ng Chrome.',
            andStep2: 'Piliin ang "Install app"',
            andStep2d: 'Kung "Add to Home screen" ang makita, iyon ang piliin.',
            andStep3: 'Pindutin ang Install',
            andStep3d: 'Lalabas ang icon ng CivicGuard sa inyong home screen.',
            andNote: 'Gamitin ang Chrome. Maaaring bahagyang magkaiba ang mga pangalan sa menu depende sa telepono.',
            navigation: 'Pangunahing nabigasyon',
            startLabel: 'Piliin kung paano magpatuloy',
            homeLink: 'CivicGuard, tahanan ng Barangay Maimpis',
        },
    },
    installed: false,
    canPrompt: false,
    guideTab: 'android',
    guideOn: true,
    init() {
        const ua = navigator.userAgent || '';
        const ios = /iPhone|iPad|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        this.guideTab = ios ? 'ios' : 'android';
        const mq = window.matchMedia('(display-mode: standalone)');
        const standalone = () => mq.matches || window.navigator.standalone === true;
        this.installed = standalone();
        if (mq.addEventListener) mq.addEventListener('change', () => { this.installed = standalone(); });
        window.addEventListener('appinstalled', () => { this.installed = true; });
        this.canPrompt = !!window.__cgInstallPrompt;
        window.addEventListener('cg-install-ready', () => { this.canPrompt = true; });
    },
    showGuide() {
        const el = document.getElementById('install-guide');
        if (!el) return;
        this.guideOn = false;
        this.$nextTick(() => { this.guideOn = true; });
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
    },
    async nativeInstall() {
        const evt = window.__cgInstallPrompt;
        if (!evt) return;
        try {
            evt.prompt();
            await evt.userChoice;
        } catch (error) {}
        window.__cgInstallPrompt = null;
        this.canPrompt = false;
    },
    text(key) {
        return this.copy[this.language][key];
    },
    setLanguage(language) {
        this.language = language;
        try {
            localStorage.setItem('landingLanguage', language);
        } catch (error) {
            return;
        }
    },
    toggleLanguage() {
        this.setLanguage(this.language === 'en' ? 'tl' : 'en');
    },
}));

Alpine.start();

// Scroll reveal for the landing page
(function () {
    const run = () => {
        const items = document.querySelectorAll('[data-cg-scroll]');
        if (!items.length) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (!('IntersectionObserver' in window)) return;
        items.forEach((el) => {
            const group = el.parentElement
                ? Array.from(el.parentElement.querySelectorAll(':scope > [data-cg-scroll]'))
                : [];
            el.style.setProperty('--cg-delay', Math.max(group.indexOf(el), 0) * 120 + 'ms');
            el.classList.add('cg-scroll');
        });
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
        items.forEach((el) => io.observe(el));
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();

// ---- Loading state for forms: buttons marked data-loading-btn ----
const CG_SPINNER = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';
function cgSetBusy(btn) {
    btn.dataset.originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.setAttribute('aria-busy', 'true');
    btn.innerHTML = '<span class="inline-flex items-center gap-2" role="status">' + CG_SPINNER + '<span></span></span>';
    btn.querySelector('span span').textContent = btn.dataset.loadingText || '...';
}
function cgResetBusy() {
    document.querySelectorAll('[data-loading-btn][aria-busy="true"]').forEach((btn) => {
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
    });
}
document.addEventListener('submit', (e) => {
    const btn = e.target.querySelector && e.target.querySelector('[data-loading-btn]');
    if (!btn || e.defaultPrevented) return;
    if (btn.disabled) { e.preventDefault(); return; }
    cgSetBusy(btn);
});
window.addEventListener('pageshow', cgResetBusy);

// ---- Keyboard: keep the active field in view ----
(function () {
    const skip = ['checkbox', 'radio', 'file', 'submit', 'button'];
    const isField = (el) => el && el.matches && el.matches('input,textarea,select') && !skip.includes(el.type);
    const reveal = (el) => el.scrollIntoView({ block: 'center', behavior: 'auto' });
    document.addEventListener('focusin', (e) => {
        if (isField(e.target)) setTimeout(() => reveal(e.target), 300);
    });
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            if (isField(document.activeElement)) reveal(document.activeElement);
        });
    }
})();

// ---- Offline banner and submit guard ----
(function () {
    const tl = (document.documentElement.lang || '').toLowerCase().startsWith('tl');
    const text = tl
        ? { msg: 'Walang koneksyon sa internet. Hindi maipapadala ang inyong mga pagbabago hanggang makakonekta kayo muli.', retry: 'Subukan muli', still: 'Offline pa rin' }
        : { msg: "You're offline. Your changes can't be sent until you reconnect.", retry: 'Retry', still: 'Still offline' };
    let banner = null;
    function build() {
        banner = document.createElement('div');
        banner.setAttribute('role', 'status');
        banner.style.cssText = 'position:fixed;left:0;right:0;top:0;z-index:9999;display:none;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;padding-top:calc(12px + env(safe-area-inset-top,0px));background:#7a1f1f;color:#fff;font-size:14px;';
        const msg = document.createElement('span');
        msg.textContent = text.msg;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = text.retry;
        btn.style.cssText = 'min-height:44px;padding:0 14px;border:1px solid #fff;border-radius:8px;background:transparent;color:#fff;font-weight:600;';
        btn.addEventListener('click', async () => {
            try {
                await fetch('/favicon.svg?cb=' + Date.now(), { cache: 'no-store' });
                hide();
            } catch (err) {
                btn.textContent = text.still;
                setTimeout(() => { btn.textContent = text.retry; }, 2000);
            }
        });
        banner.append(msg, btn);
        document.body.appendChild(banner);
    }
    function show() { if (!banner) build(); banner.style.display = 'flex'; }
    function hide() { if (banner) banner.style.display = 'none'; }
    window.addEventListener('offline', show);
    window.addEventListener('online', hide);
    document.addEventListener('DOMContentLoaded', () => { if (!navigator.onLine) show(); });
    // Capture phase: block submits while offline before other handlers (including the loading spinner) run.
    document.addEventListener('submit', (e) => {
        if (!navigator.onLine) {
            e.preventDefault();
            e.stopImmediatePropagation();
            show();
        }
    }, true);
})();

// ---- Service worker: friendly offline page for page loads only ----
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// cgInstallObserver: play the install-guide animation only while it is on screen
(function () {
    const run = () => {
        const el = document.getElementById('install-guide');
        if (!el) return;
        if (!('IntersectionObserver' in window)) { el.classList.add('is-playing'); return; }
        new IntersectionObserver((entries) => {
            entries.forEach((e) => el.classList.toggle('is-playing', e.isIntersecting));
        }, { threshold: 0.2 }).observe(el);
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();

// cgNavReveal: the public header hides over the landing hero, slides in after it, and slides out again when you return
(function () {
    const hero = document.querySelector('.cg-landing-hero');
    const header = document.querySelector('.cg-public-header');
    if (!hero || !header) return;
    const root = document.documentElement;
    root.classList.add('cg-nav-js');
    let ticking = false;
    const update = () => {
        ticking = false;
        root.classList.toggle('cg-nav-on', hero.getBoundingClientRect().bottom < window.innerHeight * 0.6);
    };
    const request = () => {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    };
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request);
    update();
})();

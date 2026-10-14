import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

function initializeScrollReveals() {
    const elements = document.querySelectorAll('[data-reveal]');

    if (!elements.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.documentElement.classList.add('cg-motion-ready');

    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
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
            footerBrand: 'CivicGuard · Barangay Maimpis Incident and Curfew Reporting System',
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
            sampleTitle: 'Broken streetlight',
            sampleSent: 'Sent today',
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
            sampleTitle: 'Sirang ilaw sa kalsada',
            sampleSent: 'Ipinadala ngayong araw',
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
            navigation: 'Pangunahing nabigasyon',
            startLabel: 'Piliin kung paano magpatuloy',
            homeLink: 'CivicGuard, tahanan ng Barangay Maimpis',
        },
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

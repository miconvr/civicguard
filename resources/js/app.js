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
